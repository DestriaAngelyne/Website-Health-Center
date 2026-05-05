<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poli extends Model
{
    use SoftDeletes;

    protected $table = 'poli';

    protected $fillable = ['kode', 'nama', 'singkatan', 'keterangan', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function dokter(): HasMany
    {
        return $this->hasMany(Dokter::class);
    }

    public function jadwalDokter(): HasMany
    {
        return $this->hasMany(JadwalDokter::class);
    }

    public function sesiAntrian(): HasMany
    {
        return $this->hasMany(SesiAntrian::class);
    }
}
