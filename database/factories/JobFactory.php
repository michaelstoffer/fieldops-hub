<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $org = Organization::factory();

        return [
            'organization_id' => $org,
            'customer_id'     => Customer::factory(),
            'property_id'     => null,
            'job_type_id'     => null,
            'assigned_to'     => null,
            'title'           => fake()->randomElement([
                'Annual HVAC Service',
                'Emergency Plumbing Repair',
                'Electrical Panel Inspection',
                'AC Unit Installation',
                'Water Heater Replacement',
                'General Maintenance',
            ]),
            'description'     => fake()->optional(0.5)->sentence(),
            'status'          => fake()->randomElement([
                Job::STATUS_SCHEDULED,
                Job::STATUS_IN_PROGRESS,
                Job::STATUS_COMPLETED,
            ]),
            'scheduled_at'    => fake()->dateTimeBetween('-1 week', '+2 weeks'),
            'started_at'      => null,
            'completed_at'    => null,
            'cancelled_at'    => null,
            'technician_notes' => null,
            'office_notes'    => null,
        ];
    }

    public function scheduled(): static
    {
        return $this->state(['status' => Job::STATUS_SCHEDULED]);
    }

    public function completed(): static
    {
        return $this->state([
            'status'       => Job::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    /**
     * Belong to a specific org, customer, and optionally property.
     */
    public function forCustomer(Customer $customer): static
    {
        return $this->state([
            'organization_id' => $customer->organization_id,
            'customer_id'     => $customer->id,
        ]);
    }
}
