<?php

use App\Events\JobCreated;
use App\Listeners\SendJobConfirmationEmail;
use App\Listeners\SendJobConfirmationSms;
use App\Mail\JobConfirmationMail;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

// ── Email (#40) ───────────────────────────────────────────────────────────────

test('creating a job dispatches the JobCreated event', function () {
    Event::fake();

    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
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

test('SendJobConfirmationEmail listener queues a mail to the customer', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'email'           => 'customer@example.com',
    ]);
    $job = Job::factory()->forCustomer($customer)->create();

    $listener = new SendJobConfirmationEmail();
    $listener->handle(new JobCreated($job));

    Mail::assertQueued(JobConfirmationMail::class, function ($mail) use ($customer) {
        return $mail->hasTo($customer->email);
    });
});

test('SendJobConfirmationEmail skips customers with no email', function () {
    Mail::fake();

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'email'           => null,
    ]);
    $job = Job::factory()->forCustomer($customer)->create();

    $listener = new SendJobConfirmationEmail();
    $listener->handle(new JobCreated($job));

    Mail::assertNothingQueued();
});

test('JobConfirmationMail has the correct subject', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);
    $job      = Job::factory()->forCustomer($customer)->create(['title' => 'HVAC Service']);

    $mail = new JobConfirmationMail($job);

    expect($mail->envelope()->subject)->toBe('Job Confirmation: HVAC Service');
});

// ── SMS (#41) ─────────────────────────────────────────────────────────────────

test('SendJobConfirmationSms listener sends an SMS to the customer mobile', function () {
    $smsFake = new class implements SmsService {
        public array $sent = [];
        public function send(string $to, string $message): void
        {
            $this->sent[] = compact('to', 'message');
        }
    };

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => '+15550001234',
        'phone'           => '+15559999999',
    ]);
    $job = Job::factory()->forCustomer($customer)->create(['title' => 'Pipe Repair']);

    $listener = new SendJobConfirmationSms($smsFake);
    $listener->handle(new JobCreated($job));

    expect($smsFake->sent)->toHaveCount(1);
    expect($smsFake->sent[0]['to'])->toBe('+15550001234');
    expect($smsFake->sent[0]['message'])->toContain('Pipe Repair');
});

test('SendJobConfirmationSms falls back to phone when mobile is null', function () {
    $smsFake = new class implements SmsService {
        public array $sent = [];
        public function send(string $to, string $message): void
        {
            $this->sent[] = compact('to', 'message');
        }
    };

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => null,
        'phone'           => '+15558887777',
    ]);
    $job = Job::factory()->forCustomer($customer)->create();

    $listener = new SendJobConfirmationSms($smsFake);
    $listener->handle(new JobCreated($job));

    expect($smsFake->sent[0]['to'])->toBe('+15558887777');
});

test('SendJobConfirmationSms skips customers with no phone numbers', function () {
    $smsFake = new class implements SmsService {
        public array $sent = [];
        public function send(string $to, string $message): void
        {
            $this->sent[] = compact('to', 'message');
        }
    };

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => null,
        'phone'           => null,
    ]);
    $job = Job::factory()->forCustomer($customer)->create();

    $listener = new SendJobConfirmationSms($smsFake);
    $listener->handle(new JobCreated($job));

    expect($smsFake->sent)->toBeEmpty();
});

test('SMS message contains the scheduled date', function () {
    $smsFake = new class implements SmsService {
        public array $sent = [];
        public function send(string $to, string $message): void
        {
            $this->sent[] = compact('to', 'message');
        }
    };

    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create([
        'organization_id' => $org->id,
        'mobile'          => '+15550001111',
    ]);
    $job = Job::factory()->forCustomer($customer)->create([
        'scheduled_at' => '2026-06-15 10:00:00',
    ]);

    $listener = new SendJobConfirmationSms($smsFake);
    $listener->handle(new JobCreated($job));

    expect($smsFake->sent[0]['message'])->toContain('Jun 15');
});

test('SMS listener is queued', function () {
    $fake = new class implements SmsService {
        public function send(string $to, string $message): void {}
    };

    expect(new SendJobConfirmationSms($fake))
        ->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('email listener is queued', function () {
    expect(new SendJobConfirmationEmail())
        ->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});
