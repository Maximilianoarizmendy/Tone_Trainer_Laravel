<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;

// === LANDING PAGE ===
Route::get('/', [LandingController::class, 'index'])->name('home');

// === AUTH (invitados) ===
Route::middleware('guest')->group(function () {
    Route::get('/login',                        [LoginController::class, 'showForm'])->name('login');
    Route::post('/login',                       [LoginController::class, 'login'])->name('login.post');
    Route::get('/register',                     [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register',                    [RegisterController::class, 'register'])->name('register.post');
    Route::get('/forgot-password',              [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password',             [ForgotPasswordController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}',       [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password',              [ResetPasswordController::class, 'reset'])->name('password.update');
});

// === LOGOUT ===
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// === DASHBOARD (autenticados) ===
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {

    // Compartido: Inicio del dashboard (todos los roles)
    Route::get('/',              [DashboardController::class, 'index'])->name('index');
    Route::get('/entrenamiento', [DashboardController::class, 'training'])->name('training');
    Route::get('/nutricion',     [DashboardController::class, 'nutrition'])->name('nutrition');

    // Solo usuario normal (rol 1)
    Route::middleware('role:1')->group(function () {
        Route::get('/progreso',      [DashboardController::class, 'progress'])->name('progress');
        Route::get('/metas',         [DashboardController::class, 'goals'])->name('goals');
    });

    // Admin (rol 2)
    Route::middleware('role:2')->group(function () {
        Route::get('/admin',         [DashboardController::class, 'admin'])->name('admin');
    });

    // Nutricionista (rol 3)
    Route::middleware('role:3')->group(function () {
        Route::get('/nutricionista', [DashboardController::class, 'nutritionist'])->name('nutritionist');
    });

    // Entrenador (rol 4)
    Route::middleware('role:4')->group(function () {
        Route::get('/entrenador',    [DashboardController::class, 'trainer'])->name('trainer');
    });

    // Compartidos: todos los roles autenticados
    Route::get('/mensajes',          [DashboardController::class, 'messages'])->name('messages');
    Route::get('/perfil',            [DashboardController::class, 'profile'])->name('profile');
    Route::get('/configuracion',     [DashboardController::class, 'settings'])->name('settings');
    Route::get('/usuarios',          [DashboardController::class, 'users'])->name('users')->middleware('role:2,3,4');
    Route::post('/editar-perfil',    [DashboardController::class, 'updateProfile'])->name('profile.update');
});
