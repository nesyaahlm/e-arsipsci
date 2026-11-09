<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LayananController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/layanan');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'formLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');

    Route::middleware(AdminOnly::class)->group(function () {
        Route::post('/layanan', [LayananController::class, 'store'])->name('layanan.store');
        Route::put('/layanan/{layanan}', [LayananController::class, 'update'])->name('layanan.update');
        Route::delete('/layanan/{layanan}', [LayananController::class, 'destroy'])->name('layanan.destroy');
    });
});
