<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientConversationSetting extends Model
{
    protected $fillable = [
        'client_id',
        'business_name',
        'short_name',
        'industry_niche',
        'website_url',
        'primary_market',
        'assistant_name',
        'target_customer',
        'ideal_lead_description',
        'poor_fit_description',
        'primary_goal',
        'secondary_goal',
        'main_services',
        'service_aliases',
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
        'qualification_fields',
        'required_handoff_fields',
        'optional_handoff_fields',
        'urgency_rules',
        'emergency_keywords',
        'objection_handling_notes',
        'trust_building_points',
        'differentiators',
        'booking_rules',
        'pricing_rules',
        'guarantee_rules',
        'human_handoff_rules',
        'refusal_rules',
    ];

    protected function casts(): array
    {
        return [
            'services_not_offered' => 'array',
            'lead_scoring_rules' => 'array',
            'appointment_booking_flow' => 'array',
            'conversation_style_rules' => 'array',
            'main_services' => 'array',
            'service_aliases' => 'array',
            'closing_messages' => 'array',
            'kb_categories' => 'array',
            'faq_categories' => 'array',
            'reasoning_rules' => 'array',
            'tone_examples' => 'array',
            'preferred_phrases' => 'array',
            'forbidden_phrases' => 'array',
            'conversation_examples' => 'array',
            'qualification_fields' => 'array',
            'required_handoff_fields' => 'array',
            'optional_handoff_fields' => 'array',
            'urgency_rules' => 'array',
            'emergency_keywords' => 'array',
            'objection_handling_notes' => 'array',
            'trust_building_points' => 'array',
            'differentiators' => 'array',
            'booking_rules' => 'array',
            'pricing_rules' => 'array',
            'guarantee_rules' => 'array',
            'human_handoff_rules' => 'array',
            'refusal_rules' => 'array',
        ];
    }
}
