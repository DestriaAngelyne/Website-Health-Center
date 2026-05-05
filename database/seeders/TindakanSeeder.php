<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tindakan;

class TindakanSeeder extends Seeder
{
    public function run(): void
    {
        $tindakan = [
            ['kode' => 'T001', 'nama' => 'Pemeriksaan Umum',           'tarif' => 15000],
            ['kode' => 'T002', 'nama' => 'Pemeriksaan Tekanan Darah',  'tarif' => 5000],
            ['kode' => 'T003', 'nama' => 'Pemeriksaan Gula Darah',     'tarif' => 20000],
            ['kode' => 'T004', 'nama' => 'Pemeriksaan Kolesterol',     'tarif' => 25000],
            ['kode' => 'T005', 'nama' => 'Pemeriksaan Asam Urat',      'tarif' => 20000],
            ['kode' => 'T006', 'nama' => 'Injeksi / Suntikan',         'tarif' => 15000],
            ['kode' => 'T007', 'nama' => 'Perawatan Luka',             'tarif' => 25000],
            ['kode' => 'T008', 'nama' => 'Nebulisasi',                 'tarif' => 30000],
            ['kode' => 'T009', 'nama' => 'Pemasangan Infus',           'tarif' => 50000],
            ['kode' => 'T010', 'nama' => 'EKG',                        'tarif' => 35000],
            ['kode' => 'T011', 'nama' => 'Cabut Gigi Susu',            'tarif' => 20000],
            ['kode' => 'T012', 'nama' => 'Cabut Gigi Tetap',           'tarif' => 50000],
            ['kode' => 'T013', 'nama' => 'Tambal Gigi',                'tarif' => 40000],
            ['kode' => 'T014', 'nama' => 'Pembersihan Karang Gigi',    'tarif' => 75000],
            ['kode' => 'T015', 'nama' => 'Imunisasi',                  'tarif' => 25000],
        ];

        foreach ($tindakan as $item) {
            Tindakan::updateOrCreate(
                ['kode' => $item['kode']],
                array_merge($item, ['is_active' => true])
            );
        }
    }
}
