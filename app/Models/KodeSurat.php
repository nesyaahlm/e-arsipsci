<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KodeSurat extends Model
{
    protected $table = 'kode_surat';

    protected $fillable = [
        'bagian_id',
        'portofolio_id',
        'kode',
        'nama',
        'retensi_aktif',
        'retensi_inaktif',
        'keterangan',
    ];

    public function bagian(): BelongsTo
    {
        return $this->belongsTo(Bagian::class, 'bagian_id');
    }

    public function portofolio(): BelongsTo
    {
        return $this->belongsTo(Portofolio::class, 'portofolio_id');
    }
}