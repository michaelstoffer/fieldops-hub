<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name'            => fake()->words(3, true),
            'sku'             => fake()->optional(0.6)->bothify('SKU-###??'),
            'description'     => fake()->optional(0.4)->sentence(),
            'unit_price'      => fake()->randomFloat(2, 5, 500),
            'unit'            => fake()->randomElement(['each', 'hr', 'ft', 'sqft']),
            'is_taxable'      => true,
            'is_active'       => true,
        ];
    }
}
