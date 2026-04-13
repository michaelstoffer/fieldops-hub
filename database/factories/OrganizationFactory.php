<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Services\PlanService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name'          => $name,
            'slug'          => Str::slug($name).'-'.Str::random(4),
            'timezone'      => fake()->randomElement([
                'America/New_York',
                'America/Chicago',
                'America/Denver',
                'America/Los_Angeles',
            ]),
            'plan'          => PlanService::PLAN_GROWTH,
            'trial_ends_at' => now()->addDays(14),
        ];
    }

    /**
     * By default every factory org gets an active trialing subscription so that
     * the CheckSubscription middleware does not block existing feature tests.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (\App\Models\Organization $org) {
            // Only create the default subscription when none exists yet.
            if ($org->subscriptions()->doesntExist()) {
                Subscription::create([
                    'organization_id' => $org->id,
                    'plan'            => $org->plan,
                    'status'          => Subscription::STATUS_TRIALING,
                    'billing_interval'=> 'monthly',
                    'trial_ends_at'   => $org->trial_ends_at ?? now()->addDays(14),
                ]);
            }
        });
    }

    public function onPlan(string $plan): static
    {
        return $this->state(['plan' => $plan]);
    }

    public function trialing(?int $days = 14): static
    {
        return $this->state(['trial_ends_at' => now()->addDays($days)]);
    }

    public function trialExpired(): static
    {
        return $this->state(['trial_ends_at' => now()->subDay()]);
    }

    public function subscribed(string $plan = PlanService::PLAN_GROWTH): static
    {
        return $this->state([
            'plan'          => $plan,
            'trial_ends_at' => null,
        ]);
    }

    /**
     * Create an org with no subscription record — useful for testing "unsubscribed" state.
     */
    public function withoutSubscription(): static
    {
        return $this->afterCreating(function (\App\Models\Organization $org) {
            $org->subscriptions()->delete();
        });
    }
}
