<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dokter extends Model
{
    use SoftDeletes;

    protected $table = 'dokter';

    protected $fillable = [
        'user_id', 'poli_id', 'nip', 'nama',
        'spesialis', 'str_number', 'no_hp', 'foto', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalDokter(): HasMany
    {
        return $this->hasMany(JadwalDokter::class);
    }

    public function antrian(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }
}
