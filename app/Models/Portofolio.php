<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Portofolio extends Model
{
    protected $table = 'portofolio';

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

    public function sampel(): HasMany
    {
        return $this->hasMany(Sampel::class, 'portofolio_id');
    }

    public function kodeSurat(): HasMany
    {
        return $this->hasMany(KodeSurat::class, 'portofolio_id');
    }
}