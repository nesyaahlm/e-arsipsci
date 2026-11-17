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
        'tempat_pengambilan',
        'analysis_for',
        'bagian_id',
        'nomor_ruang_arsip',
        'nama_ruang_arsip',
        'tanggal_masuk_arsip',
        'status',
        'foto_path',
        'deskripsi',
        'diunggah_oleh',
    ];

    protected $casts = [
        'tanggal_pengambilan' => 'date',
        'tanggal_masuk_arsip' => 'date',
    ];

    public function bagian(): BelongsTo
    {
        return $this->belongsTo(Bagian::class, 'bagian_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}