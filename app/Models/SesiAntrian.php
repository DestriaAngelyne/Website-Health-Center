<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiAntrian extends Model
{
    protected $table = 'sesi_antrian';

    protected $fillable = [
        'poli_id',
        'dokter_id',
        'tanggal',
        'jam_buka',
        'jam_tutup',
        'kuota',
        'nomor_terakhir',
        'status',
        'dibuka_oleh',
        'ditutup_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function antrian(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }

    public function getSisaKuotaAttribute(): int
    {
        $terpakai = $this->antrian()->whereNotIn('status', ['batal'])->count();
        return max(0, $this->kuota - $terpakai);
    }
}
