<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sederhanakan alur status antrian - SIANTRI fokus pada antrian & skrining,
     * bukan pemeriksaan dokter. Status baru: menunggu, dipanggil, skrining,
     * selesai, dilewati, batal.
     *
     * Data lama dinormalisasi dulu sebelum enum diubah, supaya tidak ada baris
     * yang kepental ke status kosong (MySQL non-strict) atau bikin ALTER gagal
     * (MySQL strict mode).
     */
    public function up(): void
    {
        // 1. Normalisasi data lama ke status baru yang setara
        DB::table('antrian')->where('status', 'dalam_antrian_dokter')->update(['status' => 'skrining']);
        DB::table('antrian')->where('status', 'dilayani')->update(['status' => 'selesai']);

        // 2. Ubah definisi enum
        DB::statement("ALTER TABLE antrian MODIFY status ENUM(
            'menunggu', 'dipanggil', 'skrining', 'selesai', 'dilewati', 'batal'
        ) NOT NULL DEFAULT 'menunggu'");

        // 3. Hapus kolom timestamp yang cuma dipakai status 'dilayani'
        if (Schema::hasColumn('antrian', 'waktu_mulai_dilayani')) {
            Schema::table('antrian', function ($table) {
                $table->dropColumn('waktu_mulai_dilayani');
            });
        }
    }

    public function down(): void
    {
        Schema::table('antrian', function ($table) {
            $table->timestamp('waktu_mulai_dilayani')->nullable()->after('waktu_selesai_skrining');
        });

        DB::statement("ALTER TABLE antrian MODIFY status ENUM(
            'menunggu', 'dipanggil', 'skrining', 'dalam_antrian_dokter', 'dilayani', 'selesai', 'dilewati', 'batal'
        ) NOT NULL DEFAULT 'menunggu'");
    }
};
