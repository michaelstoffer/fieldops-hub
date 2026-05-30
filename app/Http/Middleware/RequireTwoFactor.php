<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces owner and admin users to have 2FA confirmed before accessing protected routes.
 * Renders a styled Inertia page in-place rather than redirecting, avoiding redirect loops
 * and unstyled interstitials caused by the password.confirm gate on two-factor.show.
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
            return Inertia::render('auth/TwoFactorRequired')->toResponse($request);
        }

        return $next($request);
    }
}
