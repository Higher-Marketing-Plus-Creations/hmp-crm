<?php

use App\Http\Controllers\Api\LeadSubmissionController;
use App\Http\Middleware\EnsureValidWebsiteApiRequest;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:lead-submissions', EnsureValidWebsiteApiRequest::class])->group(function () {
    Route::options('/leads/submit', [LeadSubmissionController::class, 'preflight']);
    Route::post('/leads/submit', [LeadSubmissionController::class, 'store']);
});
