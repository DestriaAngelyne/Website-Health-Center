<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Administrator',
                'email'    => 'admin@puskesmas.com',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'no_hp'    => '081200000001',
                'is_active'=> true,
            ],
            [
                'name'     => 'Petugas Loket',
                'email'    => 'loket@puskesmas.com',
                'username' => 'loket',
                'password' => Hash::make('password'),
                'role'     => 'loket',
                'no_hp'    => '081200000002',
                'is_active'=> true,
            ],
            [
                'name'     => 'Perawat Sari',
                'email'    => 'perawat@puskesmas.com',
                'username' => 'perawat',
                'password' => Hash::make('password'),
                'role'     => 'perawat',
                'no_hp'    => '081200000003',
                'is_active'=> true,
            ],
            [
                'name'     => 'dr. Budi Santoso',
                'email'    => 'dokter@puskesmas.com',
                'username' => 'dokter',
                'password' => Hash::make('password'),
                'role'     => 'dokter',
                'no_hp'    => '081200000004',
                'is_active'=> true,
            ],
            [
                'name'     => 'dr. Anita Dewi',
                'email'    => 'dokter2@puskesmas.com',
                'username' => 'dokter2',
                'password' => Hash::make('password'),
                'role'     => 'dokter',
                'no_hp'    => '081200000005',
                'is_active'=> true,
            ],
            [
                'name'     => 'Pasien Demo',
                'email'    => 'pasien@puskesmas.com',
                'username' => 'pasien',
                'password' => Hash::make('password'),
                'role'     => 'pasien',
                'no_hp'    => '081200000006',
                'is_active'=> true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
