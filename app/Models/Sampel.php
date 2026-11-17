<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sampel extends Model
{
    protected $table = 'sampel';

    protected $fillable = [
        'cabang_id',
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
        'foto_path',
        'deskripsi',
        'diunggah_oleh',
        'status_arsip',
        'nomor_buku',
        'tanggal_hapus_buku',
        'keterangan_hapus_buku',
    ];

    protected $casts = [
        'tanggal_pengambilan' => 'date',
        'tanggal_masuk_arsip' => 'date',
        'tanggal_hapus_buku' => 'date',
    ];

    public function bagian(): BelongsTo
    {
        return $this->belongsTo(Bagian::class, 'bagian_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}