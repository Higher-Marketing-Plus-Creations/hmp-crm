<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->text('client_id')->nullable();
            $table->text('session_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->text('workflow_name')->nullable();
            $table->text('workflow_id')->nullable();
            $table->text('execution_id')->nullable();
            $table->text('failed_node')->nullable();
            $table->longText('error_message')->nullable();
            $table->longText('error_stack')->nullable();
            $table->longText('last_user_message')->nullable();
            $table->text('page_url')->nullable();
            $table->text('page_title')->nullable();
            $table->string('severity')->nullable()->default('error');
            $table->string('status')->nullable()->default('open');
            $table->json('raw_error')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();

            $table->index('client_id');
            $table->index('session_id');
            $table->index('lead_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
