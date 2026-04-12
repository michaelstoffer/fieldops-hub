<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\JobLineItem;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function reportingSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$user, $org, $customer];
}

function seedRoles(): void
{
    (new RolesAndPermissionsSeeder)->run();
}

// ── Authentication ─────────────────────────────────────────────────────────────

test('owner dashboard requires authentication', function () {
    $this->get('/owner/dashboard')->assertRedirect('/login');
});

test('jobs-by-type report requires authentication', function () {
    $this->get('/owner/reports/jobs-by-type')->assertRedirect('/login');
});

test('job-profitability report requires authentication', function () {
    $this->get('/owner/reports/job-profitability')->assertRedirect('/login');
});

test('technician-performance report requires authentication', function () {
    $this->get('/owner/reports/technician-performance')->assertRedirect('/login');
});

// ── Owner Dashboard ────────────────────────────────────────────────────────────

test('user can view the owner dashboard', function () {
    [$user] = reportingSetup();

    $this->actingAs($user)
        ->get('/owner/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Dashboard')->has('stats'));
});

test('dashboard stats include expected keys', function () {
    [$user] = reportingSetup();

    $this->actingAs($user)
        ->get('/owner/dashboard')
        ->assertInertia(fn ($page) => $page
            ->has('stats.jobs_today')
            ->has('stats.revenue_this_week')
            ->has('stats.accounts_receivable')
            ->has('stats.overdue_invoices')
            ->has('stats.open_jobs')
            ->has('stats.unassigned_jobs')
        );
});

test('dashboard jobs_today count is scoped to the organization', function () {
    [$user, $org, $customer]   = reportingSetup();
    [, , $otherCustomer]       = reportingSetup();

    // Own org: 2 jobs scheduled today
    Job::factory()->forCustomer($customer)->scheduled()->count(2)->create(['scheduled_at' => today()]);
    // Other org: 5 jobs today — must not appear
    Job::factory()->forCustomer($otherCustomer)->scheduled()->count(5)->create(['scheduled_at' => today()]);

    $this->actingAs($user)
        ->get('/owner/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.jobs_today', 2));
});

test('dashboard revenue_this_week sums only paid invoices for the current week', function () {
    [$user, $org, $customer] = reportingSetup();

    // Paid this week — should be included
    Invoice::factory()->forCustomer($customer)->create([
        'status'  => Invoice::STATUS_PAID,
        'total'   => 500,
        'paid_at' => now()->startOfWeek()->addDay(),
    ]);
    // Paid last week — should not be included
    Invoice::factory()->forCustomer($customer)->create([
        'status'  => Invoice::STATUS_PAID,
        'total'   => 999,
        'paid_at' => now()->subWeek(),
    ]);
    // Draft — should not be included
    Invoice::factory()->forCustomer($customer)->draft()->create(['total' => 200]);

    $this->actingAs($user)
        ->get('/owner/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.revenue_this_week', fn ($v) => (float) $v === 500.0));
});

test('dashboard accounts_receivable sums sent, partial, and overdue invoices', function () {
    [$user, $org, $customer] = reportingSetup();

    Invoice::factory()->forCustomer($customer)->create(['status' => Invoice::STATUS_SENT, 'balance_due' => 100]);
    Invoice::factory()->forCustomer($customer)->create(['status' => Invoice::STATUS_PARTIAL, 'balance_due' => 50]);
    Invoice::factory()->forCustomer($customer)->create(['status' => Invoice::STATUS_OVERDUE, 'balance_due' => 75]);
    Invoice::factory()->forCustomer($customer)->draft()->create(['balance_due' => 999]);  // excluded

    $this->actingAs($user)
        ->get('/owner/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.accounts_receivable', fn ($v) => (float) $v === 225.0));
});

test('dashboard overdue_invoices count is scoped to the organization', function () {
    [$user, $org, $customer] = reportingSetup();
    [, , $otherCustomer]     = reportingSetup();

    Invoice::factory()->forCustomer($customer)->create(['status' => Invoice::STATUS_OVERDUE]);
    Invoice::factory()->forCustomer($customer)->create(['status' => Invoice::STATUS_OVERDUE]);
    Invoice::factory()->forCustomer($otherCustomer)->create(['status' => Invoice::STATUS_OVERDUE]);

    $this->actingAs($user)
        ->get('/owner/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.overdue_invoices', 2));
});

