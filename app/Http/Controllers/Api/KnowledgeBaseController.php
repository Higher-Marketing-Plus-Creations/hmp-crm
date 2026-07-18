<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Knowledge base entries fetched successfully.', 'data' => KnowledgeBase::latest('created_at')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'string'],
            'section_title' => ['nullable', 'string'],
            'section_type' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'updated_at' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['created_at'] = now();
        $model = KnowledgeBase::create($data);

        return response()->json(['success' => true, 'message' => 'Knowledge base entry created successfully.', 'data' => $model], 201);
    }

    public function show(KnowledgeBase $knowledgeBase): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Knowledge base entry fetched successfully.', 'data' => $knowledgeBase]);
    }

    public function update(Request $request, KnowledgeBase $knowledgeBase): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'string'],
            'section_title' => ['nullable', 'string'],
            'section_type' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'updated_at' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $knowledgeBase->update($data);

        return response()->json(['success' => true, 'message' => 'Knowledge base entry updated successfully.', 'data' => $knowledgeBase->fresh()]);
    }

    public function destroy(KnowledgeBase $knowledgeBase): JsonResponse
    {
        $knowledgeBase->delete();

        return response()->json(['success' => true, 'message' => 'Knowledge base entry deleted successfully.', 'data' => null]);
    }
}
