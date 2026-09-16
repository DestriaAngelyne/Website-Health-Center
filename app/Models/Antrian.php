<?php
namespace App\Models;
use App\Events\AntrianDilewati;
use App\Events\AntrianDipanggil;
use App\Events\AntrianSelesai;
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
        'waktu_selesai', 'catatan',
    ];

    protected $casts = [
        'tanggal'                 => 'date',
        'waktu_daftar'            => 'datetime',
        'waktu_dipanggil'         => 'datetime',
        'waktu_mulai_skrining'    => 'datetime',
        'waktu_selesai_skrining'  => 'datetime',
        'waktu_selesai'           => 'datetime',
    ];

    /**
     * Auto-dispatch event notifikasi saat status berubah, terlepas dari
     * controller/role mana yang mengubahnya (Perawat, Admin, Monitor).
     * Satu tempat, konsisten, tidak perlu diulang di tiap controller.
     */
    protected static function booted(): void
    {
        static::updated(function (Antrian $antrian) {
            if (!$antrian->wasChanged('status')) {
                return;
            }

            match ($antrian->status) {
                'dipanggil' => event(new AntrianDipanggil($antrian)),
                'selesai'   => event(new AntrianSelesai($antrian)),
                'dilewati'  => event(new AntrianDilewati($antrian)),
                default     => null,
            };
        });
    }

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
