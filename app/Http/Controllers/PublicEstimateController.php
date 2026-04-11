<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class PublicEstimateController extends Controller
{
    public function show(string $token): Response|ResponseFactory
    {
        $estimate = Estimate::where('token', $token)
            ->with(['customer', 'packages.lineItems', 'organization'])
            ->firstOrFail();

        abort_unless(
            in_array($estimate->status, [Estimate::STATUS_SENT, Estimate::STATUS_ACCEPTED, Estimate::STATUS_DECLINED]),
            404
        );

        return inertia('Public/Estimate', [
            'estimate' => $estimate,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $estimate = Estimate::where('token', $token)->firstOrFail();

        abort_unless($estimate->status === Estimate::STATUS_SENT, 422);

        $request->validate([
            'tier' => ['required', 'in:' . implode(',', Estimate::TIERS)],
        ]);

        // Verify chosen tier has a package
        abort_unless(
            $estimate->packages()->where('tier', $request->tier)->exists(),
            422
        );

        $estimate->update([
            'status'           => Estimate::STATUS_ACCEPTED,
            'accepted_at'      => now(),
            'accepted_package' => $request->tier,
        ]);

        return redirect("/estimates/{$token}")->with('success', 'Thank you! Your estimate has been accepted.');
    }

    public function decline(string $token): RedirectResponse
    {
        $estimate = Estimate::where('token', $token)->firstOrFail();

        abort_unless($estimate->status === Estimate::STATUS_SENT, 422);

        $estimate->update([
            'status'      => Estimate::STATUS_DECLINED,
            'declined_at' => now(),
        ]);

        return redirect("/estimates/{$token}")->with('success', 'Your response has been recorded.');
    }
}
