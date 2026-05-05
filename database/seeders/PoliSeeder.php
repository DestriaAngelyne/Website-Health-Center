<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Poli;

class PoliSeeder extends Seeder
{
    public function run(): void
    {
        $poli = [
            [
                'kode'       => 'UMUM',
                'nama'       => 'Poli Umum',
                'singkatan'  => 'A',
                'keterangan' => 'Pelayanan kesehatan umum',
                'is_active'  => true,
            ],
            [
                'kode'       => 'GIGI',
                'nama'       => 'Poli Gigi',
                'singkatan'  => 'B',
                'keterangan' => 'Pelayanan kesehatan gigi dan mulut',
                'is_active'  => true,
            ],
            [
                'kode'       => 'KIA',
                'nama'       => 'Poli KIA',
                'singkatan'  => 'C',
                'keterangan' => 'Kesehatan Ibu dan Anak',
                'is_active'  => true,
            ],
            [
                'kode'       => 'ANAK',
                'nama'       => 'Poli Anak',
                'singkatan'  => 'D',
                'keterangan' => 'Pelayanan kesehatan anak',
                'is_active'  => true,
            ],
            [
                'kode'       => 'MATA',
                'nama'       => 'Poli Mata',
                'singkatan'  => 'E',
                'keterangan' => 'Pelayanan kesehatan mata',
                'is_active'  => true,
            ],
            [
                'kode'       => 'LANSIA',
                'nama'       => 'Poli Lansia',
                'singkatan'  => 'F',
                'keterangan' => 'Pelayanan kesehatan lanjut usia',
                'is_active'  => true,
            ],
        ];

        foreach ($poli as $item) {
            Poli::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}
