<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            $log->created_at = $log->created_at ?? now();
        });

        static::updating(function () {
            throw new LogicException('Log aktivitas tidak boleh diubah.');
        });

        static::deleting(function () {
            throw new LogicException('Log aktivitas tidak boleh dihapus.');
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