<?php

namespace Database\Seeders;

use App\Models\FoundingMemberCoupon;
use Illuminate\Database\Seeder;

class FoundingMemberCouponSeeder extends Seeder
{
    public function run(): void
    {
        FoundingMemberCoupon::firstOrCreate(
            ['code' => 'FOUNDING'],
            [
                'description' => 'Founding Member — price locked at annual rate, billed monthly forever',
                'discount_percent' => 20,
                'max_uses' => 10,
                'uses' => 0,
                'active' => true,
                'expires_at' => null,
            ]
        );
    }
}
