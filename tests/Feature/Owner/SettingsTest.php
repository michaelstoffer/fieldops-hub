<?php

use App\Models\Organization;
use App\Models\OrganizationSetting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function settingsSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');

    return [$user, $org];
}

// ── Authentication ─────────────────────────────────────────────────────────────

test('company settings page requires authentication', function () {
    $this->get('/owner/settings/company')->assertRedirect('/login');
});

test('integrations settings page requires authentication', function () {
    $this->get('/owner/settings/integrations')->assertRedirect('/login');
});

test('company settings update requires authentication', function () {
    $this->post('/owner/settings/company')->assertRedirect('/login');
});

test('integrations settings update requires authentication', function () {
    $this->post('/owner/settings/integrations')->assertRedirect('/login');
});

// ── Company Settings — Read ────────────────────────────────────────────────────

test('user can view company settings page', function () {
    [$user] = settingsSetup();

    $this->actingAs($user)
        ->get('/owner/settings/company')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Settings/Company')
            ->has('settings')
        );
});

test('company settings page creates settings record on first visit', function () {
    [$user, $org] = settingsSetup();

    expect(OrganizationSetting::where('organization_id', $org->id)->exists())->toBeFalse();

    $this->actingAs($user)->get('/owner/settings/company')->assertOk();

    expect(OrganizationSetting::where('organization_id', $org->id)->exists())->toBeTrue();
});

test('company settings page returns existing settings', function () {
    [$user, $org] = settingsSetup();
    OrganizationSetting::factory()->create([
        'organization_id' => $org->id,
        'company_name'    => 'Acme HVAC',
        'company_email'   => 'info@acme.com',
    ]);

    $this->actingAs($user)
        ->get('/owner/settings/company')
        ->assertInertia(fn ($page) => $page
            ->where('settings.company_name', 'Acme HVAC')
            ->where('settings.company_email', 'info@acme.com')
        );
});

// ── Company Settings — Update ──────────────────────────────────────────────────

test('user can update company settings', function () {
    [$user, $org] = settingsSetup();

    $this->actingAs($user)
        ->post('/owner/settings/company', [
            'company_name'  => 'Test Co',
            'company_email' => 'contact@testco.com',
            'company_phone' => '555-0100',
            'company_city'  => 'Austin',
            'company_state' => 'TX',
            'company_zip'   => '78701',
        ])
        ->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->company_name)->toBe('Test Co');
    expect($settings->company_email)->toBe('contact@testco.com');
    expect($settings->company_city)->toBe('Austin');
});

test('company settings update rejects invalid email', function () {
    [$user] = settingsSetup();

    $this->actingAs($user)
        ->post('/owner/settings/company', ['company_email' => 'not-an-email'])
        ->assertSessionHasErrors('company_email');
});

test('company settings update rejects invalid website URL', function () {
    [$user] = settingsSetup();

    $this->actingAs($user)
        ->post('/owner/settings/company', ['company_website' => 'not-a-url'])
        ->assertSessionHasErrors('company_website');
});

test('company settings are scoped to the user organization', function () {
    [$user, $org]       = settingsSetup();
    [$otherUser, $otherOrg] = settingsSetup();

    $this->actingAs($user)->post('/owner/settings/company', ['company_name' => 'Mine']);
    $this->actingAs($otherUser)->post('/owner/settings/company', ['company_name' => 'Theirs']);

    expect(OrganizationSetting::where('organization_id', $org->id)->value('company_name'))->toBe('Mine');
    expect(OrganizationSetting::where('organization_id', $otherOrg->id)->value('company_name'))->toBe('Theirs');
});

test('logo upload accepts a valid image and stores it', function () {
    Storage::fake('public');
    [$user] = settingsSetup();

    $file = UploadedFile::fake()->image('logo.png', 100, 100);

    $this->actingAs($user)
        ->post('/owner/settings/company', ['logo' => $file])
        ->assertRedirect();

    Storage::disk('public')->assertExists('logos/' . $file->hashName());
});

test('logo upload rejects non-image files', function () {
    [$user] = settingsSetup();

    $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

    $this->actingAs($user)
        ->post('/owner/settings/company', ['logo' => $file])
        ->assertSessionHasErrors('logo');
});

test('logo upload rejects files over 2MB', function () {
    [$user] = settingsSetup();

    $file = UploadedFile::fake()->image('large.jpg')->size(3000); // 3MB

    $this->actingAs($user)
        ->post('/owner/settings/company', ['logo' => $file])
        ->assertSessionHasErrors('logo');
});

test('uploading a new logo deletes the old one', function () {
    Storage::fake('public');
    [$user, $org] = settingsSetup();

    $firstFile  = UploadedFile::fake()->image('first.png');
    $secondFile = UploadedFile::fake()->image('second.png');

    $this->actingAs($user)->post('/owner/settings/company', ['logo' => $firstFile]);
    $firstPath = OrganizationSetting::where('organization_id', $org->id)->value('logo_path');

    $this->actingAs($user)->post('/owner/settings/company', ['logo' => $secondFile]);

    Storage::disk('public')->assertMissing($firstPath);
});

