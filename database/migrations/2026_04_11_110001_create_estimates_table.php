<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('field_jobs')->nullOnDelete();
            $table->string('estimate_number')->nullable();
            $table->string('title');
            $table->text('intro')->nullable();        // shown on public page above packages
            $table->text('footer')->nullable();       // shown below packages
            $table->string('status', 50)->default('draft'); // draft|sent|accepted|declined|expired
            $table->string('token', 64)->unique();    // public access token
            $table->date('expires_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->string('accepted_package')->nullable(); // 'good'|'better'|'best'
            $table->timestamp('declined_at')->nullable();
            $table->decimal('tax_rate', 5, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'estimate_number']);
            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
