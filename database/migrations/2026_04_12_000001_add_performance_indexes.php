<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance indexes for the most-hit dashboard and dispatch queries.
 *
 * Dashboard:
 *  - invoices (organization_id, paid_at)  → revenue-this-week sum
 *  - invoices (organization_id, due_at)   → overdue invoice lookups
 *  - field_jobs (assigned_to, status, scheduled_at)  → unassigned / open jobs
 *
 * Dispatch live map:
 *  - driver_locations (user_id, id DESC)  → latest-location-per-tech sub-query
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['organization_id', 'paid_at'], 'invoices_org_paid_at_idx');
            $table->index(['organization_id', 'due_at'], 'invoices_org_due_at_idx');
        });

        Schema::table('field_jobs', function (Blueprint $table) {
            $table->index(['assigned_to', 'status', 'scheduled_at'], 'field_jobs_assigned_status_sched_idx');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_org_paid_at_idx');
            $table->dropIndex('invoices_org_due_at_idx');
        });

        Schema::table('field_jobs', function (Blueprint $table) {
            $table->dropIndex('field_jobs_assigned_status_sched_idx');
        });
    }
};
