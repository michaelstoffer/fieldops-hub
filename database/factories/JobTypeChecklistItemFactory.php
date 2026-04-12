<?php

namespace Database\Factories;

use App\Models\JobType;
use App\Models\JobTypeChecklistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobTypeChecklistItem>
 */
class JobTypeChecklistItemFactory extends Factory
{
    protected $model = JobTypeChecklistItem::class;

    public function definition(): array
    {
        return [
            'job_type_id' => JobType::factory(),
            'label' => fake()->randomElement([
                'Verify customer contact on site',
                'Inspect equipment condition',
                'Take before photos',
                'Perform service',
                'Test operation',
                'Take after photos',
                'Review work with customer',
            ]),
            'sort_order' => 0,
            'is_required' => false,
        ];
    }

    public function required(): static
    {
        return $this->state(['is_required' => true]);
    }
}
