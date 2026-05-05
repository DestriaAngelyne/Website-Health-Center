<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungan')->cascadeOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokter')->nullOnDelete();
            $table->string('no_resep', 30)->unique();
            $table->text('catatan')->nullable();                // catatan umum resep
            $table->enum('status', ['menunggu', 'disiapkan', 'selesai'])->default('menunggu');
            $table->timestamp('diserahkan_at')->nullable();
            $table->timestamps();

            $table->index('kunjungan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
