<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org = Organization::create([
            'name' => 'Demo FieldOps',
            'slug' => 'demo-fieldops',
        ]);

        User::create([
            'name' => 'Demo Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'organization_id' => $org->id,
            'role' => 'owner',
        ]);
    }
}
