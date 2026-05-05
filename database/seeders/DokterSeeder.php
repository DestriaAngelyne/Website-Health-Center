<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokter;
use App\Models\User;
use App\Models\Poli;
    
class DokterSeeder extends Seeder
{
    public function run(): void
    {
        $poliUmum  = Poli::where('kode', 'UMUM')->first();
        $poliGigi  = Poli::where('kode', 'GIGI')->first();
        $poliAnak  = Poli::where('kode', 'ANAK')->first();

        $userDokter1 = User::where('email', 'dokter@puskesmas.com')->first();
        $userDokter2 = User::where('email', 'dokter2@puskesmas.com')->first();

        $dokter = [
            [
                'user_id'   => $userDokter1?->id,
                'poli_id'   => $poliUmum?->id,
                'nip'       => '198501012010011001',
                'nama'      => 'dr. Budi Santoso',
                'spesialis' => 'Dokter Umum',
                'no_hp'     => '081200000004',
                'is_active' => true,
            ],
            [
                'user_id'   => $userDokter2?->id,
                'poli_id'   => $poliGigi?->id,
                'nip'       => '198701012010012002',
                'nama'      => 'dr. Anita Dewi',
                'spesialis' => 'Dokter Gigi',
                'no_hp'     => '081200000005',
                'is_active' => true,
            ],
            [
                'user_id'   => null,
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
