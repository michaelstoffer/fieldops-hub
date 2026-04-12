<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estimate>
 */
class EstimateFactory extends Factory
{
    protected $model = Estimate::class;

    public function definition(): array
    {
        return [
            'organization_id'  => Organization::factory(),
            'customer_id'      => Customer::factory(),
            'job_id'           => null,
            'estimate_number'  => 'EST-' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'title'            => fake()->sentence(4),
            'intro'            => fake()->optional()->sentence(),
            'footer'           => fake()->optional()->sentence(),
            'status'           => Estimate::STATUS_DRAFT,
            'token'            => Str::random(48),
            'expires_at'       => now()->addDays(30)->toDateString(),
            'sent_at'          => null,
            'accepted_at'      => null,
            'accepted_package' => null,
            'declined_at'      => null,
            'tax_rate'         => 0,
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
        return $this->state(['status' => Estimate::STATUS_DRAFT]);
    }

    public function sent(): static
    {
        return $this->state([
            'status'  => Estimate::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function accepted(string $tier = 'better'): static
    {
        return $this->state([
            'status'           => Estimate::STATUS_ACCEPTED,
            'sent_at'          => now()->subDays(2),
            'accepted_at'      => now(),
            'accepted_package' => $tier,
        ]);
    }
}
