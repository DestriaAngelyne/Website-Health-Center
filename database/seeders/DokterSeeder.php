<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokter;
use App\Models\Poli;

class DokterSeeder extends Seeder
{
    public function run(): void
    {
        $poliUmum  = Poli::where('kode', 'UMUM')->first();
        $poliGigi  = Poli::where('kode', 'GIGI')->first();
        $poliAnak  = Poli::where('kode', 'ANAK')->first();

        $dokter = [
            [
                'poli_id'   => $poliUmum?->id,
                'nip'       => '198501012010011001',
                'nama'      => 'dr. Budi Santoso',
                'spesialis' => 'Dokter Umum',
                'no_hp'     => '081200000004',
                'is_active' => true,
            ],
            [
                'poli_id'   => $poliGigi?->id,
                'nip'       => '198701012010012002',
                'nama'      => 'dr. Anita Dewi',
                'spesialis' => 'Dokter Gigi',
                'no_hp'     => '081200000005',
                'is_active' => true,
            ],
            [
                'poli_id'   => $poliAnak?->id,
                'nip'       => '199001012015013003',
                'nama'      => 'dr. Hendra Wijaya',
                'spesialis' => 'Dokter Anak',
                'no_hp'     => '081200000007',
                'is_active' => true,
            ],
        ];

        foreach ($dokter as $item) {
            Dokter::updateOrCreate(
                ['nip' => $item['nip']],
                $item
            );
        }
    }
}
