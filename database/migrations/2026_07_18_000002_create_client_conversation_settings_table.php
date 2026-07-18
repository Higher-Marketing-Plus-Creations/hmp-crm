<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_conversation_settings', function (Blueprint $table) {
            $table->id();
            $table->text('client_id');
            $table->text('target_customer')->nullable();
            $table->text('ideal_lead_description')->nullable();
            $table->text('poor_fit_description')->nullable();
            $table->text('primary_goal')->nullable();
            $table->text('secondary_goal')->nullable();
            $table->json('main_services')->nullable();
            $table->json('service_aliases')->nullable();
            $table->json('qualification_fields')->nullable();
            $table->json('required_handoff_fields')->nullable();
            $table->json('optional_handoff_fields')->nullable();
            $table->json('urgency_rules')->nullable();
            $table->json('emergency_keywords')->nullable();
            $table->json('objection_handling_notes')->nullable();
            $table->json('trust_building_points')->nullable();
            $table->json('differentiators')->nullable();
            $table->json('booking_rules')->nullable();
            $table->json('pricing_rules')->nullable();
            $table->json('guarantee_rules')->nullable();
            $table->json('human_handoff_rules')->nullable();
            $table->json('refusal_rules')->nullable();
            $table->timestampTz('created_at')->nullable()->useCurrent();
            $table->timestampTz('updated_at')->nullable();
        });

        Schema::table('client_conversation_settings', function (Blueprint $table) {
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_conversation_settings');
    }
};
