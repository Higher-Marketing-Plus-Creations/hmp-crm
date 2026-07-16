<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientWorkspaceController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Admin\WebsiteMonitorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TwilioRecordingController;
use Illuminate\Support\Facades\Route;
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'create'])->name('login');
    Route::get('/login', [AuthController::class, 'create']);
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
  
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('clients/{client}/workspace', [ClientWorkspaceController::class, 'show'])->name('clients.workspace');
        Route::get('call-recordings', [TwilioRecordingController::class, 'index'])->name('twilio.recordings.index');
        Route::get('call-recordings/{recordingSid}/audio', [TwilioRecordingController::class, 'audio'])->name('twilio.recordings.audio');
        Route::get('call-recordings/{recordingSid}/download', [TwilioRecordingController::class, 'download'])->name('twilio.recordings.download');
        Route::post('websites/{website}/run-test', [WebsiteMonitorController::class, 'store'])->name('websites.run-test');
        Route::get('websites/{website}/posts', [PostController::class, 'index'])->name('websites.posts.index');
        Route::get('websites/{website}/posts/create', [PostController::class, 'create'])->name('websites.posts.create');

        Route::resource('clients', ClientController::class)->except(['show']);
        Route::resource('websites', WebsiteController::class)->except(['show']);
        Route::resource('posts', PostController::class)->except(['show']);

        Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
        Route::post('leads/{lead}/retry-email', [LeadController::class, 'retryEmail'])->name('leads.retry-email');
        Route::patch('leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
        Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');

        Route::get('email-logs', [EmailLogController::class, 'index'])->name('email-logs.index');
    });
});
