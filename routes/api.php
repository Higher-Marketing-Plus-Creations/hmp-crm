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

Route::middleware(['throttle:lead-submissions', EnsureValidWebsiteApiRequest::class])->group(function () {
    Route::options('/leads/submit', [LeadSubmissionController::class, 'preflight'])->name('api.leads.submit');
    Route::post('/leads/submit', [LeadSubmissionController::class, 'store']);
});
