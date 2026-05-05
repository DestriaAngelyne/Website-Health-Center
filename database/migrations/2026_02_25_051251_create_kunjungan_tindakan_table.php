<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan_tindakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungan')->cascadeOnDelete();
            $table->foreignId('tindakan_id')->constrained('tindakan')->restrictOnDelete();
            $table->integer('jumlah')->default(1);
            $table->decimal('tarif_satuan', 12, 2)->default(0); // snapshot tarif saat itu
            $table->decimal('total', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('kunjungan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan_tindakan');
    }
};
