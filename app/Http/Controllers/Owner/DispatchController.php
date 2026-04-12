<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\DriverLocation;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;
use Spatie\Permission\Models\Role;

class DispatchController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $technicians = User::role('technician')
            ->where('organization_id', $orgId)
            ->get(['id', 'name']);

        return inertia('Owner/Dispatch/Map', [
            'technicians' => $technicians,
        ]);
    }

    /**
     * JSON endpoint: latest location + current job for each technician in the org.
     */
    public function technicianLocations(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id;

        $technicians = User::role('technician')
            ->where('organization_id', $orgId)
            ->get(['id', 'name']);

        $data = $technicians->map(function (User $tech) {
            $location = DriverLocation::where('user_id', $tech->id)
                ->latest('recorded_at')
                ->first(['latitude', 'longitude', 'heading', 'speed', 'recorded_at']);

            $currentJob = Job::where('assigned_to', $tech->id)
                ->whereIn('status', [Job::STATUS_EN_ROUTE, Job::STATUS_IN_PROGRESS])
                ->with(['customer', 'property'])
                ->latest('scheduled_at')
                ->first();

            $upcomingJobs = Job::where('assigned_to', $tech->id)
                ->where('status', Job::STATUS_SCHEDULED)
                ->whereDate('scheduled_at', today())
                ->with(['customer', 'property'])
                ->orderBy('scheduled_at')
                ->limit(3)
                ->get();

            return [
                'id'   => $tech->id,
                'name' => $tech->name,
                'location' => $location ? [
                    'latitude'    => (float) $location->latitude,
                    'longitude'   => (float) $location->longitude,
                    'heading'     => $location->heading !== null ? (float) $location->heading : null,
                    'recorded_at' => $location->recorded_at->toISOString(),
                ] : null,
                'current_job'   => $currentJob ? $this->formatJob($currentJob) : null,
                'upcoming_jobs' => $upcomingJobs->map(fn ($j) => $this->formatJob($j))->values(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * JSON endpoint: today's trail (location history) for a technician.
     */
    public function technicianTrail(Request $request, User $user): JsonResponse
    {
        abort_unless($user->organization_id === $request->user()->organization_id, 403);

        $points = DriverLocation::where('user_id', $user->id)
            ->whereDate('recorded_at', today())
            ->orderBy('recorded_at')
            ->get(['latitude', 'longitude', 'recorded_at'])
            ->map(fn ($p) => [
                'lat' => (float) $p->latitude,
                'lng' => (float) $p->longitude,
            ]);

        return response()->json(['data' => $points]);
    }

    private function formatJob(Job $job): array
    {
        return [
            'id'     => $job->id,
            'title'  => $job->title,
            'status' => $job->status,
            'scheduled_at' => $job->scheduled_at?->toISOString(),
            'customer' => $job->customer
                ? "{$job->customer->first_name} {$job->customer->last_name}"
                : null,
            'address' => $job->property
                ? "{$job->property->address_line1}, {$job->property->city}"
                : null,
        ];
    }
}
