<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PemusnahanController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Superadmin\AdminCabangController;
use App\Http\Controllers\Superadmin\BagianController as SuperadminBagianController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\DokumenNasionalController;
use App\Http\Controllers\Superadmin\KodeSuratController;
use App\Http\Controllers\Superadmin\LogAktivitasController as SuperadminLogAktivitasController;
use App\Http\Controllers\Superadmin\SampelNasionalController;

use App\Http\Controllers\Admin\BagianController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JenisDokumenController;
use App\Http\Controllers\Admin\KelolaController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Admin\LogArsipController;
use App\Http\Controllers\Admin\SampelController as AdminSampelController;

use App\Http\Controllers\User\ArsipController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\DokumenSayaController;
use App\Http\Controllers\User\SampelController as UserSampelController;
use App\Http\Controllers\User\UnggahDokumenController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
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

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/sampel/generate-kode', [AdminSampelController::class, 'generateKode'])->name('sampel.generate-kode');

    Route::get('/pemusnahan', [PemusnahanController::class, 'index'])->name('pemusnahan.index');
    Route::post('/pemusnahan/ajukan', [PemusnahanController::class, 'ajukan'])->name('pemusnahan.ajukan');
    Route::post('/pemusnahan', [PemusnahanController::class, 'musnahkan'])->name('pemusnahan.musnahkan');
    Route::delete('/pemusnahan/antrean/{item}', [PemusnahanController::class, 'batalkan'])->whereNumber('item')->name('pemusnahan.batalkan');
    Route::delete('/pemusnahan/riwayat/massal', [PemusnahanController::class, 'destroyRiwayatMassal'])->name('pemusnahan.destroy-riwayat-massal');
    Route::delete('/pemusnahan/riwayat/{id}', [PemusnahanController::class, 'destroyRiwayat'])->whereNumber('id')->name('pemusnahan.destroy-riwayat');
});

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/kode-surat', [KodeSuratController::class, 'index'])->name('kode-surat.index');
    Route::post('/kode-surat', [KodeSuratController::class, 'store'])->name('kode-surat.store');
    Route::put('/kode-surat/{kodeSurat}', [KodeSuratController::class, 'update'])->name('kode-surat.update');
    Route::delete('/kode-surat/{kodeSurat}', [KodeSuratController::class, 'destroy'])->name('kode-surat.destroy');

    Route::get('/bagian', [SuperadminBagianController::class, 'index'])->name('bagian.index');
    Route::post('/bagian', [SuperadminBagianController::class, 'store'])->name('bagian.store');
    Route::put('/bagian/{bagian}', [SuperadminBagianController::class, 'update'])->name('bagian.update');
    Route::delete('/bagian/{bagian}', [SuperadminBagianController::class, 'destroy'])->name('bagian.destroy');

    Route::get('/portofolio', [\App\Http\Controllers\Superadmin\PortofolioController::class, 'index'])->name('portofolio.index');
    Route::post('/portofolio', [\App\Http\Controllers\Superadmin\PortofolioController::class, 'store'])->name('portofolio.store');
    Route::put('/portofolio/{portofolio}', [\App\Http\Controllers\Superadmin\PortofolioController::class, 'update'])->name('portofolio.update');
    Route::delete('/portofolio/{portofolio}', [\App\Http\Controllers\Superadmin\PortofolioController::class, 'destroy'])->name('portofolio.destroy');

    Route::get('/dokumen', [DokumenNasionalController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{dokumen}/unduh', [DokumenNasionalController::class, 'unduh'])->name('dokumen.unduh');
    Route::get('/dokumen/{dokumen}/preview', [DokumenNasionalController::class, 'preview'])->name('dokumen.preview');

    Route::get('/sampel', [SampelNasionalController::class, 'index'])->name('sampel.index');
    Route::get('/sampel/{sampel}/foto', [SampelNasionalController::class, 'foto'])->name('sampel.foto');

    Route::get('/admin-cabang', [AdminCabangController::class, 'index'])->name('admin-cabang.index');
    Route::post('/admin-cabang', [AdminCabangController::class, 'store'])->name('admin-cabang.store');
    Route::post('/admin-cabang/cabang', [AdminCabangController::class, 'storeCabang'])->name('admin-cabang.cabang.store');
    Route::put('/admin-cabang/cabang/{cabang}', [AdminCabangController::class, 'updateCabang'])->name('admin-cabang.cabang.update');
    Route::delete('/admin-cabang/cabang/{cabang}', [AdminCabangController::class, 'destroyCabang'])->name('admin-cabang.cabang.destroy');
    Route::put('/admin-cabang/{adminCabang}', [AdminCabangController::class, 'update'])->name('admin-cabang.update');
    Route::delete('/admin-cabang/{adminCabang}', [AdminCabangController::class, 'destroy'])->name('admin-cabang.destroy');

    Route::get('/log-aktivitas', [SuperadminLogAktivitasController::class, 'index'])->name('log-aktivitas.index');
    Route::post('/log-aktivitas/arsipkan', [SuperadminLogAktivitasController::class, 'arsipkan'])->name('log-aktivitas.arsipkan');
    Route::delete('/log-aktivitas/hapus-arsip', [SuperadminLogAktivitasController::class, 'hapusArsip'])->name('log-aktivitas.hapus-arsip');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');
    Route::post('/layanan', [LayananController::class, 'store'])->name('layanan.store');
    Route::put('/layanan/{layanan}', [LayananController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{layanan}', [LayananController::class, 'destroy'])->name('layanan.destroy');

    Route::post('/jenis-dokumen', [JenisDokumenController::class, 'store'])->name('jenis.store');
    Route::put('/jenis-dokumen/{jenis}', [JenisDokumenController::class, 'update'])->name('jenis.update');
    Route::delete('/jenis-dokumen/{jenis}', [JenisDokumenController::class, 'destroy'])->name('jenis.destroy');

    Route::get('/bagian', [BagianController::class, 'index'])->name('bagian.index');
    Route::post('/bagian', [BagianController::class, 'store'])->name('bagian.store');
    Route::put('/bagian/{bagian}', [BagianController::class, 'update'])->name('bagian.update');
    Route::delete('/bagian/{bagian}', [BagianController::class, 'destroy'])->name('bagian.destroy');
    Route::post('/bagian/{bagian}/klasifikasi', [BagianController::class, 'storeKlasifikasi'])->name('bagian.klasifikasi.store');
    Route::delete('/bagian/klasifikasi/{kodeSurat}', [BagianController::class, 'destroyKlasifikasi'])->name('bagian.klasifikasi.destroy');

    Route::get('/portofolio', [\App\Http\Controllers\Admin\PortofolioController::class, 'index'])->name('portofolio.index');
    Route::post('/portofolio', [\App\Http\Controllers\Admin\PortofolioController::class, 'store'])->name('portofolio.store');
    Route::put('/portofolio/{portofolio}', [\App\Http\Controllers\Admin\PortofolioController::class, 'update'])->name('portofolio.update');
    Route::delete('/portofolio/{portofolio}', [\App\Http\Controllers\Admin\PortofolioController::class, 'destroy'])->name('portofolio.destroy');

    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
    Route::post('/dokumen', [DokumenController::class, 'store'])->name('dokumen.store');
    Route::get('/dokumen/export', [DokumenController::class, 'export'])->name('dokumen.export');
    Route::get('/dokumen/{id}', fn () => redirect()->route('dokumen.index'));
    Route::put('/dokumen/{dokumen}', [DokumenController::class, 'update'])->name('dokumen.update');
    Route::delete('/dokumen/{id}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
    Route::get('/dokumen/{dokumen}/download', [DokumenController::class, 'download'])->name('dokumen.download');
    Route::get('/dokumen/{dokumen}/preview', [DokumenController::class, 'preview'])->name('dokumen.preview');

    Route::get('/sampel', [AdminSampelController::class, 'index'])->name('admin.sampel.index');
    Route::get('/sampel/export', [AdminSampelController::class, 'export'])->name('admin.sampel.export');
    Route::get('/sampel/create', [AdminSampelController::class, 'create'])->name('admin.sampel.create');
    Route::post('/sampel', [AdminSampelController::class, 'store'])->name('admin.sampel.store');
    Route::get('/sampel/{sampel}', [AdminSampelController::class, 'show'])->whereNumber('sampel')->name('admin.sampel.show');
    Route::get('/sampel/{id}/edit', [AdminSampelController::class, 'edit'])->name('admin.sampel.edit');
    Route::get('/sampel/{id}/cetak-label', [AdminSampelController::class, 'cetakLabel'])->name('admin.sampel.cetak-label');
    Route::put('/sampel/{id}', [AdminSampelController::class, 'update'])->name('admin.sampel.update');
    Route::delete('/sampel/{sampel}', [AdminSampelController::class, 'destroy'])->whereNumber('sampel')->name('admin.sampel.destroy');

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

    Route::get('/dokumen-saya', [DokumenSayaController::class, 'index'])->name('user.dokumen-saya.index');
    Route::get('/dokumen-saya/label-box-semua', [DokumenSayaController::class, 'labelBoxSemua'])->name('user.dokumen-saya.label-box-semua');
    Route::post('/dokumen-saya/label-box-terpilih', [DokumenSayaController::class, 'labelBoxTerpilih'])->name('user.dokumen-saya.label-box-terpilih');
    Route::get('/dokumen-saya/{dokumen}', [DokumenSayaController::class, 'show'])->whereNumber('dokumen')->name('user.dokumen-saya.show');
    Route::get('/dokumen-saya/{dokumen}/label-box', [DokumenSayaController::class, 'labelBox'])->whereNumber('dokumen')->name('user.dokumen-saya.label-box');

    Route::get('/unggah', [UnggahDokumenController::class, 'create'])->name('user.unggah.create');
    Route::post('/unggah', [UnggahDokumenController::class, 'store'])->name('user.unggah.store');

    Route::get('/arsip', [ArsipController::class, 'index'])->name('user.arsip.index');
    Route::get('/arsip/{dokumen}', [ArsipController::class, 'show'])->whereNumber('dokumen')->name('user.arsip.show');
    Route::get('/arsip/{dokumen}/pratinjau', [ArsipController::class, 'pratinjau'])->whereNumber('dokumen')->name('user.arsip.pratinjau');
    Route::get('/arsip/{dokumen}/unduh', [ArsipController::class, 'unduh'])->whereNumber('dokumen')->name('user.arsip.unduh');

    Route::get('/sampel-saya', [UserSampelController::class, 'index'])->name('user.sampel.index');
    Route::get('/sampel-saya/tambah', [UserSampelController::class, 'create'])->name('user.sampel.create');
    Route::post('/sampel-saya', [UserSampelController::class, 'store'])->name('user.sampel.store');
    Route::get('/sampel-saya/label-semua', [UserSampelController::class, 'labelSemua'])->name('user.sampel.label-semua');
    Route::post('/sampel-saya/label-terpilih', [UserSampelController::class, 'labelTerpilih'])->name('user.sampel.label-terpilih');
    Route::get('/sampel-saya/{sampel}', [UserSampelController::class, 'show'])->whereNumber('sampel')->name('user.sampel.show');
    Route::get('/sampel-saya/{sampel}/foto', [UserSampelController::class, 'foto'])->whereNumber('sampel')->name('user.sampel.foto');
    Route::get('/sampel-saya/{sampel}/label', [UserSampelController::class, 'labelPdf'])->whereNumber('sampel')->name('user.sampel.label');
});