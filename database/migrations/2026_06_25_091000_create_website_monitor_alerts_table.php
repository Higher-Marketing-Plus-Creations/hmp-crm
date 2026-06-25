<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_monitor_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->cascadeOnDelete();
            $table->foreignId('website_monitor_check_id')->nullable()->constrained('website_monitor_checks')->nullOnDelete();
            $table->string('type', 32);
            $table->string('state_key', 100);
            $table->string('state_label')->nullable();
            $table->json('recipients')->nullable();
            $table->string('send_status', 32)->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['website_id', 'type', 'state_key']);
            $table->index(['website_id', 'type', 'resolved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_monitor_alerts');
    }
};
