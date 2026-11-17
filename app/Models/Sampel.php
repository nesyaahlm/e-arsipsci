<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sampel extends Model
{
    protected $table = 'sampel';

    protected $fillable = [
        'kode_sampel',
        'nama_sampel',
        'shipper',
        'vessel',
        'bill_of_lading',
        'tanggal_pengambilan',
        'tahun_arsip',
        'tempat_pengambilan',
        'analysis_for',
        'bagian_id',
        'nomor_ruang_arsip',
        'nomor_tingkat_arsip',
        'nama_ruang_arsip',
        'tanggal_masuk_arsip',
        'foto_path',
        'deskripsi',
        'diunggah_oleh',
    ];

    protected $casts = [
        'tanggal_pengambilan' => 'date',
        'tanggal_masuk_arsip' => 'date',
    ];

    /**
     * Tahun di label: isi kolom tahun_arsip bila ada (boleh lebih dari satu tahun,
     * mis. "2026 dan 2027"), selain itu tahun tanggal pengambilan.
     */
    public function getLabelTahunAttribute(): string
    {
        if (filled($this->tahun_arsip)) {
            return (string) $this->tahun_arsip;
        }

        return $this->tanggal_pengambilan?->format('Y') ?? '-';
    }

    public static function aturanTahun(): array
    {
        return ['nullable', 'string', 'max:50', function ($atribut, $nilai, $gagal) {
            if (! preg_match('/^(?:19|20)\d{2}(?:\s*(?:,|&|dan)\s*(?:19|20)\d{2})*$/i', trim((string) $nilai))) {
                $gagal('Format tahun tidak valid. Contoh: 2026 atau 2026, 2027 dan 2028.');
            }
        }];
    }

    public static function rapikanTahun(?string $nilai): ?string
    {
        if (! filled($nilai)) {
            return null;
        }

        preg_match_all('/(?:19|20)\d{2}/', $nilai, $cocok);

        $tahun = collect($cocok[0])->unique()->sort()->values();

        if ($tahun->count() <= 1) {
            return $tahun->first();
        }

        $akhir = $tahun->pop();

        return $tahun->implode(', ').' dan '.$akhir;
    }

    public function bagian(): BelongsTo
    {
        return $this->belongsTo(Bagian::class, 'bagian_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}