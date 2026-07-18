<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 191)->unique();
            $table->text('current_url')->nullable();
            $table->text('page_title')->nullable();
            $table->longText('page_summary')->nullable();
            $table->text('intent')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestampTz('last_activity_at')->nullable();
            $table->string('client_id', 191)->nullable()->index();
            $table->text('selected_intent')->nullable();
            $table->text('last_event_type')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_sessions');
    }
};
