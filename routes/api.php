<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WorkoutCalendarController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NutritionPlanController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\TrainingCompletionController;
use App\Http\Controllers\Api\ProfilePhotoController;
use App\Http\Controllers\Api\TrainingPlanController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\UserController;

// Todas las rutas API requieren autenticación vía sesión
Route::middleware('auth')->group(function () {

    // === WORKOUT CALENDAR ===
    Route::prefix('calendar')->group(function () {
        Route::get('/workouts',          [WorkoutCalendarController::class, 'index']);
        Route::post('/workouts',         [WorkoutCalendarController::class, 'store']);
        Route::put('/workouts/{id}',     [WorkoutCalendarController::class, 'update']);
        Route::post('/workouts/complete',[WorkoutCalendarController::class, 'markComplete']);
        Route::delete('/workouts/{id}',  [WorkoutCalendarController::class, 'destroy']);
        Route::get('/stats',             [WorkoutCalendarController::class, 'stats']);
    });

    // === GOALS ===
    Route::prefix('goals')->group(function () {
        Route::get('/',                   [GoalController::class, 'index']);
        Route::post('/',                  [GoalController::class, 'store']);
        Route::put('/{id}',               [GoalController::class, 'update']);
        Route::post('/{id}/progress',     [GoalController::class, 'updateProgress']);
        Route::delete('/{id}',            [GoalController::class, 'destroy']);
        Route::get('/achievements',       [GoalController::class, 'achievements']);
    });

    // === MESSAGES ===
    Route::prefix('messages')->group(function () {
        Route::get('/conversations',      [MessageController::class, 'conversations']);
        Route::get('/thread',             [MessageController::class, 'thread']);
        Route::post('/send',              [MessageController::class, 'send']);
        Route::put('/{id}',               [MessageController::class, 'edit']);
        Route::delete('/{id}',            [MessageController::class, 'destroy']);
        Route::get('/unread-count',       [MessageController::class, 'unreadCount']);
    });

    // === NUTRITION ===
    Route::prefix('nutrition')->group(function () {
        Route::get('/meals',              [NutritionPlanController::class, 'index']);
        Route::post('/meals',             [NutritionPlanController::class, 'store']);
        Route::delete('/meals/{id}',      [NutritionPlanController::class, 'destroy']);
        Route::delete('/meals',           [NutritionPlanController::class, 'reset']);
    });

    // === PROGRESS ===
    Route::prefix('progress')->group(function () {
        Route::get('/metrics',            [ProgressController::class, 'getMetrics']);
        Route::post('/metrics',           [ProgressController::class, 'updateMetrics']);
    });

    // === TRAINING COMPLETIONS ===
    Route::prefix('training')->group(function () {
        Route::post('/complete',          [TrainingCompletionController::class, 'markCompleted']);
        Route::post('/uncomplete',        [TrainingCompletionController::class, 'unmark']);
        Route::get('/today',              [TrainingCompletionController::class, 'todayCompletions']);
        Route::get('/stats',              [TrainingCompletionController::class, 'stats']);
        Route::get('/history',            [TrainingCompletionController::class, 'history']);
    });

    // === TRAINING PLAN (ejercicios asignados) ===
    Route::get('/training-plan',          [TrainingPlanController::class, 'index']);
    Route::post('/training-plan',         [TrainingPlanController::class, 'store']);
    Route::delete('/training-plan/{id}',  [TrainingPlanController::class, 'destroy']);

    // === PROFILE PHOTO ===
    Route::post('/profile/photo',         [ProfilePhotoController::class, 'upload']);

    // === SETTINGS ===
    Route::prefix('settings')->group(function () {
        Route::get('/',                   [SettingsController::class, 'show']);
        Route::post('/',                  [SettingsController::class, 'update']);
        Route::post('/deactivate',       [SettingsController::class, 'destroyAccount']);
    });

    // === USERS CRUD ===
    Route::get('/users',              [UserController::class, 'index']);
    Route::post('/users',             [UserController::class, 'store']);
    Route::get('/users/{id}',          [UserController::class, 'show']);
    Route::put('/users/{id}',          [UserController::class, 'update']);
    Route::delete('/users/{id}',       [UserController::class, 'destroy']);
});
