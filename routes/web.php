<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\NotificationController;

// === LANDING PAGE ===
Route::get('/', [LandingController::class, 'index'])->name('home');

// === AUTH (invitados) ===
Route::middleware('guest')->group(function () {
    Route::get('/login',               [LoginController::class, 'showForm'])->name('login');
    Route::post('/login',              [LoginController::class, 'login'])->name('login.post');
    Route::get('/register',            [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register',           [RegisterController::class, 'register'])->name('register.post');
    Route::get('/forgot-password',     [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password',    [ForgotPasswordController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password',     [ResetPasswordController::class, 'reset'])->name('password.update');
});

// === LOGOUT ===
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// === DASHBOARD (autenticados) ===
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/',              [DashboardController::class, 'index'])->name('index');
    Route::get('/entrenamiento', [DashboardController::class, 'training'])->name('training');
    Route::get('/nutricion',     [DashboardController::class, 'nutrition'])->name('nutrition');

    // Usuario normal (rol 1)
    Route::middleware('role:1')->group(function () {
        Route::get('/progreso', [DashboardController::class, 'progress'])->name('progress');
        Route::get('/metas',    [DashboardController::class, 'goals'])->name('goals');
        Route::post('/api/training-completions/{exerciseId}', [\App\Http\Controllers\TrainingCompletionController::class, 'store'])->name('training.complete');
    });

    // Admin (rol 2)
    Route::middleware('role:2')->group(function () {
        Route::get('/admin',    [DashboardController::class, 'admin'])->name('admin');
        Route::get('/reports',  [DashboardController::class, 'reports'])->name('reports');

        Route::post('/admin/trainers',        [DashboardController::class, 'storeTrainer'])->name('admin.trainers.store');
        Route::post('/admin/nutritionists',   [DashboardController::class, 'storeNutritionist'])->name('admin.nutritionists.store');
        Route::post('/admin/trainers/verify', [DashboardController::class, 'verifyTrainer'])->name('admin.trainers.verify');
        Route::get('/admin/trainers/verification', [DashboardController::class, 'trainersVerification'])->name('admin.trainers.verification');
        Route::post('/admin/trainers/assign', [DashboardController::class, 'assignTrainer'])->name('admin.trainers.assign');
        Route::post('/admin/broadcast',       [NotificationController::class, 'broadcast'])->name('admin.broadcast');
        Route::get('/users/{id}/edit',        [DashboardController::class, 'editUser'])->name('admin.users.edit');
        Route::get('/asistencia',             [DashboardController::class, 'attendance'])->name('admin.attendance');
    });

    // Nutricionista (rol 3)
    Route::middleware('role:3')->group(function () {
        Route::get('/nutricionista', [DashboardController::class, 'nutritionist'])->name('nutritionist');
    });

    // Entrenador (rol 4)
    Route::middleware('role:4')->group(function () {
        Route::get('/entrenador',    [DashboardController::class, 'trainer'])->name('trainer');
        Route::get('/planes',        [DashboardController::class, 'trainerPlans'])->name('trainer.plans');
        Route::get('/asistencia',    [DashboardController::class, 'attendance'])->name('trainer.attendance');
        Route::post('/retos',        [ChallengeController::class, 'store'])->name('challenges.store');
        Route::delete('/retos/{id}', [ChallengeController::class, 'destroy'])->name('challenges.destroy');
    });

    Route::post('/admin/nutritionist/notes', [DashboardController::class, 'saveNutritionistNote'])->name('admin.nutritionist.notes');
    Route::post('/admin/nutritionist/ai_notes', [DashboardController::class, 'generateNutritionistAINote'])->name('admin.nutritionist.ai_notes');
    Route::get('/retos',                [ChallengeController::class, 'index'])->name('challenges.index');
    Route::post('/retos/{id}/join',     [ChallengeController::class, 'join'])->name('challenges.join');
    Route::post('/retos/{id}/progress', [ChallengeController::class, 'updateProgress'])->name('challenges.progress');

    // Ranking
    Route::get('/ranking', [DashboardController::class, 'ranking'])->name('ranking');

    // Notificaciones AJAX
    Route::get('/api/notifications',           [NotificationController::class, 'index']);
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Compartidos
    Route::get('/mensajes',       [DashboardController::class, 'messages'])->name('messages');
    Route::get('/perfil',         [DashboardController::class, 'profile'])->name('profile');
    Route::get('/configuracion',  [DashboardController::class, 'settings'])->name('settings');
    Route::get('/usuarios',       [DashboardController::class, 'users'])->name('users')->middleware('role:2,3,4');
    Route::get('/payments',       [DashboardController::class, 'payments'])->name('payments')->middleware('role:1,2');
    Route::post('/editar-perfil', [DashboardController::class, 'updateProfile'])->name('profile.update');
});

// === STRIPE ===
Route::middleware('auth')->group(function () {
    Route::post('/stripe/create-checkout-session', [StripeController::class, 'createCheckoutSession'])
        ->name('stripe.create.session');
});

Route::post('/stripe/webhook', [StripeController::class, 'handleWebhook'])->name('stripe.webhook');

// === MERCADO PAGO ===
Route::middleware('auth')->group(function () {
    Route::post('/mercadopago/create-preference', [MercadoPagoController::class, 'createPreference'])
        ->name('mercadopago.create.preference');
    Route::get('/mercadopago/callback', [MercadoPagoController::class, 'paymentCallback'])
        ->name('mercadopago.callback');
});

Route::post('/mercadopago/webhook', [MercadoPagoController::class, 'webhook'])->name('mercadopago.webhook');
