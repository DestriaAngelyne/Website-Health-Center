<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kunjungan = satu episode rekam medis.
     * Dibuat saat pasien mulai dilayani dokter.
     */
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrian_id')->unique()->constrained('antrian')->restrictOnDelete();
            $table->foreignId('pasien_id')->constrained('pasien')->restrictOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokter')->nullOnDelete();
            $table->foreignId('poli_id')->constrained('poli')->restrictOnDelete();

            $table->date('tanggal_kunjungan');
            $table->string('no_kunjungan', 30)->unique(); // kode unik kunjungan

            // Hasil skrining perawat (tanda vital)
            $table->decimal('berat_badan', 5, 2)->nullable();     // kg
            $table->decimal('tinggi_badan', 5, 2)->nullable();    // cm
            $table->string('tekanan_darah')->nullable();           // contoh: 120/80
            $table->integer('nadi')->nullable();                   // per menit
            $table->integer('suhu')->nullable();                   // celcius * 10 → e.g. 365 = 36.5°C
            $table->integer('respirasi')->nullable();              // per menit
            $table->integer('saturasi_oksigen')->nullable();       // %
            $table->text('keluhan')->nullable();                   // anamnesis awal perawat

            // Rekam medis dokter
            $table->text('anamnesis')->nullable();                 // keluhan subjektif
            $table->text('pemeriksaan_fisik')->nullable();         // objektif
            $table->string('diagnosa_kode')->nullable();           // kode ICD-10 primer
            $table->string('diagnosa_nama')->nullable();
            $table->string('diagnosa_sekunder_kode')->nullable();  // ICD-10 sekunder (opsional)
            $table->string('diagnosa_sekunder_nama')->nullable();
            $table->text('rencana_terapi')->nullable();
            $table->text('catatan_dokter')->nullable();

            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->timestamp('selesai_at')->nullable();
            $table->foreignId('perawat_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pasien_id', 'tanggal_kunjungan']);
            $table->index(['dokter_id', 'tanggal_kunjungan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
