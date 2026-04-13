<?php

namespace App\Services;

use App\Models\Organization;

class PlanService
{
    const PLAN_STARTER = 'starter';
    const PLAN_GROWTH  = 'growth';
    const PLAN_PRO     = 'pro';

    const TRIAL_DAYS = 14;

    // Technician seat caps. null = unlimited.
    const TECHNICIAN_LIMITS = [
        self::PLAN_STARTER => 3,
        self::PLAN_GROWTH  => 10,
        self::PLAN_PRO     => null,
    ];

    // During trial everyone gets Growth features, unless they chose Pro.
    const TRIAL_PLAN_MAP = [
        self::PLAN_STARTER => self::PLAN_GROWTH,
        self::PLAN_GROWTH  => self::PLAN_GROWTH,
        self::PLAN_PRO     => self::PLAN_PRO,
    ];

    const PLANS = [
        self::PLAN_STARTER,
        self::PLAN_GROWTH,
        self::PLAN_PRO,
    ];

    /**
     * The plan whose feature set is currently active for this org.
     * During a trial, Starter orgs get Growth features.
     * After trial, they get their actual subscribed plan.
     */
    public function activePlan(Organization $org): string
    {
        $subscription = $org->activeSubscription();

        if (! $subscription) {
            // No subscription at all — treat as expired, return starter (most restrictive)
            return self::PLAN_STARTER;
        }

        if ($subscription->isTrialing()) {
            return self::TRIAL_PLAN_MAP[$org->plan] ?? self::PLAN_GROWTH;
        }

        return $org->plan;
    }

    /**
     * Max technicians allowed for the active feature plan.
     */
    public function technicianLimit(Organization $org): ?int
    {
        return self::TECHNICIAN_LIMITS[$this->activePlan($org)] ?? null;
    }

    /**
     * Whether the org has reached its technician cap.
     */
    public function atTechnicianLimit(Organization $org): bool
    {
        $limit = $this->technicianLimit($org);

        if ($limit === null) {
            return false;
        }

        $count = $org->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'technician'))
            ->count();

        return $count >= $limit;
    }

    /**
     * Current technician count for an org.
     */
    public function technicianCount(Organization $org): int
    {
        return $org->users()
            ->whereHas('roles', fn ($q) => $q->where('name', 'technician'))
            ->count();
    }

    /**
     * Whether a plan key is valid.
     */
    public function isValidPlan(string $plan): bool
    {
        return in_array($plan, self::PLANS, true);
    }

    /**
     * Stripe price ID env key for a given plan + interval.
     * e.g. STRIPE_PRICE_GROWTH_MONTHLY
     */
    public function stripePriceId(string $plan, string $interval): ?string
    {
        $key = 'STRIPE_PRICE_'.strtoupper($plan).'_'.strtoupper($interval);
        return env($key) ?: null;
    }

    /**
     * Human-readable plan label.
     */
    public function label(string $plan): string
    {
        return match ($plan) {
            self::PLAN_STARTER => 'Starter',
            self::PLAN_GROWTH  => 'Growth',
            self::PLAN_PRO     => 'Pro',
            default            => ucfirst($plan),
        };
    }

    /**
     * Monthly price in dollars for display.
     */
    public function monthlyPrice(string $plan): int
    {
        return match ($plan) {
            self::PLAN_STARTER => 79,
            self::PLAN_GROWTH  => 149,
            self::PLAN_PRO     => 249,
            default            => 0,
        };
    }

    /**
     * Annual price per month in dollars for display.
     */
    public function annualPrice(string $plan): int
    {
        return match ($plan) {
            self::PLAN_STARTER => 63,
            self::PLAN_GROWTH  => 119,
            self::PLAN_PRO     => 199,
            default            => 0,
        };
    }
}
