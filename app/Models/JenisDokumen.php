<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisDokumen extends Model
{
    protected $table = 'jenis_dokumen';

    protected $fillable = ['kode', 'nama', 'retensi_tahun'];

    public function layanan(): BelongsToMany
    {
        return $this->belongsToMany(Layanan::class, 'jenis_dokumen_layanan', 'jenis_dokumen_id', 'layanan_id');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'jenis_dokumen_id');
    }
}
