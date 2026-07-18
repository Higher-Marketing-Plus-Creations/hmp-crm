<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientConversationSetting extends Model
{
    protected $fillable = [
        'client_id',
        'target_customer',
        'ideal_lead_description',
        'poor_fit_description',
        'primary_goal',
        'secondary_goal',
        'main_services',
        'service_aliases',
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
            'main_services' => 'array',
            'service_aliases' => 'array',
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
