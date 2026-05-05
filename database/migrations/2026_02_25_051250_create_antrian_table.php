<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_antrian_id')->constrained('sesi_antrian')->restrictOnDelete();
            $table->foreignId('pasien_id')->constrained('pasien')->restrictOnDelete();
            $table->foreignId('poli_id')->constrained('poli')->restrictOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokter')->nullOnDelete();
            $table->foreignId('loket_user_id')->nullable()->constrained('users')->nullOnDelete(); // petugas loket

            $table->string('kode_antrian', 10);             // contoh: A001, B023
            $table->integer('nomor_antrian');
            $table->date('tanggal');

            $table->enum('jenis_kunjungan', ['baru', 'lama'])->default('lama');
            $table->enum('jenis_pasien', ['umum', 'bpjs'])->default('umum');
            $table->enum('status', [
                'menunggu',     // baru daftar
                'dipanggil',    // sedang dipanggil perawat/dokter
                'skrining',     // sedang skrining di perawat
                'dalam_antrian_dokter', // sudah lewat perawat, antri dokter
                'dilayani',     // sedang di dokter
                'selesai',      // selesai semua
                'dilewati',     // tidak hadir saat dipanggil
                'batal',        // dibatalkan
            ])->default('menunggu');

            $table->timestamp('waktu_daftar')->useCurrent();
            $table->timestamp('waktu_dipanggil')->nullable();
            $table->timestamp('waktu_mulai_skrining')->nullable();
            $table->timestamp('waktu_selesai_skrining')->nullable();
            $table->timestamp('waktu_mulai_dilayani')->nullable();
            $table->timestamp('waktu_selesai')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['sesi_antrian_id', 'nomor_antrian']);
            $table->index(['poli_id', 'tanggal', 'status']);
            $table->index(['pasien_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};
