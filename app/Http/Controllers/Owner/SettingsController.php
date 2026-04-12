<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\OrganizationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Inertia\ResponseFactory;

class SettingsController extends Controller
{
    private function getOrCreateSettings(int $orgId): OrganizationSetting
    {
        return OrganizationSetting::firstOrCreate(
            ['organization_id' => $orgId],
        );
    }

    // ─── Company Settings ─────────────────────────────────────────────────────

    public function company(Request $request): Response|ResponseFactory
    {
        $settings = $this->getOrCreateSettings($request->user()->organization_id);

        return inertia('Owner/Settings/Company', [
            'settings' => [
                'company_name'     => $settings->company_name,
                'company_email'    => $settings->company_email,
                'company_phone'    => $settings->company_phone,
                'company_address'  => $settings->company_address,
                'company_city'     => $settings->company_city,
                'company_state'    => $settings->company_state,
                'company_zip'      => $settings->company_zip,
                'company_website'  => $settings->company_website,
                'logo_path'        => $settings->logo_path ? Storage::url($settings->logo_path) : null,
                'default_tax_rate' => $settings->default_tax_rate,
            ],
        ]);
    }

    public function updateCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name'     => 'nullable|string|max:255',
            'company_email'    => 'nullable|email|max:255',
            'company_phone'    => 'nullable|string|max:30',
            'company_address'  => 'nullable|string|max:255',
            'company_city'     => 'nullable|string|max:100',
            'company_state'    => 'nullable|string|max:10',
            'company_zip'      => 'nullable|string|max:20',
            'company_website'  => 'nullable|url|max:255',
            'default_tax_rate' => 'nullable|numeric|min:0|max:100',
            'logo'             => 'nullable|image|max:2048',
        ]);

        $orgId    = $request->user()->organization_id;
        $settings = $this->getOrCreateSettings($orgId);

        if ($request->hasFile('logo')) {
            // Delete old logo if present
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($validated['logo']);

        if (isset($validated['default_tax_rate'])) {
            $validated['default_tax_rate'] = $validated['default_tax_rate'] / 100;
        }

        $settings->update($validated);

        return back()->with('success', 'Company settings saved.');
    }

    // ─── Integration Settings ─────────────────────────────────────────────────

    public function integrations(Request $request): Response|ResponseFactory
    {
        $settings = $this->getOrCreateSettings($request->user()->organization_id);

        return inertia('Owner/Settings/Integrations', [
            'settings' => [
                // Stripe — never expose the secret key; show masked version
                'stripe_secret_key'      => OrganizationSetting::mask($settings->getRawOriginal('stripe_secret_key') ? $settings->stripe_secret_key : null),
                'stripe_publishable_key' => $settings->stripe_publishable_key,
                'stripe_webhook_secret'  => OrganizationSetting::mask($settings->getRawOriginal('stripe_webhook_secret') ? $settings->stripe_webhook_secret : null),
                // Twilio
                'twilio_account_sid'  => $settings->twilio_account_sid,
                'twilio_auth_token'   => OrganizationSetting::mask($settings->getRawOriginal('twilio_auth_token') ? $settings->twilio_auth_token : null),
                'twilio_from_number'  => $settings->twilio_from_number,
                // SendGrid
                'sendgrid_api_key'    => OrganizationSetting::mask($settings->getRawOriginal('sendgrid_api_key') ? $settings->sendgrid_api_key : null),
                'sendgrid_from_email' => $settings->sendgrid_from_email,
                // Maps
                'google_maps_api_key' => OrganizationSetting::mask($settings->getRawOriginal('google_maps_api_key') ? $settings->google_maps_api_key : null),
            ],
        ]);
    }

    public function updateIntegrations(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stripe_secret_key'      => 'nullable|string|max:255',
            'stripe_publishable_key' => 'nullable|string|max:255',
            'stripe_webhook_secret'  => 'nullable|string|max:255',
            'twilio_account_sid'     => 'nullable|string|max:100',
            'twilio_auth_token'      => 'nullable|string|max:255',
            'twilio_from_number'     => 'nullable|string|max:30',
            'sendgrid_api_key'       => 'nullable|string|max:255',
            'sendgrid_from_email'    => 'nullable|email|max:255',
            'google_maps_api_key'    => 'nullable|string|max:255',
        ]);

        $orgId    = $request->user()->organization_id;
        $settings = $this->getOrCreateSettings($orgId);

        // Only update a key when the submitted value is not a masked placeholder
        foreach ($validated as $field => $value) {
            if ($value && str_contains($value, '•')) {
                // User left the masked display value unchanged — don't overwrite
                unset($validated[$field]);
            }
        }

        $settings->update($validated);

        return back()->with('success', 'Integration settings saved.');
    }
}
