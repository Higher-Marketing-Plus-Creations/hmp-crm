<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_leads', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 191)->nullable()->index();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('intent')->nullable();
            $table->string('source_page')->nullable();
            $table->string('status')->default('new');
            $table->longText('notes')->nullable();
            $table->string('client_id', 191)->nullable()->index();
            $table->string('business_name')->nullable();
            $table->string('website_url')->nullable();
            $table->string('service_interest')->nullable();
            $table->json('custom_data')->nullable();
            $table->timestampTz('emailed_at')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_leads');
    }
};