// ── Integrations Settings — Read ──────────────────────────────────────────────

test('user can view integrations settings page', function () {
    [$user] = settingsSetup();

    $this->actingAs($user)
        ->get('/owner/settings/integrations')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Settings/Integrations')
            ->has('settings')
        );
});

test('integrations settings page masks existing secret keys', function () {
    [$user, $org] = settingsSetup();
    OrganizationSetting::factory()->create([
        'organization_id'   => $org->id,
        'stripe_secret_key' => 'sk_test_abc1234567890xyz',
    ]);

    $response = $this->actingAs($user)
        ->get('/owner/settings/integrations');

    $response->assertInertia(fn ($page) => $page
        ->where('settings.stripe_secret_key', fn ($value) =>
            str_contains($value ?? '', '•')
        )
    );
});

test('integrations settings page does not expose raw secret keys', function () {
    [$user, $org] = settingsSetup();
    $rawKey = 'sk_test_super_secret_key_12345';
    OrganizationSetting::factory()->create([
        'organization_id'   => $org->id,
        'stripe_secret_key' => $rawKey,
    ]);

    $this->actingAs($user)
        ->get('/owner/settings/integrations')
        ->assertInertia(fn ($page) => $page
            ->where('settings.stripe_secret_key', fn ($value) =>
                ! str_contains($value ?? '', 'sk_test_super_secret_key')
            )
        );
});

// ── Integrations Settings — Update ────────────────────────────────────────────

test('user can save integration keys', function () {
    [$user, $org] = settingsSetup();

    $this->actingAs($user)
        ->post('/owner/settings/integrations', [
            'stripe_publishable_key' => 'pk_test_abc123',
            'sendgrid_from_email'    => 'noreply@acme.com',
            'twilio_account_sid'     => 'ACdeadbeef',
            'twilio_from_number'     => '+15550001234',
        ])
        ->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->stripe_publishable_key)->toBe('pk_test_abc123');
    expect($settings->twilio_from_number)->toBe('+15550001234');
});

test('submitting a masked placeholder does not overwrite the stored key', function () {
    [$user, $org] = settingsSetup();
    OrganizationSetting::factory()->create([
        'organization_id'   => $org->id,
        'stripe_secret_key' => 'sk_live_original_key_value',
    ]);

    // Simulate submitting the masked value back unchanged
    $this->actingAs($user)
        ->post('/owner/settings/integrations', [
            'stripe_secret_key' => '••••••••••••••••••••••xyz',
        ])
        ->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->stripe_secret_key)->toBe('sk_live_original_key_value');
});

test('integration settings update rejects invalid sendgrid_from_email', function () {
    [$user] = settingsSetup();

    $this->actingAs($user)
        ->post('/owner/settings/integrations', ['sendgrid_from_email' => 'not-an-email'])
        ->assertSessionHasErrors('sendgrid_from_email');
});

test('integration settings are scoped to the user organization', function () {
    [$user, $org]           = settingsSetup();
    [$otherUser, $otherOrg] = settingsSetup();

    $this->actingAs($user)->post('/owner/settings/integrations', [
        'stripe_publishable_key' => 'pk_mine',
    ]);
    $this->actingAs($otherUser)->post('/owner/settings/integrations', [
        'stripe_publishable_key' => 'pk_theirs',
    ]);

    expect(OrganizationSetting::where('organization_id', $org->id)->value('stripe_publishable_key'))
        ->toBe('pk_mine');
    expect(OrganizationSetting::where('organization_id', $otherOrg->id)->value('stripe_publishable_key'))
        ->toBe('pk_theirs');
});

// ── OrganizationSetting Model ─────────────────────────────────────────────────

test('OrganizationSetting mask returns null for null input', function () {
    expect(OrganizationSetting::mask(null))->toBeNull();
});

test('OrganizationSetting mask obscures all but the last 4 characters', function () {
    $masked = OrganizationSetting::mask('sk_test_abc1234');
    expect($masked)->toEndWith('1234');
    expect(str_contains($masked, '•'))->toBeTrue();
    expect(str_contains($masked, 'sk_test_abc'))->toBeFalse();
});

test('OrganizationSetting mask handles short strings without crashing', function () {
    $masked = OrganizationSetting::mask('abc');
    expect($masked)->toBe('•••');
});

test('OrganizationSetting secrets are encrypted in the database', function () {
    [$user, $org] = settingsSetup();

    $this->actingAs($user)
        ->post('/owner/settings/integrations', [
            'stripe_secret_key' => 'sk_test_plaintext_value',
        ]);

    // Query raw DB value — should not contain the plaintext
    $rawValue = \Illuminate\Support\Facades\DB::table('organization_settings')
        ->where('organization_id', $org->id)
        ->value('stripe_secret_key');

    expect($rawValue)->not->toContain('sk_test_plaintext_value');
    expect($rawValue)->not->toBeNull();
});
