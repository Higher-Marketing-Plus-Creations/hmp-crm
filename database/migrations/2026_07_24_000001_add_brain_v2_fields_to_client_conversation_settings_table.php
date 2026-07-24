<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_conversation_settings', function (Blueprint $table) {
            $table->text('business_name')->nullable()->after('client_id');
            $table->text('short_name')->nullable()->after('business_name');
            $table->text('industry_niche')->nullable()->after('short_name');
            $table->text('website_url')->nullable()->after('industry_niche');
            $table->text('primary_market')->nullable()->after('website_url');
            $table->text('assistant_name')->nullable()->after('primary_market');
            $table->json('services_not_offered')->nullable()->after('assistant_name');
            $table->json('lead_scoring_rules')->nullable()->after('services_not_offered');
            $table->json('appointment_booking_flow')->nullable()->after('lead_scoring_rules');
            $table->json('conversation_style_rules')->nullable()->after('appointment_booking_flow');
            $table->text('first_message')->nullable()->after('conversation_style_rules');
            $table->json('closing_messages')->nullable()->after('first_message');
            $table->json('kb_categories')->nullable()->after('closing_messages');
            $table->json('faq_categories')->nullable()->after('kb_categories');
            $table->json('reasoning_rules')->nullable()->after('faq_categories');
            $table->text('handoff_summary_template')->nullable()->after('reasoning_rules');
            $table->json('tone_examples')->nullable()->after('handoff_summary_template');
            $table->json('preferred_phrases')->nullable()->after('tone_examples');
            $table->json('forbidden_phrases')->nullable()->after('preferred_phrases');
            $table->json('conversation_examples')->nullable()->after('forbidden_phrases');
        });
    }

    public function down(): void
    {
        Schema::table('client_conversation_settings', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'short_name',
                'industry_niche',
                'website_url',
                'primary_market',
                'assistant_name',
                'services_not_offered',
                'lead_scoring_rules',
                'appointment_booking_flow',
                'conversation_style_rules',
                'first_message',
                'closing_messages',
                'kb_categories',
                'faq_categories',
                'reasoning_rules',
                'handoff_summary_template',
                'tone_examples',
                'preferred_phrases',
                'forbidden_phrases',
                'conversation_examples',
            ]);
        });
    }
};
