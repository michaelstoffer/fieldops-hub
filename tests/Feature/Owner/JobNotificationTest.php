<?php

use App\Events\JobCreated;
use App\Events\JobStatusChanged;
use App\Listeners\SendJobConfirmationEmail;
use App\Listeners\SendJobConfirmationSms;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Services\MessageDispatcher;
use App\Services\SmsService;
use App\Services\TemplateRenderer;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

// Helper: build a MessageDispatcher with a fake SMS service and return both
function makeDispatcherWithFakeSms(array &$sent): MessageDispatcher
{
    $smsFake = new class($sent) implements SmsService {
        public function __construct(private array &$sent) {}
        public function send(string $to, string $message): void { $this->sent[] = compact('to', 'message'); }
    };
    return new MessageDispatcher($smsFake);
}

// ── Email (#40 / #88) ─────────────────────────────────────────────────────────

test('creating a job dispatches the JobCreated event', function () {
    Event::fake();
    (new RolesAndPermissionsSeeder)->run();

    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)
        ->post('/owner/jobs', [
            'customer_id'  => $customer->id,
            'title'        => 'Boiler Check',
            'scheduled_at' => '2026-06-01T09:00',
        ])
        ->assertRedirect();

    Event::assertDispatched(JobCreated::class);
});

test('SendJobConfirmationEmail listener logs email to job_messages', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'email'           => 'customer@example.com',
    ]);
    $job = Job::factory()->forCustomer($customer)->create();

    $sent = [];
    $listener = new SendJobConfirmationEmail(makeDispatcherWithFakeSms($sent), new TemplateRenderer());
    $listener->handle(new JobCreated($job));

    $this->assertDatabaseHas('job_messages', [
        'job_id'    => $job->id,
        'channel'   => 'email',
        'event'     => 'job_scheduled',
        'recipient' => 'customer@example.com',
        'status'    => 'sent',
    ]);
});

test('SendJobConfirmationEmail skips customers with no email', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'email'           => null,
    ]);
    $job = Job::factory()->forCustomer($customer)->create();

    $sent = [];
    $listener = new SendJobConfirmationEmail(makeDispatcherWithFakeSms($sent), new TemplateRenderer());
    $listener->handle(new JobCreated($job));

    Mail::assertNothingSent();
});

// ── SMS (#41 / #88) ───────────────────────────────────────────────────────────

test('SendJobConfirmationSms listener sends SMS to customer mobile', function () {
    $sent     = [];
    $renderer = new TemplateRenderer();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => '+15550001234',
        'phone'           => '+15559999999',
    ]);
    $job = Job::factory()->forCustomer($customer)->create(['title' => 'Pipe Repair']);

    $listener = new SendJobConfirmationSms(makeDispatcherWithFakeSms($sent), $renderer);
    $listener->handle(new JobCreated($job));

    expect($sent)->toHaveCount(1);
    expect($sent[0]['to'])->toBe('+15550001234');
    expect($sent[0]['message'])->toContain('Pipe Repair');
});

test('SendJobConfirmationSms falls back to phone when mobile is null', function () {
    $sent = [];

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => null,
        'phone'           => '+15558887777',
    ]);
    $job = Job::factory()->forCustomer($customer)->create();

    $listener = new SendJobConfirmationSms(makeDispatcherWithFakeSms($sent), new TemplateRenderer());
    $listener->handle(new JobCreated($job));

    expect($sent[0]['to'])->toBe('+15558887777');
});

test('SendJobConfirmationSms skips customers with no phone numbers', function () {
    $sent = [];

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => null,
        'phone'           => null,
    ]);
    $job = Job::factory()->forCustomer($customer)->create();

    $listener = new SendJobConfirmationSms(makeDispatcherWithFakeSms($sent), new TemplateRenderer());
    $listener->handle(new JobCreated($job));

    expect($sent)->toBeEmpty();
});

test('SMS message contains the job title', function () {
    $sent = [];

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id, 'mobile' => '+15550001111']);
    $job = Job::factory()->forCustomer($customer)->create([
        'title'        => 'Pipe Repair',
        'scheduled_at' => '2026-06-15 10:00:00',
    ]);

    $listener = new SendJobConfirmationSms(makeDispatcherWithFakeSms($sent), new TemplateRenderer());
    $listener->handle(new JobCreated($job));

    expect($sent[0]['message'])->toContain('Pipe Repair');
});

test('SMS listener is queued', function () {
    $sent = [];
    expect(new SendJobConfirmationSms(makeDispatcherWithFakeSms($sent), new TemplateRenderer()))
        ->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('email listener is queued', function () {
    $sent = [];
    expect(new SendJobConfirmationEmail(makeDispatcherWithFakeSms($sent), new TemplateRenderer()))
        ->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

// ── Status-based messages (#90 / #91) ─────────────────────────────────────────

test('en route status change sends SMS to customer', function () {
    $sent = [];

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id, 'mobile' => '+15550002222']);
    $job      = Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_SCHEDULED]);

    $listener = new \App\Listeners\SendJobStatusMessages(makeDispatcherWithFakeSms($sent), new TemplateRenderer());
    $listener->handle(new JobStatusChanged($job, Job::STATUS_SCHEDULED, Job::STATUS_EN_ROUTE));

    expect($sent)->toHaveCount(1);
    expect($sent[0]['to'])->toBe('+15550002222');
});

test('completed status change logs email to job_messages', function () {
    Mail::fake();

    $sent = [];
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id, 'email' => 'done@example.com']);
    $job      = Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_IN_PROGRESS]);

    $listener = new \App\Listeners\SendJobStatusMessages(makeDispatcherWithFakeSms($sent), new TemplateRenderer());
    $listener->handle(new JobStatusChanged($job, Job::STATUS_IN_PROGRESS, Job::STATUS_COMPLETED));

    $this->assertDatabaseHas('job_messages', [
        'job_id'    => $job->id,
        'channel'   => 'email',
        'event'     => 'job_completed',
        'recipient' => 'done@example.com',
        'status'    => 'sent',
    ]);
});
