<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realtime_sessions', function (Blueprint $table) {
            $table->id();
            $table->text('client_id');
            $table->text('session_id');
            $table->text('current_url')->nullable();
            $table->text('page_title')->nullable();
            $table->text('realtime_session_id')->nullable()->index();
            $table->text('client_secret')->nullable();
            $table->unsignedBigInteger('expires_at')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();

            $table->index(['client_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realtime_sessions');
    }
};
