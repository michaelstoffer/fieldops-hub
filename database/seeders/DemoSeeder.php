<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $org = Organization::firstOrCreate(
            ['slug' => 'demo-fieldops'],
            [
                'name' => 'Demo Field Ops',
                'timezone' => 'America/New_York',
            ]
        );

        $users = [
            ['name' => 'Admin User', 'email' => 'admin@demo.test', 'role' => 'admin'],
            ['name' => 'Alice Owner', 'email' => 'owner@demo.test', 'role' => 'owner'],
            ['name' => 'Bob Dispatcher', 'email' => 'dispatcher@demo.test', 'role' => 'dispatcher'],
            ['name' => 'Carol Tech', 'email' => 'tech@demo.test', 'role' => 'technician'],
            ['name' => 'Dave Books', 'email' => 'bookkeeper@demo.test', 'role' => 'bookkeeper'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'organization_id' => $org->id,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);
        }

        $jobTypes = [
            ['name' => 'HVAC Service', 'color' => '#3b82f6'],
            ['name' => 'Plumbing', 'color' => '#10b981'],
            ['name' => 'Electrical', 'color' => '#f59e0b'],
            ['name' => 'General Maintenance', 'color' => '#6366f1'],
        ];

        foreach ($jobTypes as $type) {
            JobType::firstOrCreate(
                ['organization_id' => $org->id, 'name' => $type['name']],
                ['color' => $type['color']]
            );
        }

        $customers = [
            ['first_name' => 'John', 'last_name' => 'Smith', 'email' => 'john.smith@example.com', 'phone' => '555-0101'],
            ['first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'jane.doe@example.com', 'phone' => '555-0102'],
            ['first_name' => 'Bob', 'last_name' => 'Johnson', 'email' => 'bob.johnson@example.com', 'phone' => '555-0103'],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(
                ['organization_id' => $org->id, 'email' => $c['email']],
                array_merge($c, ['organization_id' => $org->id])
            );
        }
    }
}
