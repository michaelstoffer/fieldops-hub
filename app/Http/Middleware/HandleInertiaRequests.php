<?php

namespace App\Http\Middleware;

use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            $org         = $user->organization;
            $orgId       = $org->id;
            $planService = app(PlanService::class);

            // Cache subscription for 5 minutes — invalidated on checkout/webhook
            $activeSub = Cache::remember(
                "org.{$orgId}.active_subscription",
                300,
                fn () => $org->activeSubscription()
            );

            // Cache active plan (depends on subscription state) for 5 minutes
            $activePlan = Cache::remember(
                "org.{$orgId}.active_plan",
                300,
                fn () => $planService->activePlan($org)
            );

            // Cache technician count for 5 minutes — invalidated on team changes
            $techCount = Cache::remember(
                "org.{$orgId}.tech_count",
                300,
                fn () => $planService->technicianCount($org)
            );

            $techLimit    = PlanService::TECHNICIAN_LIMITS[$activePlan] ?? null;
            $atTechLimit  = $techLimit !== null && $techCount >= $techLimit;

            if ($activeSub) {
                $subscription = [
                    'status'         => $activeSub->status,
                    'plan'           => $org->plan,
                    'active_plan'    => $activePlan,
                    'is_trialing'    => $activeSub->isTrialing(),
                    'days_remaining' => $activeSub->trialDaysRemaining(),
                    'trial_ends_at'  => $activeSub->trial_ends_at?->toIso8601String(),
                ];
            }

            $planData = [
                'current'       => $org->plan,
                'active'        => $activePlan,
                'tech_limit'    => $techLimit,
                'tech_count'    => $techCount,
                'at_tech_limit' => $atTechLimit,
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
