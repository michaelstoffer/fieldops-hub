<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $customer = Customer::factory();

        return [
            'organization_id' => Organization::factory(),
            'customer_id'     => $customer,
            'name'            => fake()->optional(0.5)->randomElement(['Main Residence', 'Office', 'Rental Property', 'Vacation Home']),
            'address_line1'   => fake()->streetAddress(),
            'address_line2'   => fake()->optional(0.2)->secondaryAddress(),
            'city'            => fake()->city(),
            'state'           => fake()->stateAbbr(),
            'postal_code'     => fake()->postcode(),
            'country'         => 'US',
            'latitude'        => null,
            'longitude'       => null,
            'notes'           => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * Belong to a specific customer (and inherit their org).
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->state([
            'organization_id' => $customer->organization_id,
            'customer_id'     => $customer->id,
        ]);
    }
}
