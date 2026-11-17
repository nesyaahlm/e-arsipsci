<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokumen extends Model
{
    use SoftDeletes;

    protected $table = 'dokumen';

    protected $fillable = [
        'cabang_id',
        'nomor',
        'judul',
        'bagian_id',
        'unit',
        'tanggal_dokumen',
        'tahun_arsip',
        'deskripsi',
        'file_path',
        'file_nama_asli',
        'file_ukuran',
        'lokasi_arsip_fisik',
        'diunggah_oleh',
        'retensi_sampai',
        'status_arsip',
        'nomor_buku',
        'tanggal_hapus_buku',
        'keterangan_hapus_buku',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
        'retensi_sampai' => 'date',
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