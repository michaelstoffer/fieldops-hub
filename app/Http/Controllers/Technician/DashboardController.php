<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class DashboardController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        $user = $request->user();

        $todayCount = Job::where('assigned_to', $user->id)
            ->whereDate('scheduled_at', today())
            ->whereNotIn('status', [Job::STATUS_CANCELLED])
            ->count();

        $inProgressCount = Job::where('assigned_to', $user->id)
            ->where('status', Job::STATUS_IN_PROGRESS)
            ->count();

        return inertia('Technician/Dashboard', [
            'stats' => [
                'today_jobs'  => $todayCount,
                'in_progress' => $inProgressCount,
            ],
        ]);
    }
}
