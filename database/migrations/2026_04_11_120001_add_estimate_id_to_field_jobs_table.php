<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_jobs', function (Blueprint $table) {
            $table->foreignId('estimate_id')
                ->nullable()
                ->after('job_type_id')
                ->constrained('estimates')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('field_jobs', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Estimate::class);
            $table->dropColumn('estimate_id');
        });
    }
};
