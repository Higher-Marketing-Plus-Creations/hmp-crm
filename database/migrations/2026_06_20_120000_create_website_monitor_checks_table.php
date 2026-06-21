<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_monitor_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->string('website_status', 32)->default('unknown');
            $table->string('email_delivery_status', 32)->default('pending');
            $table->unsignedInteger('forms_submitted_this_month')->default(0);
            $table->timestamp('last_successful_form_submitted_at')->nullable();
            $table->unsignedInteger('failed_form_count')->default(0);
            $table->unsignedInteger('site_load_time_ms')->nullable();
            $table->json('issues')->nullable();
            $table->string('run_test_status', 32)->default('completed');
            $table->string('ssl_status', 32)->default('unknown');
            $table->decimal('uptime_percentage', 5, 2)->nullable();
            $table->unsignedSmallInteger('http_status_code')->nullable();
            $table->text('check_summary')->nullable();
            $table->timestamp('tested_at');
            $table->timestamps();

            $table->index(['website_id', 'tested_at']);
            $table->index(['website_id', 'website_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_monitor_checks');
    }
};
