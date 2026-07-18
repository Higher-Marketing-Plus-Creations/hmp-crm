<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Conversations fetched successfully.', 'data' => Conversation::latest('created_at')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['nullable', 'string'],
            'lead_id' => ['nullable', 'integer'],
            'role' => ['required', 'string'],
            'message' => ['required', 'string'],
            'intent' => ['nullable', 'string'],
            'page_url' => ['nullable', 'string'],
            'page_title' => ['nullable', 'string'],
            'collecting_field' => ['nullable', 'string'],
            'client_id' => ['nullable', 'string'],
            'selected_intent' => ['nullable', 'string'],
            'event_type' => ['nullable', 'string'],
        ]);

        $data['created_at'] = now();
        $model = Conversation::create($data);

        return response()->json(['success' => true, 'message' => 'Conversation created successfully.', 'data' => $model], 201);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Conversation fetched successfully.', 'data' => $conversation]);
    }

    public function update(Request $request, Conversation $conversation): JsonResponse
    {
        $data = $request->validate([
            'session_id' => ['nullable', 'string'],
            'lead_id' => ['nullable', 'integer'],
            'role' => ['sometimes', 'required', 'string'],
            'message' => ['sometimes', 'required', 'string'],
            'intent' => ['nullable', 'string'],
            'page_url' => ['nullable', 'string'],
            'page_title' => ['nullable', 'string'],
            'collecting_field' => ['nullable', 'string'],
            'client_id' => ['nullable', 'string'],
            'selected_intent' => ['nullable', 'string'],
            'event_type' => ['nullable', 'string'],
        ]);

        $conversation->update($data);

        return response()->json(['success' => true, 'message' => 'Conversation updated successfully.', 'data' => $conversation->fresh()]);
    }

    public function destroy(Conversation $conversation): JsonResponse
    {
        $conversation->delete();

        return response()->json(['success' => true, 'message' => 'Conversation deleted successfully.', 'data' => null]);
    }
}
