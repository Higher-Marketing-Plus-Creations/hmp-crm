<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversationLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadCrudController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Leads fetched successfully.',
            'data' => ConversationLead::latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request, false);
        $lead = ConversationLead::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully.',
            'data' => $lead,
        ], 201);
    }

    public function show(ConversationLead $lead): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Lead fetched successfully.',
            'data' => $lead,
        ]);
    }

    public function update(Request $request, ConversationLead $lead): JsonResponse
    {
        $data = $this->validatePayload($request, true);
        $lead->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully.',
            'data' => $lead->fresh(),
        ]);
    }

    public function destroy(ConversationLead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully.',
            'data' => null,
        ]);
    }

    private function validatePayload(Request $request, bool $partial): array
    {
        $rules = [
            'session_id' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'full_name' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'email' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'phone' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'intent' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'source_page' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'notes' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'client_id' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'business_name' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'website_url' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'service_interest' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'custom_data' => [$partial ? 'sometimes' : 'nullable', 'array'],
            'emailed_at' => [$partial ? 'sometimes' : 'nullable'],
            'status' => [$partial ? 'sometimes' : 'nullable', 'string'],
        ];

        return $request->validate($rules);
    }
}
