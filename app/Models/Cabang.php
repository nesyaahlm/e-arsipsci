<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $table = 'cabang';

    protected $fillable = [
        'kode',
        'nama',
        'kota',
        'alamat',
    ];

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'cabang_id');
    }

    public function sampel(): HasMany
    {
        return $this->hasMany(Sampel::class, 'cabang_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    public function bagian(): HasMany
    {
        return $this->hasMany(Bagian::class, 'cabang_id');
    }
}