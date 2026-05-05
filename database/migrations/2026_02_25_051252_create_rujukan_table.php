<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rujukan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungan')->cascadeOnDelete();
            $table->foreignId('pasien_id')->constrained('pasien')->restrictOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokter')->nullOnDelete();

            $table->string('no_rujukan', 30)->unique();
            $table->date('tanggal_rujukan');
            $table->date('tanggal_berlaku')->nullable();    // masa berlaku rujukan

            $table->string('faskes_tujuan');                // nama RS / faskes tujuan
            $table->string('poli_tujuan')->nullable();      // spesialis tujuan
            $table->string('diagnosa_kode')->nullable();    // ICD-10
            $table->string('diagnosa_nama')->nullable();
            $table->text('alasan_rujukan');
            $table->text('terapi_yang_telah_diberikan')->nullable();
            $table->text('catatan')->nullable();

            $table->enum('jenis', ['internal', 'eksternal'])->default('eksternal');
            $table->enum('status', ['aktif', 'digunakan', 'kadaluarsa', 'dibatalkan'])->default('aktif');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['pasien_id', 'tanggal_rujukan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rujukan');
    }
};
