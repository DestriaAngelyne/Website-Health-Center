<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Restrukturisasi role users:
     * - admin lama       -> superadmin
     * - loket, perawat   -> admin
     *
     * Enum lama: admin, loket, perawat, dokter, pasien
     * Enum baru: superadmin, admin, dokter, pasien
     *
     * Urutan penting (MySQL strict mode menolak UPDATE ke nilai yang belum ada di enum):
     * 1. perlebar enum dulu supaya memuat nilai lama DAN baru
     * 2. admin lama jadi superadmin DULU, baru loket/perawat jadi admin
     *    (kalau dibalik, semua akan berakhir jadi superadmin)
     * 3. kunci enum ke definisi final
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'loket', 'perawat', 'dokter', 'pasien', 'superadmin') NOT NULL DEFAULT 'pasien'");

        DB::table('users')->where('role', 'admin')->update(['role' => 'superadmin']);
        DB::table('users')->whereIn('role', ['loket', 'perawat'])->update(['role' => 'admin']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'dokter', 'pasien') NOT NULL DEFAULT 'pasien'");
    }

    /**
     * Pembedaan loket vs perawat tidak bisa dikembalikan; semua admin
     * dikembalikan sebagai loket.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'loket', 'perawat', 'dokter', 'pasien', 'superadmin') NOT NULL DEFAULT 'pasien'");

        DB::table('users')->where('role', 'admin')->update(['role' => 'loket']);
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'loket', 'perawat', 'dokter', 'pasien') NOT NULL DEFAULT 'pasien'");
    }
};
