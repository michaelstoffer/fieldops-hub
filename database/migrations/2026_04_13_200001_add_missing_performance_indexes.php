<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additional performance indexes identified in audit.
 *
 * users (organization_id) — used on every request in HandleInertiaRequests
 *   to load the org relationship, and in TeamController, PlanService
 *   technicianCount/atTechnicianLimit queries.
 *
 *   Note: the foreign key constraint alone does NOT create an index in MySQL/SQLite;
 *   a covering index is needed for efficient lookups.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('organization_id', 'users_organization_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_organization_id_idx');
        });
    }
};
