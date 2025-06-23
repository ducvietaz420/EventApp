<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\AiSuggestionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Health check route for Railway
Route::get('/up', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect trang chủ
Route::get('/', function () {
    return redirect()->route('events.index');
});



// Protected Routes (cần đăng nhập)
Route::middleware(['auth', 'check.user.status'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [EventController::class, 'dashboard'])->name('dashboard');
    
    // User Management Routes 
    Route::get('users', [UserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->middleware('permission:users.create')->name('users.create');
    Route::post('users', [UserController::class, 'store'])->middleware('permission:users.create')->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])->middleware('permission:users.view')->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users.edit')->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('users.destroy');
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:users.edit')->name('users.toggle-status');
    Route::get('users/{user}/permissions', [UserController::class, 'permissions'])->middleware('permission:users.permissions')->name('users.permissions');
    Route::put('users/{user}/permissions', [UserController::class, 'updatePermissions'])->middleware('permission:users.permissions')->name('users.permissions.update');

    // Event Management Routes
    Route::get('events/export/excel', [EventController::class, 'exportEvents'])->middleware('permission:events.export')->name('events.export');
    Route::get('events/{event}/export/detail', [EventController::class, 'exportEventDetail'])->middleware('permission:events.export')->name('events.export.detail');
    
    // Events CRUD with permissions
    Route::get('events', [EventController::class, 'index'])->middleware('permission:events.view')->name('events.index');
    Route::get('events/create', [EventController::class, 'create'])->middleware('permission:events.create')->name('events.create');
    Route::post('events', [EventController::class, 'store'])->middleware('permission:events.create')->name('events.store');
    Route::get('events/{event}', [EventController::class, 'show'])->middleware('permission:events.view')->name('events.show');
    Route::get('events/{event}/edit', [EventController::class, 'edit'])->middleware('permission:events.edit')->name('events.edit');
    Route::put('events/{event}', [EventController::class, 'update'])->middleware('permission:events.edit')->name('events.update');
    Route::delete('events/{event}', [EventController::class, 'destroy'])->middleware('permission:events.delete')->name('events.destroy');
    
    Route::patch('events/{event}/status', [EventController::class, 'updateStatus'])->middleware('permission:events.edit')->name('events.updateStatus');

    // Event Images Management Routes
    Route::prefix('events/{event}/images')->group(function () {
        Route::get('/', [EventController::class, 'imagesIndex'])->middleware('permission:events.images.view')->name('events.images.index');
        Route::post('/upload', [EventController::class, 'uploadImages'])->middleware('permission:events.images.upload')->name('events.images.upload');
        Route::delete('/{image}', [EventController::class, 'deleteImage'])->middleware('permission:events.images.delete')->name('events.images.delete');
        Route::get('/download-zip', [EventController::class, 'downloadImagesZip'])->middleware('permission:events.images.download')->name('events.images.download-zip');
    });

    // Checklist Management Routes
    Route::get('checklists/export/excel', [ChecklistController::class, 'exportChecklists'])->middleware('permission:checklists.export')->name('checklists.export');
    Route::post('checklists/reorder', [ChecklistController::class, 'reorder'])->middleware('permission:checklists.edit')->name('checklists.reorder');
    Route::post('checklists/{checklist}/duplicate', [ChecklistController::class, 'duplicate'])->middleware('permission:checklists.create')->name('checklists.duplicate');
    
    // Checklists CRUD with permissions
    Route::get('checklists', [ChecklistController::class, 'index'])->middleware('permission:checklists.view')->name('checklists.index');
    Route::get('checklists/create', [ChecklistController::class, 'create'])->middleware('permission:checklists.create')->name('checklists.create');
    Route::post('checklists', [ChecklistController::class, 'store'])->middleware('permission:checklists.create')->name('checklists.store');
    Route::get('checklists/{checklist}', [ChecklistController::class, 'show'])->middleware('permission:checklists.view')->name('checklists.show');
    Route::get('checklists/{checklist}/edit', [ChecklistController::class, 'edit'])->middleware('permission:checklists.edit')->name('checklists.edit');
    Route::put('checklists/{checklist}', [ChecklistController::class, 'update'])->middleware('permission:checklists.edit')->name('checklists.update');
    Route::delete('checklists/{checklist}', [ChecklistController::class, 'destroy'])->middleware('permission:checklists.delete')->name('checklists.destroy');
    
    Route::patch('checklists/{checklist}/status', [ChecklistController::class, 'updateStatus'])->middleware('permission:checklists.complete')->name('checklists.updateStatus');
    Route::prefix('events/{event}/checklists')->group(function () {
        Route::get('/', [ChecklistController::class, 'index'])->middleware('permission:checklists.view')->name('events.checklists.index');
        Route::get('/create', [ChecklistController::class, 'create'])->middleware('permission:checklists.create')->name('events.checklists.create');
        Route::post('/', [ChecklistController::class, 'store'])->middleware('permission:checklists.create')->name('events.checklists.store');
        Route::patch('/{checklist}/complete', [ChecklistController::class, 'complete'])->middleware('permission:checklists.complete')->name('events.checklists.complete');
    });

    // AI Suggestion Routes
    Route::get('ai-suggestions', [AiSuggestionController::class, 'index'])->middleware('permission:ai_suggestions.view')->name('ai-suggestions.index');
    Route::get('ai-suggestions/create', [AiSuggestionController::class, 'create'])->middleware('permission:ai_suggestions.create')->name('ai-suggestions.create');
    Route::post('ai-suggestions', [AiSuggestionController::class, 'store'])->middleware('permission:ai_suggestions.create')->name('ai-suggestions.store');
    Route::get('ai-suggestions/{aiSuggestion}', [AiSuggestionController::class, 'show'])->middleware('permission:ai_suggestions.view')->name('ai-suggestions.show');
    Route::get('ai-suggestions/{aiSuggestion}/edit', [AiSuggestionController::class, 'edit'])->middleware('permission:ai_suggestions.edit')->name('ai-suggestions.edit');
    Route::put('ai-suggestions/{aiSuggestion}', [AiSuggestionController::class, 'update'])->middleware('permission:ai_suggestions.edit')->name('ai-suggestions.update');
    Route::delete('ai-suggestions/{aiSuggestion}', [AiSuggestionController::class, 'destroy'])->middleware('permission:ai_suggestions.delete')->name('ai-suggestions.destroy');
    
    Route::post('ai-suggestions/generate', [AiSuggestionController::class, 'generate'])->middleware('permission:ai_suggestions.create')->name('ai-suggestions.generate');
    Route::patch('ai-suggestions/{aiSuggestion}/status', [AiSuggestionController::class, 'updateStatus'])->middleware('permission:ai_suggestions.accept,ai_suggestions.reject')->name('ai-suggestions.updateStatus');
    Route::patch('ai-suggestions/{aiSuggestion}/rate', [AiSuggestionController::class, 'rate'])->middleware('permission:ai_suggestions.view')->name('ai-suggestions.rate');
    Route::patch('ai-suggestions/{aiSuggestion}/favorite', [AiSuggestionController::class, 'toggleFavorite'])->middleware('permission:ai_suggestions.view')->name('ai-suggestions.toggleFavorite');
    Route::prefix('events/{event}/ai-suggestions')->group(function () {
        Route::get('/', [AiSuggestionController::class, 'index'])->middleware('permission:ai_suggestions.view')->name('events.ai-suggestions.index');
        Route::post('/generate', [AiSuggestionController::class, 'generate'])->middleware('permission:ai_suggestions.create')->name('events.ai-suggestions.generate');
        Route::post('/{aiSuggestion}/accept', [AiSuggestionController::class, 'accept'])->middleware('permission:ai_suggestions.accept')->name('events.ai-suggestions.accept');
        Route::post('/{aiSuggestion}/reject', [AiSuggestionController::class, 'reject'])->middleware('permission:ai_suggestions.reject')->name('events.ai-suggestions.reject');
    });

    // Activity Logs Routes (chỉ admin)
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->middleware('permission:activity_logs.view_all')->name('activity-logs.index');
    Route::get('users/{user}/activities', [ActivityLogController::class, 'userActivities'])->middleware('permission:activity_logs.view_all')->name('activity-logs.user-activities');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->middleware('permission:activity_logs.view_all')->name('activity-logs.show');
    Route::post('activity-logs/cleanup', [ActivityLogController::class, 'cleanup'])->middleware('permission:activity_logs.cleanup')->name('activity-logs.cleanup');

    // API Routes for AJAX calls
    Route::prefix('api')->group(function () {
        Route::get('/events/{event}/dashboard', [EventController::class, 'dashboard'])->name('api.events.dashboard');
        Route::get('/events/{event}/progress', [EventController::class, 'getProgress'])->name('api.events.progress');
    });
});
