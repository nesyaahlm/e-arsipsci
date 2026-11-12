<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LogicException;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'aktivitas',
        'dokumen_id',
        'keterangan',
        'created_at',
        'diarsipkan_pada',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'diarsipkan_pada' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            $log->created_at = $log->created_at ?? now();
        });

        static::updating(function (self $log) {
            if (array_keys($log->getDirty()) !== ['diarsipkan_pada']) {
                throw new LogicException('Log aktivitas tidak boleh diubah.');
            }
        });

        static::deleting(function (self $log) {
            if ($log->diarsipkan_pada === null) {
                throw new LogicException('Hanya log yang sudah diarsipkan yang boleh dihapus.');
            }
        });
    }

    public static function catat(string $aktivitas, ?int $dokumenId = null, ?string $keterangan = null, ?int $userId = null): self
    {
        return static::create([
            'user_id' => $userId ?? Auth::id(),
            'aktivitas' => $aktivitas,
            'dokumen_id' => $dokumenId,
            'keterangan' => $keterangan !== null ? Str::limit($keterangan, 250, '') : null,
        ]);
    }

    public static function arsipkanLama(int $hari = 7): int
    {
        return static::query()
            ->whereNull('diarsipkan_pada')
            ->where('created_at', '<', now()->subDays($hari))
            ->update(['diarsipkan_pada' => now()]);
    }

    public static function hapusArsip(?array $ids = null): int
    {
        return static::query()
            ->whereNotNull('diarsipkan_pada')
            ->when($ids !== null, fn ($query) => $query->whereIn('id', $ids))
            ->delete();
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereNull('diarsipkan_pada');
    }

    public function scopeDiarsipkan(Builder $query): Builder
    {
        return $query->whereNotNull('diarsipkan_pada');
    }

    public function scopeFilterLog(Builder $query, string $cari, ?string $aktivitas, ?string $tanggal): Builder
    {
        return $query
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($w) use ($cari) {
                    $w->where('keterangan', 'like', "%{$cari}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%"))
                        ->orWhereHas('dokumen', function ($d) use ($cari) {
                            $d->where(function ($x) use ($cari) {
                                $x->where('nomor', 'like', "%{$cari}%")
                                    ->orWhere('judul', 'like', "%{$cari}%");
                            });
                        });
                });
            })
            ->when($aktivitas, fn ($q) => $q->where('aktivitas', $aktivitas))
            ->when($tanggal && strtotime($tanggal), fn ($q) => $q->whereDate('created_at', $tanggal));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id')->withTrashed();
    }

    public function getLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->aktivitas));
    }
}