<?php

namespace Database\Seeders;

use App\Models\FoundingMemberCoupon;
use Illuminate\Database\Seeder;

class FoundingMemberCouponSeeder extends Seeder
{
    public function run(): void
    {
        FoundingMemberCoupon::firstOrCreate(
            ['code' => 'FOUNDING40'],
            [
                'description' => 'Founding Member — 40% off for life',
                'discount_percent' => 40,
                'max_uses' => 10,
                'uses' => 0,
                'active' => true,
                'expires_at' => null,
            ]
        );
    }
}
