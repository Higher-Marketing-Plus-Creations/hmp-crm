<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_settings', function (Blueprint $table) {
            $table->id();
            $table->text('client_id')->unique();
            $table->string('business_name');
            $table->string('industry')->nullable();
            $table->string('assistant_name')->nullable();
            $table->string('primary_cta')->nullable();
            $table->string('booking_url')->nullable();
            $table->string('tone')->nullable()->default('friendly, professional, concise');
            $table->text('fallback_message')->nullable();
            $table->text('qualification_question')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_settings');
    }
};
