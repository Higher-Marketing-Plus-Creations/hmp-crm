<?php

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Api\LeadSubmissionController;
use App\Http\Middleware\EnsureValidWebsiteApiRequest;
use Illuminate\Support\Facades\Route;

Route::post('test',function(){
    return response()->json(['message' => 'OK']);

});

Route::get('/posts/widget', [PostController::class, 'widget']);
Route::get('/posts/widget-script', [PostController::class, 'widgetScript']);
Route::get('/posts/detail', [PostController::class, 'detail']);

Route::middleware(['throttle:lead-submissions', EnsureValidWebsiteApiRequest::class])->group(function () {
    Route::options('/leads/submit', [LeadSubmissionController::class, 'preflight'])->name('api.leads.submit');
    Route::post('/leads/submit', [LeadSubmissionController::class, 'store']);
});
