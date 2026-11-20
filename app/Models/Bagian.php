<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bagian extends Model
{
    protected $table = 'bagian';

    protected $fillable = [
        'cabang_id',
        'kode',
        'nama',
        'retensi_tahun',
        'retensi_satuan',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'bagian_id');
    }

    public function kodeSurat(): HasMany
    {
        return $this->hasMany(KodeSurat::class, 'bagian_id');
    }
}