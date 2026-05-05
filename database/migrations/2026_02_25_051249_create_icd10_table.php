<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('icd10', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();           // contoh: A00, A00.0
            $table->string('nama');                         // nama penyakit
            $table->string('nama_indonesia')->nullable();   // terjemahan Indonesia
            $table->string('kategori')->nullable();         // Chapter ICD-10
            $table->timestamps();

            $table->index('kode');
            $table->fullText(['nama', 'nama_indonesia']);   // untuk pencarian cepat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('icd10');
    }
};
