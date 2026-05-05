<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        $obat = [
            ['kode' => 'OBT001', 'nama' => 'Paracetamol 500mg',     'generik' => 'Paracetamol',    'satuan' => 'tablet',  'kategori' => 'Analgesik',    'stok' => 500, 'harga' => 500],
            ['kode' => 'OBT002', 'nama' => 'Amoxicillin 500mg',     'generik' => 'Amoxicillin',    'satuan' => 'kapsul',  'kategori' => 'Antibiotik',   'stok' => 300, 'harga' => 1500],
            ['kode' => 'OBT003', 'nama' => 'Antasida Doen',         'generik' => 'Antasida',       'satuan' => 'tablet',  'kategori' => 'Antasida',     'stok' => 400, 'harga' => 300],
            ['kode' => 'OBT004', 'nama' => 'Ibuprofen 400mg',       'generik' => 'Ibuprofen',      'satuan' => 'tablet',  'kategori' => 'Analgesik',    'stok' => 350, 'harga' => 800],
            ['kode' => 'OBT005', 'nama' => 'Cetirizine 10mg',       'generik' => 'Cetirizine',     'satuan' => 'tablet',  'kategori' => 'Antihistamin', 'stok' => 200, 'harga' => 1000],
            ['kode' => 'OBT006', 'nama' => 'Omeprazole 20mg',       'generik' => 'Omeprazole',     'satuan' => 'kapsul',  'kategori' => 'Antasida',     'stok' => 150, 'harga' => 2000],
            ['kode' => 'OBT007', 'nama' => 'Metformin 500mg',       'generik' => 'Metformin',      'satuan' => 'tablet',  'kategori' => 'Antidiabetik', 'stok' => 200, 'harga' => 1200],
            ['kode' => 'OBT008', 'nama' => 'Amlodipine 5mg',        'generik' => 'Amlodipine',     'satuan' => 'tablet',  'kategori' => 'Antihipertensi','stok' => 250, 'harga' => 1500],
            ['kode' => 'OBT009', 'nama' => 'Vitamin C 500mg',       'generik' => 'Asam Askorbat',  'satuan' => 'tablet',  'kategori' => 'Vitamin',      'stok' => 600, 'harga' => 500],
            ['kode' => 'OBT010', 'nama' => 'ORS / Oralit',          'generik' => 'Oralit',         'satuan' => 'sachet',  'kategori' => 'Rehidrasi',    'stok' => 300, 'harga' => 1000],
            ['kode' => 'OBT011', 'nama' => 'Salbutamol 4mg',        'generik' => 'Salbutamol',     'satuan' => 'tablet',  'kategori' => 'Bronkodilator','stok' => 150, 'harga' => 800],
            ['kode' => 'OBT012', 'nama' => 'Dexamethasone 0.5mg',   'generik' => 'Dexamethasone',  'satuan' => 'tablet',  'kategori' => 'Kortikosteroid','stok' => 200, 'harga' => 600],
            ['kode' => 'OBT013', 'nama' => 'Cotrimoxazole 480mg',   'generik' => 'Cotrimoxazole',  'satuan' => 'tablet',  'kategori' => 'Antibiotik',   'stok' => 250, 'harga' => 700],
            ['kode' => 'OBT014', 'nama' => 'Antimo',                'generik' => 'Dimenhydrinate', 'satuan' => 'tablet',  'kategori' => 'Antiemetik',   'stok' => 100, 'harga' => 2000],
            ['kode' => 'OBT015', 'nama' => 'Betadine 30ml',         'generik' => 'Povidone Iodine','satuan' => 'botol',   'kategori' => 'Antiseptik',   'stok' => 80,  'harga' => 15000],
        ];

        foreach ($obat as $item) {
            Obat::updateOrCreate(
                ['kode' => $item['kode']],
                array_merge($item, [
                    'stok_minimum' => 20,
                    'is_active'    => true,
                ])
            );
        }
    }
}
