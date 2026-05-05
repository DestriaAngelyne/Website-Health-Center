<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;
use App\Models\User;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        $userPasien = User::where('email', 'pasien@puskesmas.com')->first();

        $pasien = [
            [
                'user_id'          => $userPasien?->id,
                'no_rm'            => 'RM-2024-00001',
                'nik'              => '3201010101900001',
                'nama'             => 'Budi Setiawan',
                'jenis_kelamin'    => 'L',
                'tanggal_lahir'    => '1990-01-01',
                'tempat_lahir'     => 'Jakarta',
                'alamat'           => 'Jl. Merdeka No. 1',
                'kota'             => 'Jakarta',
                'no_hp'            => '081200000006',
                'golongan_darah'   => 'O',
                'jenis_pasien'     => 'umum',
                'is_active'        => true,
            ],
            [
                'user_id'          => null,
                'no_rm'            => 'RM-2024-00002',
                'nik'              => '3201010101850002',
                'no_bpjs'          => '0001234567890',
                'nama'             => 'Siti Rahayu',
                'jenis_kelamin'    => 'P',
                'tanggal_lahir'    => '1985-06-15',
                'tempat_lahir'     => 'Bandung',
                'alamat'           => 'Jl. Kemuning No. 5',
                'kota'             => 'Bandung',
                'no_hp'            => '081200000008',
                'golongan_darah'   => 'A',
                'jenis_pasien'     => 'bpjs',
                'is_active'        => true,
            ],
            [
                'user_id'          => null,
                'no_rm'            => 'RM-2024-00003',
                'nik'              => '3201010101920003',
                'nama'             => 'Ahmad Fauzi',
                'jenis_kelamin'    => 'L',
                'tanggal_lahir'    => '1992-03-20',
                'tempat_lahir'     => 'Surabaya',
                'alamat'           => 'Jl. Veteran No. 10',
                'kota'             => 'Surabaya',
                'no_hp'            => '081200000009',
                'golongan_darah'   => 'B',
                'jenis_pasien'     => 'umum',
                'is_active'        => true,
            ],
        ];

        foreach ($pasien as $item) {
            Pasien::updateOrCreate(
                ['no_rm' => $item['no_rm']],
                $item
            );
        }
    }
}
