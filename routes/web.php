<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DokumenSayaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/lupa-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/cek-email', [AuthController::class, 'showCheckEmail'])->name('password.check_email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return "<h1>Selamat Datang di Dashboard Admin (" . auth()->user()->name . ")</h1><form action='" . route('logout') . "' method='POST'>" . csrf_field() . "<button type='submit'>Logout</button></form>";
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/dokumen-saya', [DokumenSayaController::class, 'index'])->name('user.dokumen-saya.index');
    Route::get('/dokumen-saya/{dokumen}', [DokumenSayaController::class, 'show'])->whereNumber('dokumen')->name('user.dokumen-saya.show');
});