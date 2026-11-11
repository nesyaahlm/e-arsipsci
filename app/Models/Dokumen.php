<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokumen extends Model
{
    use SoftDeletes; 

    protected $table = 'dokumen';

    protected $fillable = [
        'nomor',
        'judul',
        'layanan_id',
        'jenis_dokumen_id',
        'unit',
        'tanggal_dokumen',
        'deskripsi',
        'file_path',
        'file_nama_asli',
        'file_ukuran',
        'lokasi_arsip_fisik',
        'diunggah_oleh',
        'retensi_sampai',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
        'retensi_sampai' => 'date',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function jenisDokumen(): BelongsTo
    {
        return $this->belongsTo(JenisDokumen::class, 'jenis_dokumen_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}