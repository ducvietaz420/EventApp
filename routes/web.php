<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\AiSuggestionController;
use App\Http\Controllers\EventReportController;

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

// Trang chủ
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard route
Route::get('/dashboard', [EventController::class, 'dashboard'])->name('dashboard');

// Event Management Routes
Route::resource('events', EventController::class);
Route::patch('events/{event}/status', [EventController::class, 'updateStatus'])->name('events.updateStatus');

// Budget Management Routes
Route::resource('budgets', BudgetController::class);
Route::put('budgets/{budget}/update-spent', [BudgetController::class, 'updateSpent'])->name('budgets.update_spent');
Route::prefix('events/{event}/budgets')->group(function () {
    Route::get('/', [BudgetController::class, 'index'])->name('events.budgets.index');
    Route::get('/create', [BudgetController::class, 'create'])->name('events.budgets.create');
    Route::post('/', [BudgetController::class, 'store'])->name('events.budgets.store');
});

// Timeline Management Routes
Route::resource('timelines', TimelineController::class);
Route::patch('timelines/{timeline}/status', [TimelineController::class, 'updateStatus'])->name('timelines.updateStatus');
Route::post('timelines/{timeline}/complete', [TimelineController::class, 'markCompleted'])->name('timelines.complete');
Route::post('timelines/{timeline}/uncomplete', [TimelineController::class, 'markUncompleted'])->name('timelines.uncomplete');
Route::post('timelines/bulk-complete', [TimelineController::class, 'bulkComplete'])->name('timelines.bulk-complete');
Route::post('timelines/bulk-priority', [TimelineController::class, 'bulkPriority'])->name('timelines.bulk-priority');
Route::post('timelines/bulk-delete', [TimelineController::class, 'bulkDelete'])->name('timelines.bulk-delete');
Route::prefix('events/{event}/timelines')->group(function () {
    Route::get('/', [TimelineController::class, 'index'])->name('events.timelines.index');
    Route::get('/create', [TimelineController::class, 'create'])->name('events.timelines.create');
    Route::post('/', [TimelineController::class, 'store'])->name('events.timelines.store');
});

// Supplier Management Routes
Route::resource('suppliers', SupplierController::class);
Route::patch('suppliers/{supplier}/toggle-verified', [SupplierController::class, 'toggleVerified'])->name('suppliers.toggleVerified');
Route::patch('suppliers/{supplier}/toggle-preferred', [SupplierController::class, 'togglePreferred'])->name('suppliers.togglePreferred');
Route::prefix('events/{event}/suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('events.suppliers.index');
    Route::post('/attach', [SupplierController::class, 'attach'])->name('events.suppliers.attach');
    Route::delete('/detach/{supplier}', [SupplierController::class, 'detach'])->name('events.suppliers.detach');
});

// Checklist Management Routes
Route::resource('checklists', ChecklistController::class);
Route::patch('checklists/{checklist}/status', [ChecklistController::class, 'updateStatus'])->name('checklists.updateStatus');
Route::post('checklists/reorder', [ChecklistController::class, 'reorder'])->name('checklists.reorder');
Route::post('checklists/{checklist}/duplicate', [ChecklistController::class, 'duplicate'])->name('checklists.duplicate');
Route::prefix('events/{event}/checklists')->group(function () {
    Route::get('/', [ChecklistController::class, 'index'])->name('events.checklists.index');
    Route::get('/create', [ChecklistController::class, 'create'])->name('events.checklists.create');
    Route::post('/', [ChecklistController::class, 'store'])->name('events.checklists.store');
    Route::patch('/{checklist}/complete', [ChecklistController::class, 'complete'])->name('events.checklists.complete');
});

// AI Suggestion Routes
Route::resource('ai-suggestions', AiSuggestionController::class);
Route::post('ai-suggestions/generate', [AiSuggestionController::class, 'generate'])->name('ai-suggestions.generate');
Route::patch('ai-suggestions/{aiSuggestion}/status', [AiSuggestionController::class, 'updateStatus'])->name('ai-suggestions.updateStatus');
Route::patch('ai-suggestions/{aiSuggestion}/rate', [AiSuggestionController::class, 'rate'])->name('ai-suggestions.rate');
Route::patch('ai-suggestions/{aiSuggestion}/favorite', [AiSuggestionController::class, 'toggleFavorite'])->name('ai-suggestions.toggleFavorite');
Route::prefix('events/{event}/ai-suggestions')->group(function () {
    Route::get('/', [AiSuggestionController::class, 'index'])->name('events.ai-suggestions.index');
    Route::post('/generate', [AiSuggestionController::class, 'generate'])->name('events.ai-suggestions.generate');
    Route::post('/{aiSuggestion}/accept', [AiSuggestionController::class, 'accept'])->name('events.ai-suggestions.accept');
    Route::post('/{aiSuggestion}/reject', [AiSuggestionController::class, 'reject'])->name('events.ai-suggestions.reject');
});

// Event Report Routes
Route::resource('event-reports', EventReportController::class);
Route::patch('event-reports/{eventReport}/status', [EventReportController::class, 'updateStatus'])->name('event-reports.updateStatus');
Route::get('event-reports/{eventReport}/export-pdf', [EventReportController::class, 'exportPdf'])->name('event-reports.exportPdf');
Route::post('event-reports/{eventReport}/duplicate', [EventReportController::class, 'duplicate'])->name('event-reports.duplicate');
Route::prefix('events/{event}/reports')->group(function () {
    Route::get('/', [EventReportController::class, 'index'])->name('events.reports.index');
    Route::get('/create', [EventReportController::class, 'create'])->name('events.reports.create');
    Route::post('/', [EventReportController::class, 'store'])->name('events.reports.store');
    Route::post('/generate', [EventReportController::class, 'generate'])->name('events.reports.generate');
});


// API Routes for AJAX calls
Route::prefix('api')->group(function () {
    Route::get('/events/{event}/dashboard', [EventController::class, 'dashboard'])->name('api.events.dashboard');
    Route::get('/events/{event}/progress', [EventController::class, 'getProgress'])->name('api.events.progress');
    Route::get('/suppliers/search', [SupplierController::class, 'search'])->name('api.suppliers.search');
    Route::get('/budgets/summary', [BudgetController::class, 'summary'])->name('api.budgets.summary');
    Route::get('/timelines/upcoming', [TimelineController::class, 'upcoming'])->name('api.timelines.upcoming');
});
