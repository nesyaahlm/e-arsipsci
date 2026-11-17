<?php

namespace App\Support;

use App\Models\JenisDokumen;
use App\Models\Bagian;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Throwable;

class PencatatAktivitas
{
    public static function daftarkan(): void
    {
        User::created(fn (User $u) => self::catat('mengelola_user', null, 'Menambahkan ' . $u->name));

        User::updated(function (User $u) {
            if ($u->id === Auth::id()) {
                return;
            }

            $berubah = self::perubahan($u, ['remember_token']);

            if ($berubah === []) {
                return;
            }

            if ($berubah === ['status']) {
                $awalan = $u->status === 'nonaktif' ? 'Menonaktifkan ' : 'Mengaktifkan ';
            } else {
                $awalan = 'Mengubah ';
            }

            self::catat('mengelola_user', null, $awalan . $u->name);
        });

        User::deleted(fn (User $u) => self::catat('mengelola_user', null, 'Menghapus ' . $u->name));

        // Pencatatan log untuk Bagian (menggantikan Layanan)
        Bagian::created(fn (Bagian $b) => self::catat('mengelola_bagian', null, 'Menambahkan Bagian ' . $b->nama));

        Bagian::updated(function (Bagian $b) {
            if (self::adaPerubahan($b)) {
                self::catat('mengelola_bagian', null, 'Mengubah Bagian ' . $b->nama);
            }
        });

        Bagian::deleted(fn (Bagian $b) => self::catat('mengelola_bagian', null, 'Menghapus Bagian ' . $b->nama));

        JenisDokumen::created(fn (JenisDokumen $j) => self::catat('mengelola_jenis_dokumen', null, 'Menambahkan ' . $j->nama));

        JenisDokumen::updated(function (JenisDokumen $j) {
            if (self::adaPerubahan($j)) {
                self::catat('mengelola_jenis_dokumen', null, 'Mengubah ' . $j->nama);
            }
        });

        JenisDokumen::deleted(fn (JenisDokumen $j) => self::catat('mengelola_jenis_dokumen', null, 'Menghapus ' . $j->nama));

        Event::listen(Login::class, fn (Login $e) => self::catat('login', null, null, $e->user->getAuthIdentifier()));

        Event::listen(Logout::class, function (Logout $e) {
            if ($e->user) {
                self::catat('logout', null, null, $e->user->getAuthIdentifier());
            }
        });

        Event::listen(PasswordReset::class, fn (PasswordReset $e) => self::catat('reset_password', null, null, $e->user->getAuthIdentifier()));
    }

    public static function catat(string $aktivitas, ?int $dokumenId = null, ?string $keterangan = null, $userId = null): void
    {
        $userId = $userId ?? Auth::id();

        if ($userId === null || app()->runningInConsole()) {
            return;
        }

        try {
            LogAktivitas::catat($aktivitas, $dokumenId, $keterangan, (int) $userId);
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected static function perubahan($model, array $abaikan = []): array
    {
        return array_values(array_diff(array_keys($model->getChanges()), array_merge(['updated_at'], $abaikan)));
    }

    protected static function adaPerubahan($model): bool
    {
        return self::perubahan($model) !== [];
    }
}