<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemusnahan extends Model
{
    protected $table = 'pemusnahan';

    protected $fillable = [
        'nomor',
        'tanggal',
        'pelaksana',
        'bukti_path',
        'bukti_nama_asli',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PemusnahanItem::class, 'pemusnahan_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}