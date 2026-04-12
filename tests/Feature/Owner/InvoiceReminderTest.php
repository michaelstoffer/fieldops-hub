<?php

use App\Mail\InvoiceReminderMail;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function reminderSetup(): array
{
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id, 'email' => 'customer@example.com']);

    return [$org, $customer];
}

test('reminder command queues mail for invoices sent 7+ days ago', function () {
    Mail::fake();

    [, $customer] = reminderSetup();

    Invoice::factory()->forCustomer($customer)->sent()->create([
        'sent_at'     => now()->subDays(7),
        'balance_due' => 150,
    ]);

    $this->artisan('invoices:send-reminders')->assertSuccessful();

    Mail::assertQueued(InvoiceReminderMail::class, 1);
});

test('reminder command skips invoices sent fewer than 7 days ago', function () {
    Mail::fake();

    [, $customer] = reminderSetup();

    Invoice::factory()->forCustomer($customer)->sent()->create([
        'sent_at'     => now()->subDays(3),
        'balance_due' => 100,
    ]);

    $this->artisan('invoices:send-reminders')->assertSuccessful();

    Mail::assertNothingQueued();
});

test('reminder command skips paid invoices', function () {
    Mail::fake();

    [, $customer] = reminderSetup();

    Invoice::factory()->forCustomer($customer)->paid()->create([
        'sent_at' => now()->subDays(10),
    ]);

    $this->artisan('invoices:send-reminders')->assertSuccessful();

    Mail::assertNothingQueued();
});

test('reminder command skips customers without email', function () {
    Mail::fake();

    [, $customer] = reminderSetup();
    $customer->update(['email' => null]);

    Invoice::factory()->forCustomer($customer)->sent()->create([
        'sent_at'     => now()->subDays(8),
        'balance_due' => 200,
    ]);

    $this->artisan('invoices:send-reminders')->assertSuccessful();

    Mail::assertNothingQueued();
});

test('reminder command respects custom --days option', function () {
    Mail::fake();

    [, $customer] = reminderSetup();

    Invoice::factory()->forCustomer($customer)->sent()->create([
        'sent_at'     => now()->subDays(3),
        'balance_due' => 75,
    ]);

    $this->artisan('invoices:send-reminders', ['--days' => 2])->assertSuccessful();

    Mail::assertQueued(InvoiceReminderMail::class, 1);
});
