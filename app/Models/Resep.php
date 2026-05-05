<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = [
        'kunjungan_id',
        'dokter_id',
        'no_resep',
        'catatan',
        'status',
        'diserahkan_at',
    ];

    protected $casts = [
        'diserahkan_at' => 'datetime',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function detail()
    {
        return $this->hasMany(ResepDetail::class);
    }
}
