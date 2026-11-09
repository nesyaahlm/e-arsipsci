<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = ['nama', 'deskripsi'];

    public function jenisDokumen(): BelongsToMany
    {
        return $this->belongsToMany(JenisDokumen::class, 'jenis_dokumen_layanan', 'layanan_id', 'jenis_dokumen_id')
            ->orderBy('jenis_dokumen.nama');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'layanan_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'layanan_id');
    }
}
