<?php

namespace App\Http\Controllers\Owner;

use App\Events\JobCreated;
use App\Events\JobStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreJobRequest;
use App\Http\Requests\Owner\UpdateJobRequest;
use App\Models\Customer;
use App\Models\Job;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\ResponseFactory;

class JobController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        $jobs = Job::where('organization_id', $orgId)
            ->with([
                'customer:id,first_name,last_name,email',
                'property:id,address_line1,city,state',
                'jobType:id,name,color',
                'assignedTechnician:id,name',
            ])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) =>
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                        );
                });
            })
            ->orderByDesc('scheduled_at')
            ->paginate(25)
            ->withQueryString();

        return inertia('Owner/Jobs/Index', [
            'jobs'     => $jobs,
            'filters'  => $request->only(['search', 'status']),
            'statuses' => Job::statuses(),
        ]);
    }

    public function show(Request $request, Job $job): Response|ResponseFactory
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);

        $job->load(['customer', 'property', 'jobType', 'assignedTechnician', 'invoice', 'messages']);

        return inertia('Owner/Jobs/Show', [
            'job'      => $job,
            'statuses' => Job::statuses(),
        ]);
    }

    public function create(Request $request): Response|ResponseFactory
    {
        $orgId = $request->user()->organization_id;

        return inertia('Owner/Jobs/Create', [
            'customers' => Customer::where('organization_id', $orgId)
                ->with('properties')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name']),
            'jobTypes'   => JobType::where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'color']),
            'technicians' => User::where('organization_id', $orgId)
                ->orderBy('name')
                ->get(['id', 'name']),
            'statuses'   => Job::statuses(),
            'preselect'  => $request->only(['customer_id', 'property_id']),
        ]);
    }

    public function store(StoreJobRequest $request): RedirectResponse
    {
        $job = Job::create([
            ...$request->validated(),
            'organization_id' => $request->user()->organization_id,
            'status'          => Job::STATUS_SCHEDULED,
        ]);

        JobCreated::dispatch($job);

        return redirect()->route('owner.jobs.show', $job)
            ->with('success', 'Job created successfully.');
    }

    public function edit(Request $request, Job $job): Response|ResponseFactory
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);

        $orgId = $request->user()->organization_id;

        return inertia('Owner/Jobs/Edit', [
            'job'        => $job,
            'customers'  => Customer::where('organization_id', $orgId)
                ->with('properties')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name']),
            'jobTypes'   => JobType::where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'color']),
            'technicians' => User::where('organization_id', $orgId)
                ->orderBy('name')
                ->get(['id', 'name']),
            'statuses'   => Job::statuses(),
        ]);
    }

    public function update(UpdateJobRequest $request, Job $job): RedirectResponse
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);

        $job->update($request->validated());

        return redirect()->route('owner.jobs.show', $job)
            ->with('success', 'Job updated successfully.');
    }

    public function destroy(Request $request, Job $job): RedirectResponse
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);

        $job->delete();

        return redirect()->route('owner.jobs.index')
            ->with('success', 'Job cancelled successfully.');
    }

    public function updateStatus(Request $request, Job $job): RedirectResponse
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);

        $request->validate([
            'status' => ['required', Rule::in(array_keys(Job::statuses()))],
        ]);

        $timestamps = match ($request->status) {
            Job::STATUS_IN_PROGRESS => ['started_at'    => now()],
            Job::STATUS_COMPLETED   => ['completed_at'  => now()],
            Job::STATUS_CANCELLED   => ['cancelled_at'  => now()],
            default                 => [],
        };

        $oldStatus = $job->status;
        $job->update(['status' => $request->status, ...$timestamps]);

        JobStatusChanged::dispatch($job->fresh(), $oldStatus, $request->status);

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function reschedule(Request $request, Job $job): RedirectResponse
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);

        $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        $job->update(['scheduled_at' => $request->scheduled_at]);

        return redirect()->back()->with('success', 'Job rescheduled.');
    }

    public function reassign(Request $request, Job $job): RedirectResponse
    {
        abort_unless($job->organization_id === $request->user()->organization_id, 403);

        $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $job->update(['assigned_to' => $request->assigned_to]);

        return redirect()->back()->with('success', 'Technician updated.');
    }
}
