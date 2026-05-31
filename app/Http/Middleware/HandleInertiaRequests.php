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

            // Single cache key — consolidates 3 round-trips into 1
            $orgData = Cache::remember(
                "org.{$orgId}.inertia_share",
                300,
                function () use ($org, $planService) {
                    $activeSub  = $org->activeSubscription();
                    $activePlan = $planService->activePlan($org);
                    $techCount  = $planService->technicianCount($org);
                    return compact('activeSub', 'activePlan', 'techCount');
                }
            );

            $activeSub  = $orgData['activeSub'];
            $activePlan = $orgData['activePlan'];
            $techCount  = $orgData['techCount'];

            $techLimit   = PlanService::TECHNICIAN_LIMITS[$activePlan] ?? null;
            $atTechLimit = $techLimit !== null && $techCount >= $techLimit;

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
            'flash' => [
                'success' => $request->session()->get('success'),
                'warning' => $request->session()->get('warning'),
                'error'   => $request->session()->get('error'),
            ],
        ];
    }
}
