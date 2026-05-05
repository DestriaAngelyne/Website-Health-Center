<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SesiAntrian = satu sesi layanan per hari per poli.
     * Contoh: Poli Umum - Senin 12 Agustus 2024
     * Setiap pagi sistem (atau admin) membuka sesi baru.
     */
    public function up(): void
    {
        Schema::create('sesi_antrian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->constrained('poli')->restrictOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokter')->nullOnDelete();
            $table->date('tanggal');
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->integer('kuota')->default(20);
            $table->integer('nomor_terakhir')->default(0);  // counter nomor antrian
            $table->enum('status', ['menunggu', 'aktif', 'selesai', 'ditutup'])->default('menunggu');
            $table->foreignId('dibuka_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ditutup_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Satu poli hanya boleh punya 1 sesi aktif per hari
            $table->unique(['poli_id', 'tanggal']);
            $table->index(['poli_id', 'tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_antrian');
    }
};
