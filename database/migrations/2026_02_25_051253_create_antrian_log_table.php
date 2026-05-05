<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mencatat setiap perubahan status antrian sebagai audit trail.
     * Berguna untuk analisa waktu tunggu, laporan, dan debugging.
     */
    public function up(): void
    {
        Schema::create('antrian_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrian_id')->constrained('antrian')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // siapa yang ubah
            $table->string('status_sebelum')->nullable();
            $table->string('status_sesudah');
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['antrian_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrian_log');
    }
};
