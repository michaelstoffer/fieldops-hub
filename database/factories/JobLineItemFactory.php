<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\JobLineItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobLineItem>
 */
class JobLineItemFactory extends Factory
{
    protected $model = JobLineItem::class;

    public function definition(): array
    {
        return [
            'job_id'      => Job::factory(),
            'item_id'     => null,
            'name'        => fake()->words(3, true),
            'description' => null,
            'unit_price'  => fake()->randomFloat(2, 5, 500),
            'quantity'    => 1,
            'sort_order'  => 0,
        ];
    }
}
