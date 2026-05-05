<?php
// KunjunganTindakan.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KunjunganTindakan extends Model
{
    protected $table = 'kunjungan_tindakan';
    protected $fillable = ['kunjungan_id', 'tindakan_id', 'jumlah', 'tarif_satuan', 'total', 'keterangan'];
    protected $casts = ['jumlah' => 'integer', 'tarif_satuan' => 'decimal:2', 'total' => 'decimal:2'];

    public function kunjungan() { return $this->belongsTo(Kunjungan::class); }
    public function tindakan()  { return $this->belongsTo(Tindakan::class); }
}
