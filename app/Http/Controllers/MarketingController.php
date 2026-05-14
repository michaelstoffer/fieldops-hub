<?php

namespace App\Http\Controllers;

use App\Models\FoundingMemberCoupon;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function index(): Response
    {
        $coupon = FoundingMemberCoupon::where('code', 'FOUNDING')
            ->where('active', true)
            ->first();

        $foundingOffer = null;

        if ($coupon && $coupon->isAvailable()) {
            $foundingOffer = [
                'remaining' => $coupon->remainingUses(),
                'max_uses'  => $coupon->max_uses,
                'prices'    => [
                    'starter' => 63,
                    'growth'  => 119,
                    'pro'     => 199,
                ],
            ];
        }

        return Inertia::render('Welcome', [
            'foundingOffer' => $foundingOffer,
        ]);
    }
}
