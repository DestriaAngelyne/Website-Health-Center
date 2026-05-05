<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('resep')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obat')->restrictOnDelete();
            $table->string('nama_obat');                    // snapshot nama obat
            $table->integer('jumlah');
            $table->string('satuan', 30);                   // snapshot satuan
            $table->string('dosis')->nullable();            // contoh: 3x1, 2x1/2
            $table->string('aturan_pakai')->nullable();     // contoh: sesudah makan
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('resep_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_detail');
    }
};
