<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kunjungan extends Model
{
    use SoftDeletes;

    protected $table = 'kunjungan';

    protected $fillable = [
        'antrian_id',
        'pasien_id',
        'dokter_id',
        'poli_id',
        'perawat_id',
        'tanggal_kunjungan',
        'no_kunjungan',
        'berat_badan',
        'tinggi_badan',
        'tekanan_darah',
        'nadi',
        'suhu',
        'respirasi',
        'saturasi_oksigen',
        'keluhan',
        'anamnesis',
        'pemeriksaan_fisik',
        'diagnosa_kode',
        'diagnosa_nama',
        'diagnosa_sekunder_kode',
        'diagnosa_sekunder_nama',
        'rencana_terapi',
        'catatan_dokter',
        'status',
        'selesai_at',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'selesai_at'        => 'datetime',
        'berat_badan'       => 'decimal:2',
        'tinggi_badan'      => 'decimal:2',
        'nadi'              => 'integer',
        'suhu'              => 'integer',
        'respirasi'         => 'integer',
        'saturasi_oksigen'  => 'integer',
    ];

    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function perawat()
    {
        return $this->belongsTo(User::class, 'perawat_id');
    }

    public function tindakan()
    {
        return $this->hasMany(KunjunganTindakan::class);
    }

    public function resep()
    {
        return $this->hasOne(Resep::class);
    }

    public function rujukan()
    {
        return $this->hasOne(Rujukan::class);
    }
}
