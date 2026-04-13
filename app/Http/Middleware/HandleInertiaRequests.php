<?php

namespace App\Http\Middleware;

use App\Services\PlanService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $subscription = null;
        $planData = null;

        if ($user && $user->organization_id) {
            $org = $user->organization;
            $activeSub = $org->activeSubscription();
            $planService = app(PlanService::class);

            if ($activeSub) {
                $subscription = [
                    'status'          => $activeSub->status,
                    'plan'            => $org->plan,
                    'active_plan'     => $planService->activePlan($org),
                    'is_trialing'     => $activeSub->isTrialing(),
                    'days_remaining'  => $activeSub->trialDaysRemaining(),
                    'trial_ends_at'   => $activeSub->trial_ends_at?->toIso8601String(),
                ];
            }

            $planData = [
                'current'       => $org->plan,
                'active'        => $planService->activePlan($org),
                'tech_limit'    => $planService->technicianLimit($org),
                'tech_count'    => $planService->technicianCount($org),
                'at_tech_limit' => $planService->atTechnicianLimit($org),
            ];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user'  => $user,
                'roles' => $user?->getRoleNames() ?? [],
            ],
            'subscription' => $subscription,
            'plan'         => $planData,
        ];
    }
}
