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
        Route::get('/poll',               [MessageController::class, 'poll']);          // Polling eficiente por timestamp
        Route::get('/unread-count',       [MessageController::class, 'unreadCount']);
        Route::post('/send',              [MessageController::class, 'send']);
        Route::put('/{id}',               [MessageController::class, 'edit']);
        Route::delete('/{id}',            [MessageController::class, 'destroy']);

        // Contact Requests
        Route::get('/contacts/search',       [MessageController::class, 'searchUsers']);
        Route::get('/requests/pending',      [MessageController::class, 'pendingRequests']);
        Route::post('/requests/send',        [MessageController::class, 'sendRequest']);
        Route::post('/requests/{id}/accept',  [MessageController::class, 'acceptRequest']);
        Route::post('/requests/{id}/decline', [MessageController::class, 'declineRequest']);
    });

    // === NUTRITION ===
    Route::prefix('nutrition')->group(function () {
        Route::get('/meals',              [NutritionPlanController::class, 'index']);
        Route::post('/meals',             [NutritionPlanController::class, 'store']);
        Route::delete('/meals/{id}',      [NutritionPlanController::class, 'destroy']);
        Route::delete('/meals',           [NutritionPlanController::class, 'reset']);
    });

    // === FOOD LOGS ===
    Route::prefix('food-logs')->group(function () {
        Route::get('/', [\App\Http\Controllers\FoodLogController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\FoodLogController::class, 'store']);
        Route::post('/{id}/consume', [\App\Http\Controllers\FoodLogController::class, 'consume']);
        Route::delete('/{id}', [\App\Http\Controllers\FoodLogController::class, 'destroy']);
    });

    // === PROGRESS ===
    Route::prefix('progress')->group(function () {
        Route::get('/metrics',            [ProgressController::class, 'getMetrics']);
        Route::get('/compare',            [ProgressController::class, 'compare']);
        Route::post('/metrics',           [ProgressController::class, 'updateMetrics']);
        Route::put('/metrics/{id}/validate', [ProgressController::class, 'validateProgress']);
    });

    // === TRAINING COMPLETIONS ===
    Route::prefix('training')->group(function () {
        Route::post('/complete',          [TrainingCompletionController::class, 'markCompleted']);
        Route::post('/uncomplete',        [TrainingCompletionController::class, 'unmark']);
        Route::get('/today',              [TrainingCompletionController::class, 'todayCompletions']);
        Route::get('/stats',              [TrainingCompletionController::class, 'stats']);
        Route::get('/history',            [TrainingCompletionController::class, 'history']);
    });

    // === ATTENDANCE (REPORT) ===
    Route::get('/attendance',             [\App\Http\Controllers\RoutineAttendanceController::class, 'index']);

    // === TRAINING PLAN (ejercicios asignados) ===
    Route::get('/training-plan',          [TrainingPlanController::class, 'index']);
    Route::post('/training-plan',         [TrainingPlanController::class, 'store']);
    Route::delete('/training-plan/{id}',  [TrainingPlanController::class, 'destroy']);
    Route::get('/exercises-library',      [TrainingPlanController::class, 'library']);

    // === PROFILE PHOTO ===
    Route::post('/profile/photo',         [ProfilePhotoController::class, 'upload']);

    // === SETTINGS ===
    Route::prefix('settings')->group(function () {
        Route::get('/',                   [SettingsController::class, 'show']);
        Route::post('/',                  [SettingsController::class, 'update']);
        Route::post('/deactivate',       [SettingsController::class, 'destroyAccount']);
    });

    // === MEMBERSHIPS ===
    Route::prefix('memberships')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\MembershipController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\MembershipController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\Api\MembershipController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\MembershipController::class, 'destroy']);
    });

    // === PAYMENTS ===
    Route::prefix('payments')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\PaymentController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\PaymentController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\Api\PaymentController::class, 'update']);
    });

    // === CHALLENGES ===
    Route::prefix('challenges')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ChallengeController::class, 'index']);
        Route::get('/my-challenges', [\App\Http\Controllers\Api\ChallengeController::class, 'myChallenges']);
        Route::post('/', [\App\Http\Controllers\Api\ChallengeController::class, 'store']);
        Route::post('/{id}/join', [\App\Http\Controllers\Api\ChallengeController::class, 'join']);
        Route::post('/{id}/progress', [\App\Http\Controllers\Api\ChallengeController::class, 'updateProgress']);
    });

    // === REPORTS ===
    Route::get('/reports', [\App\Http\Controllers\Api\ReportController::class, 'index']);

    // === RANKING ===
    Route::get('/ranking', [\App\Http\Controllers\Api\RankingController::class, 'index']);

    // === NOTIFICATIONS ===
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::post('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
        Route::post('/broadcast', [\App\Http\Controllers\Api\NotificationController::class, 'broadcast']);
        Route::post('/send/{userId}', [\App\Http\Controllers\Api\NotificationController::class, 'sendToUser']);
    });

    // === USERS CRUD ===
    Route::get('/users',              [UserController::class, 'index']);
    Route::post('/users',             [UserController::class, 'store']);
    Route::get('/users/{id}',          [UserController::class, 'show']);
    Route::put('/users/{id}',          [UserController::class, 'update']);
    Route::delete('/users/{id}',       [UserController::class, 'destroy']);
});
