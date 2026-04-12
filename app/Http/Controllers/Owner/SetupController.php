<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use App\Models\OrganizationSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Response;
use Inertia\ResponseFactory;

class SetupController extends Controller
{
    /**
     * Return true if the organization has completed initial setup:
     *  - Company name is set
     *  - At least one job type exists
     *  - At least one technician exists
     */
    public static function isComplete(int $orgId): bool
    {
        $settings = OrganizationSetting::where('organization_id', $orgId)->first();
        if (! $settings || empty($settings->company_name)) {
            return false;
        }
        if (! JobType::where('organization_id', $orgId)->exists()) {
            return false;
        }
        if (! User::where('organization_id', $orgId)->role('technician')->exists()) {
            return false;
        }

        return true;
    }

    public function show(Request $request): Response|ResponseFactory|RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        if (self::isComplete($orgId)) {
            return redirect()->route('owner.dashboard');
        }

        $settings = OrganizationSetting::firstOrCreate(['organization_id' => $orgId]);
        $jobTypes  = JobType::where('organization_id', $orgId)->get(['id', 'name', 'color']);
        $technicians = User::where('organization_id', $orgId)->role('technician')->get(['id', 'name', 'email']);

        return inertia('Owner/Setup/Wizard', [
            'company' => [
                'name'    => $settings->company_name,
                'email'   => $settings->company_email,
                'phone'   => $settings->company_phone,
                'address' => $settings->company_address,
                'city'    => $settings->company_city,
                'state'   => $settings->company_state,
                'zip'     => $settings->company_zip,
            ],
            'job_types'   => $jobTypes,
            'technicians' => $technicians,
        ]);
    }

    public function saveCompany(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city'    => ['nullable', 'string', 'max:100'],
            'state'   => ['nullable', 'string', 'max:100'],
            'zip'     => ['nullable', 'string', 'max:20'],
        ]);

        $settings = OrganizationSetting::firstOrCreate(['organization_id' => $orgId]);
        $settings->update([
            'company_name'    => $data['name'],
            'company_email'   => $data['email'] ?? null,
            'company_phone'   => $data['phone'] ?? null,
            'company_address' => $data['address'] ?? null,
            'company_city'    => $data['city'] ?? null,
            'company_state'   => $data['state'] ?? null,
            'company_zip'     => $data['zip'] ?? null,
        ]);

        return back()->with('success', 'Company info saved.');
    }

    public function addJobType(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100', Rule::unique('job_types')->where('organization_id', $orgId)],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        JobType::create([
            'organization_id' => $orgId,
            'name'            => $data['name'],
            'color'           => $data['color'],
        ]);

        return back()->with('success', 'Job type added.');
    }

    public function removeJobType(Request $request, JobType $jobType): RedirectResponse
    {
        abort_unless($jobType->organization_id === $request->user()->organization_id, 403);
        $jobType->delete();

        return back()->with('success', 'Job type removed.');
    }

    public function addTechnician(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'organization_id'   => $orgId,
            'email_verified_at' => now(),
        ]);
        $user->assignRole('technician');

        return back()->with('success', 'Technician added.');
    }

    public function complete(Request $request): RedirectResponse
    {
        $orgId = $request->user()->organization_id;

        if (! self::isComplete($orgId)) {
            return back()->withErrors(['setup' => 'Please complete all required setup steps before continuing.']);
        }

        return redirect()->route('owner.dashboard')->with('success', 'Setup complete! Welcome to FieldOps Hub.');
    }
}
