<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

class HealthController extends Controller
{
    /**
     * Basic liveness probe — returns 200 if the app is running.
     * Used by load balancers to determine if the instance should receive traffic.
     */
    public function liveness(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }

    /**
     * Readiness probe — checks critical dependencies.
     * Used by orchestrators (ECS, k8s) to decide if the instance is ready.
     */
    public function readiness(): JsonResponse
    {
        $checks = [];
        $healthy = true;

        // Database check
        try {
            DB::select('SELECT 1');
            $checks['database'] = 'ok';
        } catch (Throwable $e) {
            $checks['database'] = 'fail: ' . $e->getMessage();
            $healthy = false;
        }

        // Queue check — verify the jobs table is accessible
        try {
            DB::table('jobs')->count();
            $checks['queue'] = 'ok';
        } catch (Throwable $e) {
            $checks['queue'] = 'fail: ' . $e->getMessage();
            $healthy = false;
        }

        // Cache check
        try {
            cache()->put('health_check', true, 5);
            $checks['cache'] = cache()->get('health_check') === true ? 'ok' : 'fail: read mismatch';
            if ($checks['cache'] !== 'ok') {
                $healthy = false;
            }
        } catch (Throwable $e) {
            $checks['cache'] = 'fail: ' . $e->getMessage();
            $healthy = false;
        }

        $status = $healthy ? 200 : 503;

        return response()->json([
            'status' => $healthy ? 'ok' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $status);
    }
}
