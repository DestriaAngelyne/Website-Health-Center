<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat';

    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'kategori',
        'satuan',
        'stok',
        'stok_minimum',
        'harga',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'stok'         => 'integer',
        'stok_minimum' => 'integer',
        'harga'        => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    // Stok kritis
    public function getIsKritisAttribute(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }

    // Generate kode otomatis: OBT-0001
    public static function generateKode(): string
    {
        $last = self::orderByDesc('id')->first();
        $num  = $last ? ((int) substr($last->kode_obat, 4)) + 1 : 1;
        return 'OBT-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
