<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class DashboardController extends Controller
{
    /**
     * @param  Request  $request
     *
     * @return Response|ResponseFactory
     */
    public function index(Request $request): Response|ResponseFactory
    {
        $user = $request->user();

        // TODO: Replace with real queries
        $stats = [
            'open_jobs'        => 12,
            'today_visits'     => 7,
            'overdue_jobs'     => 3,
            'unassigned_jobs'  => 5,
        ];

        return inertia('Owner/Dashboard', [
            'user'  => $user,
            'stats' => $stats,
        ]);
    }
}
