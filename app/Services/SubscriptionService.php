<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;
use Stripe\StripeClient;

class SubscriptionService
{
    public function __construct(
        private readonly PlanService $planService,
    ) {}

    private function stripe(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a local trial subscription record at registration.
     * No Stripe call yet — card not required for trial.
     */
    public function createTrial(Organization $org, string $plan): Subscription
    {
        $trialEndsAt = now()->addDays(PlanService::TRIAL_DAYS);

        // Update org with chosen plan and trial end date
        $org->update([
            'plan'           => $plan,
            'trial_ends_at'  => $trialEndsAt,
        ]);

        $sub = Subscription::create([
            'organization_id' => $org->id,
            'plan'            => $plan,
            'status'          => Subscription::STATUS_TRIALING,
            'billing_interval'=> 'monthly',
            'trial_ends_at'   => $trialEndsAt,
        ]);

        $this->flushOrgCache($org->id);

        return $sub;
    }

    /**
     * Create a Stripe Customer for the org (idempotent).
     */
    public function ensureStripeCustomer(Organization $org, string $email, string $name): string
    {
        if ($org->stripe_customer_id) {
            return $org->stripe_customer_id;
        }

        $customer = $this->stripe()->customers->create([
            'email'    => $email,
            'name'     => $name,
            'metadata' => ['organization_id' => $org->id],
        ]);

        $org->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }

    /**
     * Create a Stripe Checkout Session for subscribing.
     * Used after the trial — user picks plan + interval.
     */
    public function createCheckoutSession(
        Organization $org,
        string $plan,
        string $interval,
        string $successUrl,
        string $cancelUrl,
        string $ownerEmail,
        string $ownerName,
    ): string {
        $priceId = $this->planService->stripePriceId($plan, $interval);

        if (! $priceId) {
            throw new \RuntimeException("Stripe price ID not configured for plan={$plan} interval={$interval}. Run php artisan stripe:setup-products.");
        }

        $customerId = $this->ensureStripeCustomer($org, $ownerEmail, $ownerName);

        $params = [
            'customer'             => $customerId,
            'mode'                 => 'subscription',
            'line_items'           => [['price' => $priceId, 'quantity' => 1]],
            'success_url'          => $successUrl,
            'cancel_url'           => $cancelUrl,
            'allow_promotion_codes'=> true,
            'metadata'             => [
                'organization_id' => $org->id,
                'plan'            => $plan,
                'interval'        => $interval,
            ],
        ];

        // If still in trial, carry trial days over to Stripe
        $subscription = $org->activeSubscription();
        if ($subscription && $subscription->isTrialing()) {
            $params['subscription_data'] = [
                'trial_end' => $subscription->trial_ends_at->timestamp,
            ];
        }

        $session = $this->stripe()->checkout->sessions->create($params);

        return $session->url;
    }

    /**
     * Activate subscription after Stripe confirms payment.
     * Called from the webhook handler.
     */
    public function activateFromStripe(
        string $organizationId,
        string $stripeSubscriptionId,
        string $plan,
        string $interval,
        string $stripePriceId,
        int $periodStart,
        int $periodEnd,
    ): void {
        $org = Organization::find($organizationId);
        if (! $org) {
            return;
        }

        $org->update(['plan' => $plan]);

        // Cancel any existing local subscription records
        Subscription::where('organization_id', $org->id)
            ->whereIn('status', [Subscription::STATUS_TRIALING, Subscription::STATUS_ACTIVE])
            ->update(['status' => Subscription::STATUS_CANCELED, 'canceled_at' => now()]);

        Subscription::create([
            'organization_id'        => $org->id,
            'plan'                   => $plan,
            'status'                 => Subscription::STATUS_ACTIVE,
            'billing_interval'       => $interval,
            'stripe_subscription_id' => $stripeSubscriptionId,
            'stripe_price_id'        => $stripePriceId,
            'current_period_start'   => \Carbon\Carbon::createFromTimestamp($periodStart),
            'current_period_end'     => \Carbon\Carbon::createFromTimestamp($periodEnd),
        ]);

        $this->flushOrgCache($org->id);
    }

    /**
     * Handle Stripe subscription updated (renewal, plan change, etc).
     */
    public function updateFromStripe(object $stripeSubscription): void
    {
        $sub = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if (! $sub) {
            return;
        }

        $sub->update([
            'status'               => $stripeSubscription->status,
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end'   => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
        ]);

        $this->flushOrgCache($sub->organization_id);
    }

    /**
     * Handle Stripe subscription deletion.
     */
    public function cancelFromStripe(object $stripeSubscription): void
    {
        $sub = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if (! $sub) {
            return;
        }

        $sub->update([
            'status'      => Subscription::STATUS_CANCELED,
            'canceled_at' => now(),
        ]);

        $this->flushOrgCache($sub->organization_id);
    }

    /**
     * Flush all org-level Inertia shared-prop caches.
     * Call whenever subscription or team membership changes.
     */
    public function flushOrgCache(int $orgId): void
    {
        Cache::forget("org.{$orgId}.active_subscription");
        Cache::forget("org.{$orgId}.active_plan");
        Cache::forget("org.{$orgId}.tech_count");
    }
}