test('dashboard open_jobs excludes completed and cancelled jobs', function () {
    [$user, $org, $customer] = reportingSetup();

    Job::factory()->forCustomer($customer)->scheduled()->count(3)->create();
    Job::factory()->forCustomer($customer)->completed()->count(2)->create();
    Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_CANCELLED]);

    $this->actingAs($user)
        ->get('/owner/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.open_jobs', 3));
});

test('dashboard unassigned_jobs counts open jobs with no assigned technician', function () {
    [$user, $org, $customer] = reportingSetup();
    $tech = User::factory()->create(['organization_id' => $org->id]);

    Job::factory()->forCustomer($customer)->scheduled()->count(2)->create(['assigned_to' => null]);
    Job::factory()->forCustomer($customer)->scheduled()->create(['assigned_to' => $tech->id]);
    Job::factory()->forCustomer($customer)->completed()->create(['assigned_to' => null]); // excluded (completed)

    $this->actingAs($user)
        ->get('/owner/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.unassigned_jobs', 2));
});

// ── Jobs by Type ───────────────────────────────────────────────────────────────

test('user can view the jobs-by-type report', function () {
    [$user] = reportingSetup();

    $this->actingAs($user)
        ->get('/owner/reports/jobs-by-type')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Reports/JobsByType'));
});

test('jobs-by-type report is scoped to the organization', function () {
    [$user, $org, $customer] = reportingSetup();
    [, , $otherCustomer]     = reportingSetup();

    $jobType = JobType::factory()->create(['organization_id' => $org->id]);
    Job::factory()->forCustomer($customer)->count(3)->create([
        'job_type_id'  => $jobType->id,
        'status'       => Job::STATUS_COMPLETED,
        'scheduled_at' => today(),
    ]);
    Job::factory()->forCustomer($otherCustomer)->count(5)->create([
        'scheduled_at' => today(),
    ]);

    $this->actingAs($user)
        ->get('/owner/reports/jobs-by-type')
        ->assertInertia(fn ($page) => $page->has('rows', 1));
});

test('jobs-by-type report respects date range filter', function () {
    [$user, $org, $customer] = reportingSetup();
    $jobType = JobType::factory()->create(['organization_id' => $org->id]);

    // In range
    Job::factory()->forCustomer($customer)->count(2)->create([
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now()->subDays(5),
    ]);
    // Out of range
    Job::factory()->forCustomer($customer)->count(3)->create([
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now()->subDays(60),
    ]);

    $from = now()->subDays(10)->toDateString();
    $to   = now()->toDateString();

    $this->actingAs($user)
        ->get("/owner/reports/jobs-by-type?from={$from}&to={$to}")
        ->assertInertia(fn ($page) => $page->has('rows', 1)
            ->where('rows.0.total', 2));
});

// ── Job Profitability ──────────────────────────────────────────────────────────

test('user can view the job-profitability report', function () {
    seedRoles();
    [$user] = reportingSetup();

    $this->actingAs($user)
        ->get('/owner/reports/job-profitability')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Reports/JobProfitability'));
});

test('job-profitability report only includes completed jobs', function () {
    seedRoles();
    [$user, $org, $customer] = reportingSetup();

    Job::factory()->forCustomer($customer)->completed()->count(2)->create([
        'completed_at' => now()->subDays(3),
    ]);
    Job::factory()->forCustomer($customer)->scheduled()->count(4)->create();

    $this->actingAs($user)
        ->get('/owner/reports/job-profitability')
        ->assertInertia(fn ($page) => $page->has('jobs', 2));
});

test('job-profitability report is scoped to the organization', function () {
    seedRoles();
    [$user, $org, $customer] = reportingSetup();
    [, , $otherCustomer]     = reportingSetup();

    Job::factory()->forCustomer($customer)->completed()->count(2)->create([
        'completed_at' => now()->subDays(1),
    ]);
    Job::factory()->forCustomer($otherCustomer)->completed()->count(5)->create([
        'completed_at' => now()->subDays(1),
    ]);

    $this->actingAs($user)
        ->get('/owner/reports/job-profitability')
        ->assertInertia(fn ($page) => $page->has('jobs', 2));
});

