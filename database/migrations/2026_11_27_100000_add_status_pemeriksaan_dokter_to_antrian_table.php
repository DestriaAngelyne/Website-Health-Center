<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE antrian MODIFY status ENUM('menunggu','dipanggil','skrining','siap_diperiksa','diperiksa','selesai','dilewati','batal') NOT NULL DEFAULT 'menunggu'");

        Schema::table('antrian', function (Blueprint $table) {
            $table->text('catatan_dokter')->nullable()->after('catatan');
            $table->timestamp('waktu_mulai_periksa')->nullable()->after('waktu_selesai_skrining');
        });
    }

    public function down(): void
    {
        DB::table('antrian')->whereIn('status', ['siap_diperiksa', 'diperiksa'])->update(['status' => 'skrining']);

        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn(['catatan_dokter', 'waktu_mulai_periksa']);
        });

        DB::statement("ALTER TABLE antrian MODIFY status ENUM('menunggu','dipanggil','skrining','selesai','dilewati','batal') NOT NULL DEFAULT 'menunggu'");
    }
};