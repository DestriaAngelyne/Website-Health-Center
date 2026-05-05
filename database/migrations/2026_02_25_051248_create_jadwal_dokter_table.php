<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('dokter')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('poli')->restrictOnDelete();
            $table->enum('hari', [
                'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'
            ]);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kuota')->default(20);          // maks pasien per sesi
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Satu dokter tidak boleh jadwal dobel di hari & jam yang sama
            $table->unique(['dokter_id', 'hari', 'jam_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_dokter');
    }
};
