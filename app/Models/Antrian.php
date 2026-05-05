<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Antrian extends Model
{
    protected $table = 'antrian';
    protected $fillable = [
        'sesi_antrian_id', 'pasien_id', 'poli_id', 'dokter_id',
        'loket_user_id', 'kode_antrian', 'nomor_antrian', 'tanggal',
        'jenis_kunjungan', 'jenis_pasien', 'status', 'waktu_daftar',
        'waktu_dipanggil', 'waktu_mulai_skrining', 'waktu_selesai_skrining',
        'waktu_mulai_dilayani', 'waktu_selesai', 'catatan',
    ];

    protected $casts = [
        'tanggal'                 => 'date',
        'waktu_daftar'            => 'datetime',
        'waktu_dipanggil'         => 'datetime',
        'waktu_mulai_skrining'    => 'datetime',
        'waktu_selesai_skrining'  => 'datetime',
        'waktu_mulai_dilayani'    => 'datetime',
        'waktu_selesai'           => 'datetime',
    ];

    // Relasi ke Pasien
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    // Relasi ke Poli
    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }

    // Relasi ke Dokter
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    // Relasi ke SesiAntrian
    public function sesiAntrian(): BelongsTo
    {
        return $this->belongsTo(SesiAntrian::class, 'sesi_antrian_id');
    }
}
