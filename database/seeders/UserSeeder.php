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
            // Superadmin - mengelola sistem (users, master data, laporan)
            [
                'name'     => 'Superadmin',
                'email'    => 'superadmin@puskesmas.com',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
                'no_hp'    => '081200000000',
                'is_active'=> true,
            ],
            // Admin Operasional - mengurus operasional (pendaftaran, sesi, antrian, skrining)
            [
                'name'     => 'Administrator',
                'email'    => 'admin@puskesmas.com',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'no_hp'    => '081200000001',
                'is_active'=> true,
            ],
            // Pasien Demo
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
