<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'customer_id'     => Customer::factory(),
            'job_id'          => null,
            'invoice_number'  => 'INV-' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status'          => Invoice::STATUS_DRAFT,
            'subtotal'        => 0,
            'tax_rate'        => 0,
            'tax_amount'      => 0,
            'discount_amount' => 0,
            'total'           => 0,
            'amount_paid'     => 0,
            'balance_due'     => 0,
            'issued_at'       => today()->toDateString(),
            'due_at'          => today()->addDays(30)->toDateString(),
            'sent_at'         => null,
            'paid_at'         => null,
            'notes'           => null,
        ];
    }

    public function forCustomer(Customer $customer): static
    {
        return $this->state([
            'organization_id' => $customer->organization_id,
            'customer_id'     => $customer->id,
        ]);
    }

    public function draft(): static
    {
        return $this->state(['status' => Invoice::STATUS_DRAFT]);
    }

    public function sent(): static
    {
        return $this->state([
            'status'  => Invoice::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function paid(): static
    {
        return $this->state([
            'status'     => Invoice::STATUS_PAID,
            'sent_at'    => now()->subDays(5),
            'paid_at'    => now(),
            'amount_paid' => fn (array $attrs) => $attrs['total'],
            'balance_due' => 0,
        ]);
    }
}
