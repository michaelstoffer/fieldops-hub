<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobType>
 */
class JobTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name'            => fake()->unique()->randomElement([
                'HVAC Service', 'Plumbing', 'Electrical', 'General Maintenance',
                'Roofing', 'Landscaping', 'Pest Control', 'Cleaning',
            ]).'-'.fake()->numerify('###'),
            'color'           => fake()->hexColor(),
            'description'     => fake()->optional(0.4)->sentence(),
            'is_active'       => true,
        ];
    }
}
