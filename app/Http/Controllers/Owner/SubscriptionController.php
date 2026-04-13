<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly SubscriptionService $subscriptionService,
    ) {}

    /**
     * Subscription management page — shows current plan, trial status, and upgrade options.
     */
    public function index(Request $request): Response
    {
        $org          = $request->user()->organization;
        $subscription = $org->activeSubscription();

        return Inertia::render('Owner/Subscription/Index', [
            'subscription'   => $subscription ? [
                'plan'              => $subscription->plan,
                'status'            => $subscription->status,
                'billing_interval'  => $subscription->billing_interval,
                'trial_ends_at'     => $subscription->trial_ends_at?->toIso8601String(),
                'current_period_end'=> $subscription->current_period_end?->toIso8601String(),
                'days_remaining'    => $subscription->trialDaysRemaining(),
                'is_trialing'       => $subscription->isTrialing(),
            ] : null,
            'current_plan'   => $org->plan,
            'active_plan'    => $this->planService->activePlan($org),
            'plans'          => $this->planData(),
        ]);
    }

    /**
     * Expired trial page — shown when trial has ended without subscribing.
     */
    public function expired(Request $request): Response
    {
        $org = $request->user()->organization;

        return Inertia::render('Owner/Subscription/Expired', [
            'current_plan' => $org->plan,
            'plans'        => $this->planData(),
        ]);
    }

    /**
     * Create a Stripe Checkout Session and redirect the user to Stripe.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $request->validate([
            'plan'     => ['required', 'in:starter,growth,pro'],
            'interval' => ['required', 'in:monthly,annual'],
        ]);

        $org  = $request->user()->organization;
        $user = $request->user();

        $checkoutUrl = $this->subscriptionService->createCheckoutSession(
            org: $org,
            plan: $request->plan,
            interval: $request->interval,
            successUrl: route('owner.subscription.success').'?session_id={CHECKOUT_SESSION_ID}',
            cancelUrl: route('owner.subscription.index'),
            ownerEmail: $user->email,
            ownerName: $user->name,
        );

        return redirect($checkoutUrl);
    }

    /**
     * Stripe redirects here after a successful checkout.
     */
    public function success(Request $request): Response
    {
        return Inertia::render('Owner/Subscription/Success');
    }

    private function planData(): array
    {
        $plans = [];
        foreach (PlanService::PLANS as $key) {
            $plans[] = [
                'key'          => $key,
                'label'        => $this->planService->label($key),
                'monthly'      => $this->planService->monthlyPrice($key),
                'annual'       => $this->planService->annualPrice($key),
                'tech_limit'   => PlanService::TECHNICIAN_LIMITS[$key],
            ];
        }
        return $plans;
    }
}
