<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,      // 1. user dulu (karena dokter & pasien butuh user_id)
            PoliSeeder::class,      // 2. poli (karena dokter butuh poli_id)
            DokterSeeder::class,    // 3. dokter
            TindakanSeeder::class,  // 4. tindakan
            ObatSeeder::class,      // 5. obat
            PasienSeeder::class,    // 6. pasien
        ]);
    }
}
