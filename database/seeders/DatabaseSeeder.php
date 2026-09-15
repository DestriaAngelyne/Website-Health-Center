<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,      // 1. user dulu (karena pasien butuh user_id)
            PoliSeeder::class,      // 2. poli (karena dokter butuh poli_id)
            DokterSeeder::class,    // 3. dokter
            TindakanSeeder::class,  // 4. tindakan
            PasienSeeder::class,    // 5. pasien
        ]);
    }
}
