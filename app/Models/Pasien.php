<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use SoftDeletes;

    protected $table = 'pasien';

    protected $fillable = [
        'user_id', 'no_rm', 'nik', 'no_bpjs', 'nama',
        'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir',
        'alamat', 'kelurahan', 'kecamatan', 'kota', 'provinsi',
        'no_hp', 'golongan_darah', 'agama', 'pekerjaan',
        'status_pernikahan', 'nama_wali', 'no_hp_wali',
        'jenis_pasien', 'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active'     => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function antrian(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }
}
