<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\ResponseFactory;

class JobController extends Controller
{
    // -------------------------------------------------------------------------
    // Inertia pages
    // -------------------------------------------------------------------------

    public function index(Request $request): Response|ResponseFactory
    {
        $jobs = $this->todayQuery($request->user()->id)
            ->with(['customer', 'property', 'jobType'])
            ->orderBy('scheduled_at')
            ->get();

        return inertia('Technician/Jobs/Index', [
            'jobs'     => $jobs,
            'statuses' => Job::statuses(),
        ]);
    }

    public function show(Request $request, Job $job): Response|ResponseFactory
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $job->load(['customer', 'property', 'jobType']);

        return inertia('Technician/Jobs/Show', [
            'job'      => $job,
            'statuses' => Job::statuses(),
        ]);
    }

    // -------------------------------------------------------------------------
    // JSON API endpoints (used by PWA / service worker cache)
    // -------------------------------------------------------------------------

    public function today(Request $request): JsonResponse
    {
        $jobs = $this->todayQuery($request->user()->id)
            ->with(['customer', 'property', 'jobType'])
            ->orderBy('scheduled_at')
            ->get();

        return response()->json(['data' => $jobs]);
    }

    public function apiShow(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $job->load(['customer', 'property', 'jobType']);

        return response()->json(['data' => $job]);
    }

    public function updateStatus(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $allowedStatuses = array_diff(
            array_keys(Job::statuses()),
            [Job::STATUS_CANCELLED]
        );

        $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
        ]);

        $timestamps = match ($request->status) {
            Job::STATUS_IN_PROGRESS => ['started_at'   => now()],
            Job::STATUS_COMPLETED   => ['completed_at' => now()],
            default                 => [],
        };

        $job->update(['status' => $request->status, ...$timestamps]);

        return response()->json(['status' => 'ok', 'data' => $job->fresh()]);
    }

    public function updateNotes(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $request->validate([
            'technician_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $job->update(['technician_notes' => $request->technician_notes]);

        return response()->json(['status' => 'ok', 'data' => $job->fresh()]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function todayQuery(int $userId)
    {
        return Job::where('assigned_to', $userId)
            ->whereDate('scheduled_at', today())
            ->whereNotIn('status', [Job::STATUS_CANCELLED]);
    }
}
