<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\FoundingMemberCoupon;
use App\Models\Organization;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    public function store(Request $request, SubscriptionService $subscriptionService): RedirectResponse
    {
        $request->validate([
            'plan'             => ['required', 'string', 'in:starter,growth,pro'],
            'billing_interval' => ['required', 'string', 'in:monthly,annual'],
            'company_name'     => ['required', 'string', 'max:255'],
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the organization
        $slug = Str::slug($request->company_name);
        $baseSlug = $slug;
        $counter = 2;
        while (Organization::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        // Honour founding member invite if the coupon still has slots
        $isFoundingMember = false;
        if ($request->boolean('founding')) {
            $coupon = FoundingMemberCoupon::where('active', true)->first();
            if ($coupon && $coupon->isAvailable()) {
                $isFoundingMember = true;
                $coupon->incrementUses();
            }
        }

        $organization = Organization::create([
            'name'            => $request->company_name,
            'slug'            => $slug,
            'plan'            => $request->plan,
            'founding_member' => $isFoundingMember,
        ]);

        // Create the owner user
        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'organization_id' => $organization->id,
        ]);

        $user->assignRole('owner');

        // Start the 14-day trial at the chosen plan and billing interval
        $subscriptionService->createTrial($organization, $request->plan, $request->billing_interval);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('owner.dashboard', absolute: false));
    }
}
