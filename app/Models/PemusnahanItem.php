<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemusnahanItem extends Model
{
    protected $table = 'pemusnahan_item';

    protected $fillable = [
        'tipe',
        'dokumen_id',
        'sampel_id',
        'kode',
        'judul',
        'status_sebelumnya',
        'lokasi_sebelumnya',
        'diajukan_oleh',
        'pemusnahan_id',
    ];

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id')->withTrashed();
    }

    public function sampel()
    {
        return $this->belongsTo(Sampel::class, 'sampel_id')->withTrashed();
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function pemusnahan()
    {
        return $this->belongsTo(Pemusnahan::class, 'pemusnahan_id');
    }
}