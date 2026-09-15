<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modul rekam medis (Kunjungan, Resep, Rujukan, ICD-10, Kunjungan Tindakan, Tindakan)
     * di luar scope SIANTRI - sistem ini fokus pada antrian & skrining, bukan
     * pemeriksaan medis oleh dokter. Urutan drop mengikuti dependensi foreign key:
     * tabel anak (kunjungan_tindakan, resep, rujukan) dihapus dulu sebelum
     * tabel induknya (kunjungan, tindakan). icd10 berdiri sendiri, aman kapan saja.
     */
    public function up(): void
    {
        Schema::dropIfExists('kunjungan_tindakan');
        Schema::dropIfExists('resep');
        Schema::dropIfExists('rujukan');
        Schema::dropIfExists('kunjungan');
        Schema::dropIfExists('tindakan');
        Schema::dropIfExists('icd10');
    }

    public function down(): void
    {
        // Tabel tidak direkonstruksi otomatis di rollback.
        // Jalankan ulang migration create_*_table yang lama jika perlu dikembalikan.
    }
};
