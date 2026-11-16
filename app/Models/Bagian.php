<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bagian extends Model
{
    protected $table = 'bagian';

    protected $fillable = [
        'kode',
        'nama',
        'retensi_tahun',
    ];

    /**
     * Relasi ke model Dokumen.
     */
    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'bagian_id');
    }
}