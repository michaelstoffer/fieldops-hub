<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->organization_id) {
            return $next($request);
        }

        // Owners and admins are subject to subscription checks.
        // Technicians, dispatchers, and bookkeepers are always allowed through
        // — their access is controlled by the owner's subscription.
        if (! $user->hasRole(['owner', 'admin'])) {
            return $next($request);
        }

        $org = $user->organization;
        $subscription = $org->activeSubscription();

        // No subscription at all — trial never started or was never created
        if (! $subscription) {
            return $this->redirectToExpired($request);
        }

        // Trial has ended and no active paid subscription
        if ($subscription->status === Subscription::STATUS_TRIALING
            && $subscription->trial_ends_at
            && $subscription->trial_ends_at->isPast()) {
            return $this->redirectToExpired($request);
        }

        // Paid subscription is canceled
        if ($subscription->status === Subscription::STATUS_CANCELED) {
            return $this->redirectToExpired($request);
        }

        return $next($request);
    }

    private function redirectToExpired(Request $request): Response
    {
        if ($request->inertia()) {
            return Inertia::location(route('owner.subscription.expired'));
        }

        return redirect()->route('owner.subscription.expired');
    }
}
