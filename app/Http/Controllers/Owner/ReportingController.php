<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Inertia\ResponseFactory;

class ReportingController extends Controller
{
    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        $jobsToday = Job::where('organization_id', $orgId)
            ->whereDate('scheduled_at', $today)
            ->whereNotIn('status', [Job::STATUS_CANCELLED])
            ->count();

        $revenueThisWeek = Invoice::where('organization_id', $orgId)
            ->where('status', Invoice::STATUS_PAID)
            ->whereBetween('paid_at', [$weekStart, $weekEnd])
            ->sum('total');

        $ar = Invoice::where('organization_id', $orgId)
            ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE])
            ->sum('balance_due');

        $overdueInvoices = Invoice::where('organization_id', $orgId)
            ->where('status', Invoice::STATUS_OVERDUE)
            ->count();

        $openJobs = Job::where('organization_id', $orgId)
            ->whereNotIn('status', [Job::STATUS_COMPLETED, Job::STATUS_CANCELLED])
            ->count();

        $unassignedJobs = Job::where('organization_id', $orgId)
            ->whereNull('assigned_to')
            ->whereNotIn('status', [Job::STATUS_COMPLETED, Job::STATUS_CANCELLED])
            ->count();

        return inertia('Owner/Dashboard', [
            'stats' => [
                'jobs_today'       => $jobsToday,
                'revenue_this_week' => (float) $revenueThisWeek,
                'accounts_receivable' => (float) $ar,
                'overdue_invoices'  => $overdueInvoices,
                'open_jobs'         => $openJobs,
                'unassigned_jobs'   => $unassignedJobs,
            ],
        ]);
    }

    // ─── Jobs by Type ─────────────────────────────────────────────────────────

    public function jobsByType(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $rows = Job::where('organization_id', $orgId)
            ->whereBetween('scheduled_at', [$from, $to])
            ->select('job_type_id', DB::raw('count(*) as total'), 'status')
            ->groupBy('job_type_id', 'status')
            ->with('jobType:id,name,color')
            ->get();

        // Pivot: keyed by job_type_id → { type, statuses:{}, total }
        $byType = [];
        foreach ($rows as $row) {
            $key = $row->job_type_id ?? 0;
            if (! isset($byType[$key])) {
                $byType[$key] = [
                    'type'     => $row->jobType ? ['id' => $row->jobType->id, 'name' => $row->jobType->name, 'color' => $row->jobType->color] : ['id' => 0, 'name' => 'Untyped', 'color' => '#94a3b8'],
                    'statuses' => [],
                    'total'    => 0,
                ];
            }
            $byType[$key]['statuses'][$row->status] = $row->total;
            $byType[$key]['total'] += $row->total;
        }

        usort($byType, fn ($a, $b) => $b['total'] <=> $a['total']);

        return inertia('Owner/Reports/JobsByType', [
            'rows'     => array_values($byType),
            'filters'  => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'statuses' => Job::statuses(),
        ]);
    }

    // ─── Job Profitability ────────────────────────────────────────────────────

    public function jobProfitability(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $typeId = $request->input('job_type_id');
        $techId = $request->input('technician_id');

        $jobs = Job::where('organization_id', $orgId)
            ->where('status', Job::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$from, $to])
            ->when($typeId, fn ($q) => $q->where('job_type_id', $typeId))
            ->when($techId, fn ($q) => $q->where('assigned_to', $techId))
            ->with(['jobType:id,name,color', 'assignedTechnician:id,name', 'invoice', 'lineItems'])
            ->get()
            ->map(function (Job $job) {
                $revenue = $job->invoice?->total ?? 0;
                $parts   = $job->lineItems->sum(fn ($li) => $li->unit_price * $li->quantity);
                $margin  = $revenue - $parts;
                $marginPct = $revenue > 0 ? round($margin / $revenue * 100, 1) : null;

                return [
                    'id'          => $job->id,
                    'title'       => $job->title,
                    'completed_at' => $job->completed_at?->toDateString(),
                    'job_type'    => $job->jobType ? ['id' => $job->jobType->id, 'name' => $job->jobType->name, 'color' => $job->jobType->color] : null,
                    'technician'  => $job->assignedTechnician ? ['id' => $job->assignedTechnician->id, 'name' => $job->assignedTechnician->name] : null,
                    'revenue'     => (float) $revenue,
                    'parts_cost'  => (float) $parts,
                    'margin'      => (float) $margin,
                    'margin_pct'  => $marginPct,
                ];
            });

        $jobTypes     = JobType::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);
        $technicians  = User::where('organization_id', $orgId)->role('technician')->orderBy('name')->get(['id', 'name']);

        return inertia('Owner/Reports/JobProfitability', [
            'jobs'        => $jobs,
            'job_types'   => $jobTypes,
            'technicians' => $technicians,
            'filters'     => [
                'from'          => $from->toDateString(),
                'to'            => $to->toDateString(),
                'job_type_id'   => $typeId,
                'technician_id' => $techId,
            ],
        ]);
    }

    // ─── Technician Performance ───────────────────────────────────────────────

    public function technicianPerformance(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $technicians = User::where('organization_id', $orgId)
            ->role('technician')
            ->with(['jobs' => function ($q) use ($orgId, $from, $to) {
                $q->where('organization_id', $orgId)
                  ->where('status', Job::STATUS_COMPLETED)
                  ->whereBetween('completed_at', [$from, $to])
                  ->with('invoice');
            }])
            ->get()
            ->map(function (User $tech) {
                $jobs = $tech->jobs;
                $completed = $jobs->count();

                $revenue = $jobs->sum(fn ($j) => $j->invoice?->total ?? 0);

                $durations = $jobs
                    ->filter(fn ($j) => $j->started_at && $j->completed_at)
                    ->map(fn ($j) => $j->completed_at->diffInMinutes($j->started_at));

                $avgDuration = $durations->count() > 0 ? round($durations->avg()) : null;

                return [
                    'id'           => $tech->id,
                    'name'         => $tech->name,
                    'jobs_completed' => $completed,
                    'revenue'      => (float) $revenue,
                    'avg_duration_minutes' => $avgDuration,
                ];
            })
            ->sortByDesc('jobs_completed')
            ->values();

        return inertia('Owner/Reports/TechnicianPerformance', [
            'technicians' => $technicians,
            'filters'     => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
        ]);
    }
}
