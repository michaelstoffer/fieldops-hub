<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
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

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the organization first
        $slug = Str::slug($request->company_name);
        $baseSlug = $slug;
        $counter = 2;
        while (Organization::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $organization = Organization::create([
            'name' => $request->company_name,
            'slug' => $slug,
        ]);

        // Create the user and attach to the org
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'organization_id' => $organization->id,
        ]);

        // Assign the owner role
        $user->assignRole('owner');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('owner.dashboard', absolute: false));
    }
}
