<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class CalendarController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        return inertia('Owner/Calendar');
    }

    /**
     * Returns jobs as FullCalendar-compatible events for a given date range.
     */
    public function events(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end'   => ['required', 'date'],
        ]);

        $orgId = $request->user()->organization_id;

        $jobs = Job::where('organization_id', $orgId)
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$request->start, $request->end])
            ->with(['customer', 'jobType'])
            ->get();

        $events = $jobs->map(fn (Job $job) => [
            'id'              => $job->id,
            'title'           => $job->customer
                ? "{$job->customer->last_name}: {$job->title}"
                : $job->title,
            'start'           => $job->scheduled_at->toIso8601String(),
            'end'             => $job->started_at && $job->completed_at
                ? $job->completed_at->toIso8601String()
                : null,
            'url'             => "/owner/jobs/{$job->id}",
            'backgroundColor' => $job->jobType?->color ?? '#6366f1',
            'borderColor'     => $job->jobType?->color ?? '#6366f1',
            'textColor'       => '#ffffff',
            'extendedProps'   => [
                'status'   => $job->status,
                'customer' => $job->customer?->full_name,
            ],
        ]);

        return response()->json($events);
    }
}
