<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('poli_id')->constrained('poli')->restrictOnDelete();
            $table->string('nip', 30)->unique()->nullable();
            $table->string('nama');
            $table->string('spesialis')->nullable();
            $table->string('str_number', 50)->nullable();   // Surat Tanda Registrasi
            $table->string('no_hp')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokter');
    }
};
