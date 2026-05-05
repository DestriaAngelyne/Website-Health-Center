<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rujukan extends Model
{
    use SoftDeletes;

    protected $table = 'rujukan';

    protected $fillable = [
        'kunjungan_id',
        'pasien_id',
        'dokter_id',
        'no_rujukan',
        'tanggal_rujukan',
        'tanggal_berlaku',
        'faskes_tujuan',
        'poli_tujuan',
        'diagnosa_kode',
        'diagnosa_nama',
        'alasan_rujukan',
        'terapi_yang_telah_diberikan',
        'catatan',
        'jenis',
        'status',
    ];

    protected $casts = [
        'tanggal_rujukan' => 'date',
        'tanggal_berlaku' => 'date',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }
}
