<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_type_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_type_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->index(['job_type_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_type_checklist_items');
    }
};
