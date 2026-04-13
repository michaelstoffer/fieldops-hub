<?php

namespace App\Http\Controllers;

use App\Models\FoundingMemberCoupon;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function index(): Response
    {
        $coupon = FoundingMemberCoupon::where('code', 'FOUNDING40')
            ->where('active', true)
            ->first();

        $foundingOffer = null;

        if ($coupon && $coupon->isAvailable()) {
            $foundingOffer = [
                'discount_percent' => $coupon->discount_percent,
                'remaining' => $coupon->remainingUses(),
                'max_uses' => $coupon->max_uses,
            ];
        }

        return Inertia::render('Welcome', [
            'foundingOffer' => $foundingOffer,
        ]);
    }
}
