<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientConversationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientConversationSettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Conversation settings fetched successfully.', 'data' => ClientConversationSetting::latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'string'],
            'target_customer' => ['nullable', 'string'],
            'ideal_lead_description' => ['nullable', 'string'],
            'poor_fit_description' => ['nullable', 'string'],
            'primary_goal' => ['nullable', 'string'],
            'secondary_goal' => ['nullable', 'string'],
            'main_services' => ['nullable', 'array'],
            'service_aliases' => ['nullable', 'array'],
            'qualification_fields' => ['nullable', 'array'],
            'required_handoff_fields' => ['nullable', 'array'],
            'optional_handoff_fields' => ['nullable', 'array'],
            'urgency_rules' => ['nullable', 'array'],
            'emergency_keywords' => ['nullable', 'array'],
            'objection_handling_notes' => ['nullable', 'array'],
            'trust_building_points' => ['nullable', 'array'],
            'differentiators' => ['nullable', 'array'],
            'booking_rules' => ['nullable', 'array'],
            'pricing_rules' => ['nullable', 'array'],
            'guarantee_rules' => ['nullable', 'array'],
            'human_handoff_rules' => ['nullable', 'array'],
            'refusal_rules' => ['nullable', 'array'],
        ]);

        $model = ClientConversationSetting::create($data);

        return response()->json(['success' => true, 'message' => 'Conversation setting created successfully.', 'data' => $model], 201);
    }

    public function show(ClientConversationSetting $clientConversationSetting): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Conversation setting fetched successfully.', 'data' => $clientConversationSetting]);
    }

    public function update(Request $request, ClientConversationSetting $clientConversationSetting): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['sometimes', 'required', 'string'],
            'target_customer' => ['nullable', 'string'],
            'ideal_lead_description' => ['nullable', 'string'],
            'poor_fit_description' => ['nullable', 'string'],
            'primary_goal' => ['nullable', 'string'],
            'secondary_goal' => ['nullable', 'string'],
            'main_services' => ['nullable', 'array'],
            'service_aliases' => ['nullable', 'array'],
            'qualification_fields' => ['nullable', 'array'],
            'required_handoff_fields' => ['nullable', 'array'],
            'optional_handoff_fields' => ['nullable', 'array'],
            'urgency_rules' => ['nullable', 'array'],
            'emergency_keywords' => ['nullable', 'array'],
            'objection_handling_notes' => ['nullable', 'array'],
            'trust_building_points' => ['nullable', 'array'],
            'differentiators' => ['nullable', 'array'],
            'booking_rules' => ['nullable', 'array'],
            'pricing_rules' => ['nullable', 'array'],
            'guarantee_rules' => ['nullable', 'array'],
            'human_handoff_rules' => ['nullable', 'array'],
            'refusal_rules' => ['nullable', 'array'],
        ]);

        $clientConversationSetting->update($data);

        return response()->json(['success' => true, 'message' => 'Conversation setting updated successfully.', 'data' => $clientConversationSetting->fresh()]);
    }

    public function destroy(ClientConversationSetting $clientConversationSetting): JsonResponse
    {
        $clientConversationSetting->delete();

        return response()->json(['success' => true, 'message' => 'Conversation setting deleted successfully.', 'data' => null]);
    }
}
