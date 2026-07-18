<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Error logs fetched successfully.', 'data' => ErrorLog::latest('created_at')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'string'],
            'session_id' => ['nullable', 'string'],
            'lead_id' => ['nullable', 'integer'],
            'workflow_name' => ['nullable', 'string'],
            'workflow_id' => ['nullable', 'string'],
            'execution_id' => ['nullable', 'string'],
            'failed_node' => ['nullable', 'string'],
            'error_message' => ['nullable', 'string'],
            'error_stack' => ['nullable', 'string'],
            'last_user_message' => ['nullable', 'string'],
            'page_url' => ['nullable', 'string'],
            'page_title' => ['nullable', 'string'],
            'severity' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'raw_error' => ['nullable', 'array'],
        ]);

        $data['created_at'] = now();
        $model = ErrorLog::create($data);

        return response()->json(['success' => true, 'message' => 'Error log created successfully.', 'data' => $model], 201);
    }

    public function show(ErrorLog $errorLog): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Error log fetched successfully.', 'data' => $errorLog]);
    }

    public function update(Request $request, ErrorLog $errorLog): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'string'],
            'session_id' => ['nullable', 'string'],
            'lead_id' => ['nullable', 'integer'],
            'workflow_name' => ['nullable', 'string'],
            'workflow_id' => ['nullable', 'string'],
            'execution_id' => ['nullable', 'string'],
            'failed_node' => ['nullable', 'string'],
            'error_message' => ['nullable', 'string'],
            'error_stack' => ['nullable', 'string'],
            'last_user_message' => ['nullable', 'string'],
            'page_url' => ['nullable', 'string'],
            'page_title' => ['nullable', 'string'],
            'severity' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'raw_error' => ['nullable', 'array'],
        ]);

        $errorLog->update($data);

        return response()->json(['success' => true, 'message' => 'Error log updated successfully.', 'data' => $errorLog->fresh()]);
    }

    public function destroy(ErrorLog $errorLog): JsonResponse
    {
        $errorLog->delete();

        return response()->json(['success' => true, 'message' => 'Error log deleted successfully.', 'data' => null]);
    }
}
