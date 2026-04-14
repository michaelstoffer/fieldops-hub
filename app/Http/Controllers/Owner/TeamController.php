<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PlanService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly SubscriptionService $subscriptionService,
    ) {}

    public function index(Request $request): Response
    {
        $org   = $request->user()->organization;
        $users = $org->users()
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => [
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
                'roles' => $u->getRoleNames()->values()->all(),
            ]);

        $techLimit = $this->planService->technicianLimit($org);
        $techCount = $this->planService->technicianCount($org);

        return Inertia::render('Owner/Team/Index', [
            'team_members'      => $users,
            'roles'             => ['owner', 'admin', 'dispatcher', 'bookkeeper', 'technician'],
            'technician_limit'  => $techLimit,
            'technician_count'  => $techCount,
            'at_limit'          => $this->planService->atTechnicianLimit($org),
            'active_plan'       => $this->planService->activePlan($org),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
            'role'     => ['required', 'string', 'in:owner,admin,dispatcher,bookkeeper,technician'],
        ]);

        $org = $request->user()->organization;

        // Enforce technician seat cap
        if ($request->role === 'technician' && $this->planService->atTechnicianLimit($org)) {
            $limit = $this->planService->technicianLimit($org);
            return back()->withErrors([
                'role' => "Your plan allows up to {$limit} technicians. Upgrade to add more.",
            ]);
        }

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'organization_id'   => $org->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($request->role);

        $this->subscriptionService->flushOrgCache($org->id);

        return back()->with('success', "{$user->name} has been added to your team.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $org = $request->user()->organization;

        abort_if($user->organization_id !== $org->id, 403);

        $request->validate([
            'role' => ['required', 'string', 'in:owner,admin,dispatcher,bookkeeper,technician'],
        ]);

        // Enforce technician cap if promoting to technician
        $wasNotTechnician = ! $user->hasRole('technician');
        if ($request->role === 'technician' && $wasNotTechnician && $this->planService->atTechnicianLimit($org)) {
            $limit = $this->planService->technicianLimit($org);
            return back()->withErrors([
                'role' => "Your plan allows up to {$limit} technicians. Upgrade to add more.",
            ]);
        }

        $user->syncRoles([$request->role]);

        $this->subscriptionService->flushOrgCache($org->id);

        return back()->with('success', "{$user->name}'s role has been updated.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $org = $request->user()->organization;

        abort_if($user->organization_id !== $org->id, 403);
        abort_if($user->id === $request->user()->id, 403, 'You cannot remove yourself.');

        $user->delete();

        $this->subscriptionService->flushOrgCache($org->id);

        return back()->with('success', "{$user->name} has been removed from your team.");
    }
}
