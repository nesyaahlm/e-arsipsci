<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JenisDokumenController;
use App\Http\Controllers\Admin\KelolaController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Admin\LogArsipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
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

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');
    Route::post('/layanan', [LayananController::class, 'store'])->name('layanan.store');
    Route::put('/layanan/{layanan}', [LayananController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{layanan}', [LayananController::class, 'destroy'])->name('layanan.destroy');

    Route::post('/jenis-dokumen', [JenisDokumenController::class, 'store'])->name('jenis.store');
    Route::put('/jenis-dokumen/{jenis}', [JenisDokumenController::class, 'update'])->name('jenis.update');
    Route::delete('/jenis-dokumen/{jenis}', [JenisDokumenController::class, 'destroy'])->name('jenis.destroy');

    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{id}', function () {
        return redirect()->route('dokumen.index');
    });
    Route::post('/dokumen', [DokumenController::class, 'store'])->name('dokumen.store');
    Route::put('/dokumen/{dokumen}', [DokumenController::class, 'update'])->name('dokumen.update');
    Route::delete('/dokumen/{id}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
    Route::get('/dokumen/{dokumen}/download', [DokumenController::class, 'download'])->name('dokumen.download');
    Route::get('/dokumen/{dokumen}/preview', [DokumenController::class, 'preview'])->name('dokumen.preview');

    Route::get('/kelola-user', [KelolaController::class, 'index'])->name('users.index');
    Route::post('/kelola-user', [KelolaController::class, 'store'])->name('users.store');
    Route::put('/kelola-user/{user}', [KelolaController::class, 'update'])->name('users.update');
    Route::delete('/kelola-user/{user}', [KelolaController::class, 'destroy'])->name('users.destroy');

    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log.index');
    Route::get('/log-aktivitas/arsip', [LogArsipController::class, 'index'])->name('log.arsip.index');
    Route::delete('/log-aktivitas/arsip/terpilih', [LogArsipController::class, 'hapusTerpilih'])->name('log.arsip.hapus-terpilih');
    Route::delete('/log-aktivitas/arsip', [LogArsipController::class, 'hapusSemua'])->name('log.arsip.hapus-semua');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});