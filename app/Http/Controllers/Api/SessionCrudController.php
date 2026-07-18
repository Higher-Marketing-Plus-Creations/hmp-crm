<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversationSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionCrudController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Sessions fetched successfully.',
            'data' => ConversationSession::latest('id')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request, false);
        $session = ConversationSession::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Session created successfully.',
            'data' => $session,
        ], 201);
    }

    public function show(ConversationSession $session): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Session fetched successfully.',
            'data' => $session,
        ]);
    }

    public function update(Request $request, ConversationSession $session): JsonResponse
    {
        $data = $this->validatePayload($request, true);
        $session->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Session updated successfully.',
            'data' => $session->fresh(),
        ]);
    }

    public function destroy(ConversationSession $session): JsonResponse
    {
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Session deleted successfully.',
            'data' => null,
        ]);
    }

    private function validatePayload(Request $request, bool $partial): array
    {
        $rules = [
            'session_id' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'current_url' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'page_title' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'page_summary' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'intent' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'lead_id' => [$partial ? 'sometimes' : 'nullable', 'integer'],
            'is_active' => [$partial ? 'sometimes' : 'nullable', 'boolean'],
            'last_activity_at' => [$partial ? 'sometimes' : 'nullable'],
            'client_id' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'selected_intent' => [$partial ? 'sometimes' : 'nullable', 'string'],
            'last_event_type' => [$partial ? 'sometimes' : 'nullable', 'string'],
        ];

        return $request->validate($rules);
    }
}
