<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->timestampTz('created_at')->useCurrent();
            $table->text('session_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->text('role');
            $table->text('message');
            $table->text('intent')->nullable();
            $table->text('page_url')->nullable();
            $table->text('page_title')->nullable();
            $table->text('collecting_field')->nullable();
            $table->text('client_id')->nullable();
            $table->text('selected_intent')->nullable();
            $table->text('event_type')->nullable();

            $table->index('session_id');
            $table->index('lead_id');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
