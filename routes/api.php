<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Api\ClientConversationSettingController;
use App\Http\Controllers\Api\ClientSettingController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\ErrorLogController;
use App\Http\Controllers\Api\LeadSubmissionController;
use App\Http\Controllers\Api\LeadCrudController;
use App\Http\Controllers\Api\KnowledgeBaseController;
use App\Http\Controllers\Api\SessionCrudController;
use App\Http\Controllers\Api\WorkspaceApiController;
use App\Http\Middleware\EnsureValidWebsiteApiRequest;
use Illuminate\Support\Facades\Route;

Route::post('test',function(){
    return response()->json(['message' => 'OK']);

});

Route::get('/posts/widget', [PostController::class, 'widget']);
Route::get('/posts/widget-script', [PostController::class, 'widgetScript']);
Route::get('/posts/detail', [PostController::class, 'detail']);

Route::apiResource('client-settings', ClientSettingController::class);
Route::apiResource('client-conversation-settings', ClientConversationSettingController::class);
Route::apiResource('conversations', ConversationController::class);
Route::apiResource('error-logs', ErrorLogController::class);
Route::apiResource('knowledge-base', KnowledgeBaseController::class);
Route::apiResource('leads', LeadCrudController::class);
Route::apiResource('sessions', SessionCrudController::class);

Route::prefix('workspaces/{client_id}')->group(function () {
    Route::get('overview', [WorkspaceApiController::class, 'overview']);
    Route::get('settings', [WorkspaceApiController::class, 'settings']);
    Route::get('conversation-settings', [WorkspaceApiController::class, 'conversationSettings']);
    Route::get('knowledge-base', [WorkspaceApiController::class, 'knowledgeBase']);
    Route::get('sessions', [WorkspaceApiController::class, 'sessions']);
    Route::get('conversations', [WorkspaceApiController::class, 'conversations']);
    Route::get('leads', [WorkspaceApiController::class, 'leads']);
    Route::get('error-logs', [WorkspaceApiController::class, 'errorLogs']);
    Route::post('bootstrap', [WorkspaceApiController::class, 'bootstrap']);
    Route::post('conversation-start', [WorkspaceApiController::class, 'conversationStart']);
    Route::post('conversation-event', [WorkspaceApiController::class, 'conversationEvent']);
});

Route::get('workspace-api-map', [WorkspaceApiController::class, 'docs']);

Route::middleware(['throttle:lead-submissions', EnsureValidWebsiteApiRequest::class])->group(function () {
    Route::options('/leads/submit', [LeadSubmissionController::class, 'preflight'])->name('api.leads.submit');
    Route::post('/leads/submit', [LeadSubmissionController::class, 'store']);
});
