<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->timestampTz('created_at')->useCurrent();
            $table->text('client_id')->nullable();
            $table->text('section_title')->nullable();
            $table->text('section_type')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('sort_order')->nullable();

            $table->index('client_id');
            $table->index('section_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base');
    }
};
