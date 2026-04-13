<?php

namespace App\Http\Middleware;

use App\Services\PlanService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTechnicianLimit
{
    public function __construct(private readonly PlanService $planService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->organization_id) {
            return $next($request);
        }

        $org = $user->organization;

        if ($this->planService->atTechnicianLimit($org)) {
            $limit = $this->planService->technicianLimit($org);
            $plan  = $this->planService->activePlan($org);

            if ($request->expectsJson() || $request->inertia()) {
                return back()->withErrors([
                    'technician_limit' => "Your {$plan} plan allows up to {$limit} technicians. Upgrade your plan to add more.",
                ]);
            }

            return back()->withErrors([
                'technician_limit' => "Your {$plan} plan allows up to {$limit} technicians. Upgrade your plan to add more.",
            ]);
        }

        return $next($request);
    }
}
