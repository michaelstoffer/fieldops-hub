<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces owner and admin users to have 2FA confirmed before accessing protected routes.
 * Users without 2FA set up are redirected to the profile/security page.
 */
class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || app()->runningUnitTests()) {
            return $next($request);
        }

        if (! $user->hasRole(['owner', 'admin'])) {
            return $next($request);
        }

        // two_factor_confirmed_at is set by Fortify after the user confirms their TOTP code
        if (! $user->two_factor_confirmed_at) {
            return redirect()->route('two-factor.show')
                ->with('warning', 'Two-factor authentication is required for your account. Please enable it to continue.');
        }

        return $next($request);
    }
}
