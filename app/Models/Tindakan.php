<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tindakan extends Model
{
    use SoftDeletes;

    protected $table = 'tindakan';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'tarif',
        'is_active',
    ];

    protected $casts = [
        'tarif'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function kunjunganTindakan()
    {
        return $this->hasMany(KunjunganTindakan::class);
    }
}
