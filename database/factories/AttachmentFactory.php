<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'uploaded_by'     => User::factory(),
            'attachable_type' => Job::class,
            'attachable_id'   => Job::factory(),
            'filename'        => fake()->word() . '.jpg',
            'disk'            => 'public',
            'path'            => 'jobs/1/photos/' . fake()->uuid() . '.jpg',
            'mime_type'       => 'image/jpeg',
            'size'            => fake()->numberBetween(50000, 500000),
            'tag'             => null,
        ];
    }

    public function before(): static
    {
        return $this->state(['tag' => 'before']);
    }

    public function after(): static
    {
        return $this->state(['tag' => 'after']);
    }
}
