<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // The plan the owner selected at registration
            $table->string('plan')->default('growth')->after('timezone');
            // trial_ends_at is set at registration; null means no trial (already subscribed)
            $table->timestamp('trial_ends_at')->nullable()->after('plan');
            // Stripe customer ID for this org
            $table->string('stripe_customer_id')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['plan', 'trial_ends_at', 'stripe_customer_id']);
        });
    }
};