test('job-profitability margin calculation is correct', function () {
    seedRoles();
    [$user, $org, $customer] = reportingSetup();

    $job = Job::factory()->forCustomer($customer)->completed()->create([
        'completed_at' => now()->subDays(1),
    ]);

    // Parts cost: 2 items × $25 = $50
    JobLineItem::factory()->create(['job_id' => $job->id, 'unit_price' => 25.00, 'quantity' => 2]);

    // Invoice total: $150
    Invoice::factory()->forCustomer($customer)->create([
        'job_id' => $job->id,
        'total'  => 150.00,
        'status' => Invoice::STATUS_PAID,
    ]);

    $this->actingAs($user)
        ->get('/owner/reports/job-profitability')
        ->assertInertia(fn ($page) => $page
            ->where('jobs.0.revenue', fn ($v) => (float) $v === 150.0)
            ->where('jobs.0.parts_cost', fn ($v) => (float) $v === 50.0)
            ->where('jobs.0.margin', fn ($v) => (float) $v === 100.0)
            ->where('jobs.0.margin_pct', 66.7)
        );
});

test('job-profitability report can filter by job type', function () {
    seedRoles();
    [$user, $org, $customer] = reportingSetup();
    $typeA = JobType::factory()->create(['organization_id' => $org->id]);
    $typeB = JobType::factory()->create(['organization_id' => $org->id]);

    Job::factory()->forCustomer($customer)->completed()->count(2)->create([
        'job_type_id'  => $typeA->id,
        'completed_at' => now()->subDays(1),
    ]);
    Job::factory()->forCustomer($customer)->completed()->count(3)->create([
        'job_type_id'  => $typeB->id,
        'completed_at' => now()->subDays(1),
    ]);

    $this->actingAs($user)
        ->get("/owner/reports/job-profitability?job_type_id={$typeA->id}")
        ->assertInertia(fn ($page) => $page->has('jobs', 2));
});

// ── Technician Performance ─────────────────────────────────────────────────────

test('user can view the technician-performance report', function () {
    seedRoles();
    [$user] = reportingSetup();

    $this->actingAs($user)
        ->get('/owner/reports/technician-performance')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Reports/TechnicianPerformance'));
});

test('technician-performance report is scoped to the organization', function () {
    seedRoles();
    [$user, $org, $customer] = reportingSetup();
    [, $otherOrg]            = reportingSetup();

    $tech      = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');

    $otherTech = User::factory()->create(['organization_id' => $otherOrg->id]);
    $otherTech->assignRole('technician');

    $this->actingAs($user)
        ->get('/owner/reports/technician-performance')
        ->assertInertia(fn ($page) => $page->has('technicians', 1));
});

test('technician-performance report shows jobs_completed count', function () {
    seedRoles();
    [$user, $org, $customer] = reportingSetup();

    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');

    Job::factory()->forCustomer($customer)->completed()->count(3)->create([
        'assigned_to'  => $tech->id,
        'completed_at' => now()->subDays(5),
    ]);
    // Scheduled — not counted
    Job::factory()->forCustomer($customer)->scheduled()->create(['assigned_to' => $tech->id]);

    $this->actingAs($user)
        ->get('/owner/reports/technician-performance')
        ->assertInertia(fn ($page) => $page->where('technicians.0.jobs_completed', 3));
});

test('technician-performance report respects date range filter', function () {
    seedRoles();
    [$user, $org, $customer] = reportingSetup();

    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');

    // In range
    Job::factory()->forCustomer($customer)->completed()->count(2)->create([
        'assigned_to'  => $tech->id,
        'completed_at' => now()->subDays(5),
    ]);
    // Out of range
    Job::factory()->forCustomer($customer)->completed()->count(4)->create([
        'assigned_to'  => $tech->id,
        'completed_at' => now()->subDays(60),
    ]);

    $from = now()->subDays(10)->toDateString();
    $to   = now()->toDateString();

    $this->actingAs($user)
        ->get("/owner/reports/technician-performance?from={$from}&to={$to}")
        ->assertInertia(fn ($page) => $page->where('technicians.0.jobs_completed', 2));
});
