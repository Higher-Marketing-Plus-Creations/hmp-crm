<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientSettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Client settings fetched successfully.', 'data' => ClientSetting::latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'string'],
            'business_name' => ['required', 'string'],
            'industry' => ['nullable', 'string'],
            'assistant_name' => ['nullable', 'string'],
            'primary_cta' => ['nullable', 'string'],
            'booking_url' => ['nullable', 'string'],
            'tone' => ['nullable', 'string'],
            'fallback_message' => ['nullable', 'string'],
            'qualification_question' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $model = ClientSetting::create($data);

        return response()->json(['success' => true, 'message' => 'Client setting created successfully.', 'data' => $model], 201);
    }

    public function show(ClientSetting $clientSetting): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Client setting fetched successfully.', 'data' => $clientSetting]);
    }

    public function update(Request $request, ClientSetting $clientSetting): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['sometimes', 'required', 'string'],
            'business_name' => ['sometimes', 'required', 'string'],
            'industry' => ['nullable', 'string'],
            'assistant_name' => ['nullable', 'string'],
            'primary_cta' => ['nullable', 'string'],
            'booking_url' => ['nullable', 'string'],
            'tone' => ['nullable', 'string'],
            'fallback_message' => ['nullable', 'string'],
            'qualification_question' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $clientSetting->update($data);

        return response()->json(['success' => true, 'message' => 'Client setting updated successfully.', 'data' => $clientSetting->fresh()]);
    }

    public function destroy(ClientSetting $clientSetting): JsonResponse
    {
        $clientSetting->delete();

        return response()->json(['success' => true, 'message' => 'Client setting deleted successfully.', 'data' => null]);
    }
}
