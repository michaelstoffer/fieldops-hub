<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('event', 50);     // 'job_scheduled', 'job_reminder', 'en_route', 'job_completed'
            $table->string('channel', 20);   // 'email' | 'sms'
            $table->string('subject')->nullable(); // email only
            $table->text('body');            // supports {{variable}} placeholders
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['organization_id', 'event', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
