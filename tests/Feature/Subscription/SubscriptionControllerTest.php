<?php

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

/**
 * Helper: owner + active trialing subscription.
 */
function ownerOnTrial(string $plan = 'growth'): array
{
    $org  = Organization::factory()->trialing()->create(['plan' => $plan]);
    $user = User::factory()->owner($org)->create();
    Subscription::factory()->trialing($org, $plan)->create();

    return [$user, $org];
}

// ── index ──────────────────────────────────────────────────────────────────────

test('subscription index renders for owner', function () {
    [$owner] = ownerOnTrial();

    $this->actingAs($owner)
        ->get(route('owner.subscription.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Subscription/Index'));
});

test('subscription index passes subscription data to view', function () {
    [$owner] = ownerOnTrial('growth');

    $this->actingAs($owner)
        ->get(route('owner.subscription.index'))
        ->assertInertia(fn ($page) => $page
            ->has('subscription')
            ->where('subscription.plan', 'growth')
            ->where('subscription.status', Subscription::STATUS_TRIALING)
            ->where('subscription.is_trialing', true)
            ->has('plans')
            ->has('current_plan')
        );
});

test('subscription index passes null subscription when none exists', function () {
    $org  = Organization::factory()->withoutSubscription()->create(['plan' => 'growth']);
    $user = User::factory()->owner($org)->create();

    $this->actingAs($user)
        ->get(route('owner.subscription.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('subscription', null));
});

// ── expired ────────────────────────────────────────────────────────────────────

test('expired page renders for owner with expired trial', function () {
    $org  = Organization::factory()->trialExpired()->create(['plan' => 'growth']);
    $user = User::factory()->owner($org)->create();
    Subscription::factory()->trialExpired($org)->create();

    $this->actingAs($user)
        ->get(route('owner.subscription.expired'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Subscription/Expired'));
});

test('expired page passes plan data to view', function () {
    $org  = Organization::factory()->trialExpired()->create(['plan' => 'starter']);
    $user = User::factory()->owner($org)->create();

    $this->actingAs($user)
        ->get(route('owner.subscription.expired'))
        ->assertInertia(fn ($page) => $page
            ->has('plans')
            ->where('current_plan', 'starter')
        );
});

// ── success ────────────────────────────────────────────────────────────────────

test('success page renders for authenticated owner', function () {
    [$owner] = ownerOnTrial();

    $this->actingAs($owner)
        ->get(route('owner.subscription.success'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Subscription/Success'));
});

// ── checkout ───────────────────────────────────────────────────────────────────

test('checkout validates plan field', function () {
    [$owner] = ownerOnTrial();

    $this->actingAs($owner)
        ->post(route('owner.subscription.checkout'), [
            'plan'     => 'invalid_plan',
            'interval' => 'monthly',
        ])
        ->assertSessionHasErrors('plan');
});

test('checkout validates interval field', function () {
    [$owner] = ownerOnTrial();

    $this->actingAs($owner)
        ->post(route('owner.subscription.checkout'), [
            'plan'     => 'growth',
            'interval' => 'weekly',
        ])
        ->assertSessionHasErrors('interval');
});

test('checkout requires both plan and interval', function () {
    [$owner] = ownerOnTrial();

    $this->actingAs($owner)
        ->post(route('owner.subscription.checkout'), [])
        ->assertSessionHasErrors(['plan', 'interval']);
});

test('checkout redirects to stripe url when valid', function () {
    [$owner, $org] = ownerOnTrial();

    // Mock the SubscriptionService to return a fake Stripe URL
    $mock = Mockery::mock(SubscriptionService::class);
    $mock->shouldReceive('createCheckoutSession')->once()->andReturn('https://checkout.stripe.com/pay/fake');
    $this->app->instance(SubscriptionService::class, $mock);

    $this->actingAs($owner)
        ->post(route('owner.subscription.checkout'), [
            'plan'     => 'growth',
            'interval' => 'monthly',
        ])
        ->assertRedirect('https://checkout.stripe.com/pay/fake');
});

// ── access control ─────────────────────────────────────────────────────────────

test('unauthenticated users cannot access subscription index', function () {
    $this->get(route('owner.subscription.index'))
        ->assertRedirect(route('login'));
});

test('technician cannot access subscription index', function () {
    $org  = Organization::factory()->trialing()->create();
    $user = User::factory()->technician($org)->create();

    $this->actingAs($user)
        ->get(route('owner.subscription.index'))
        ->assertForbidden();
});
