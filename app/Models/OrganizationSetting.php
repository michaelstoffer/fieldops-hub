<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationSetting extends Model
{
    use HasFactory;

    protected $table = 'organization_settings';

    protected $fillable = [
        'organization_id',
        // Company / branding
        'company_name',
        'company_email',
        'company_phone',
        'company_address',
        'company_city',
        'company_state',
        'company_zip',
        'company_website',
        'logo_path',
        'default_tax_rate',
        // Integrations
        'stripe_secret_key',
        'stripe_publishable_key',
        'stripe_webhook_secret',
        'twilio_auth_token',
        'twilio_account_sid',
        'twilio_from_number',
        'sendgrid_api_key',
        'sendgrid_from_email',
        'google_maps_api_key',
    ];

    protected $hidden = [
        'stripe_secret_key',
        'stripe_webhook_secret',
        'twilio_auth_token',
        'sendgrid_api_key',
        'google_maps_api_key',
    ];

    protected function casts(): array
    {
        return [
            'default_tax_rate'     => 'decimal:4',
            'stripe_secret_key'    => 'encrypted',
            'stripe_webhook_secret' => 'encrypted',
            'twilio_auth_token'    => 'encrypted',
            'sendgrid_api_key'     => 'encrypted',
            'google_maps_api_key'  => 'encrypted',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Returns a masked version of a secret key for display (shows last 4 chars).
     */
    public static function mask(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $len = strlen($value);

        if ($len <= 4) {
            return str_repeat('•', $len);
        }

        return str_repeat('•', $len - 4) . substr($value, -4);
    }
}
