<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimate_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->constrained()->cascadeOnDelete();
            $table->string('tier', 20); // 'good' | 'better' | 'best'
            $table->string('label');    // display name, e.g. "Basic", "Standard", "Premium"
            $table->text('description')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('is_recommended')->default(false);
            $table->timestamps();

            $table->unique(['estimate_id', 'tier']);
            $table->index('estimate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimate_packages');
    }
};
