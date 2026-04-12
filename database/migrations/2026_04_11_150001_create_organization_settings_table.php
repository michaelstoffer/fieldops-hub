<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();

            // Company / branding
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state', 10)->nullable();
            $table->string('company_zip', 20)->nullable();
            $table->string('company_website')->nullable();
            $table->string('logo_path')->nullable();
            $table->decimal('default_tax_rate', 6, 4)->default(0);

            // Integration keys (encrypted at rest)
            $table->text('stripe_secret_key')->nullable();
            $table->string('stripe_publishable_key')->nullable();
            $table->string('stripe_webhook_secret')->nullable();
            $table->text('twilio_auth_token')->nullable();
            $table->string('twilio_account_sid')->nullable();
            $table->string('twilio_from_number')->nullable();
            $table->text('sendgrid_api_key')->nullable();
            $table->string('sendgrid_from_email')->nullable();
            $table->text('google_maps_api_key')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_settings');
    }
};
