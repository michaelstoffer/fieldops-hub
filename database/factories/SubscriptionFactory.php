<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Subscription;
use App\Services\PlanService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'organization_id'  => Organization::factory(),
            'plan'             => PlanService::PLAN_GROWTH,
            'status'           => Subscription::STATUS_TRIALING,
            'billing_interval' => 'monthly',
            'trial_ends_at'    => now()->addDays(14),
        ];
    }

    /**
     * Create a trialing subscription for the given org.
     */
    public function trialing(Organization $org, string $plan = PlanService::PLAN_GROWTH): static
    {
        return $this->state([
            'organization_id' => $org->id,
            'plan'            => $plan,
            'status'          => Subscription::STATUS_TRIALING,
            'trial_ends_at'   => $org->trial_ends_at ?? now()->addDays(14),
        ]);
    }

    /**
     * Create an active (paid) subscription for the given org.
     */
    public function active(Organization $org, string $plan = PlanService::PLAN_GROWTH): static
    {
        return $this->state([
            'organization_id'        => $org->id,
            'plan'                   => $plan,
            'status'                 => Subscription::STATUS_ACTIVE,
            'trial_ends_at'          => null,
            'stripe_subscription_id' => 'sub_test_'.fake()->regexify('[A-Za-z0-9]{20}'),
            'current_period_start'   => now()->subMonth(),
            'current_period_end'     => now()->addMonth(),
        ]);
    }

    /**
     * Create a canceled subscription.
     */
    public function canceled(Organization $org): static
    {
        return $this->state([
            'organization_id' => $org->id,
            'status'          => Subscription::STATUS_CANCELED,
            'canceled_at'     => now()->subDay(),
        ]);
    }

    /**
     * Create a subscription whose trial has already expired.
     */
    public function trialExpired(Organization $org, string $plan = PlanService::PLAN_GROWTH): static
    {
        return $this->state([
            'organization_id' => $org->id,
            'plan'            => $plan,
            'status'          => Subscription::STATUS_TRIALING,
            'trial_ends_at'   => now()->subDay(),
        ]);
    }
}
