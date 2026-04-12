<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('field_jobs')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('channel', 20);   // 'email' | 'sms'
            $table->string('event', 50);     // 'job_scheduled', 'job_reminder', 'en_route', 'job_completed'
            $table->string('recipient');     // email address or phone number
            $table->text('body');
            $table->string('status', 20)->default('sent'); // 'sent' | 'failed'
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['job_id', 'channel']);
            $table->index(['customer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_messages');
    }
};
