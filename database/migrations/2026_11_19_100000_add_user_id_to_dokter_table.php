<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Revisi pembimbing: dokter kembali punya akun login dan dashboard sendiri.
     * Membalik migration 2026_09_10_014400_drop_user_id_from_dokter_table.
     * user_id nullable agar dokter yang sudah ada tidak error sebelum akunnya dibuat.
     */
    public function up(): void
    {
        Schema::table('dokter', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dokter', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};