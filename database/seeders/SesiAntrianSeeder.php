<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SesiAntrian;
use App\Models\Dokter;
use Carbon\Carbon;

class SesiAntrianSeeder extends Seeder
{
    /**
     * Generate sesi antrian otomatis:
     * - Senin s/d Sabtu
     * - 4 minggu ke depan dari hari ini
     * - 2 sesi per hari: Pagi (08:00–12:00) & Siang (13:00–15:00)
     * - Untuk semua dokter aktif
     */
    public function run(): void
    {
        // 1 sesi per poli per tanggal (karena ada unique constraint poli_id+tanggal)
        $dokterList = Dokter::where('is_active', true)->get();

        if ($dokterList->isEmpty()) {
            $this->command->warn('Tidak ada dokter aktif. Jalankan DokterSeeder terlebih dahulu.');
            return;
        }

        $today   = Carbon::today();
        $created = 0;
        $skipped = 0;

        for ($i = 0; $i <= 6; $i++) {
            $tanggal = $today->copy()->addDays($i);

            // Skip Minggu
            if ($tanggal->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }

            foreach ($dokterList as $dokter) {
                $exists = SesiAntrian::where('poli_id', $dokter->poli_id)
                    ->where('tanggal', $tanggal->format('Y-m-d'))
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                SesiAntrian::create([
                    'poli_id'        => $dokter->poli_id,
                    'dokter_id'      => $dokter->id,
                    'tanggal'        => $tanggal->format('Y-m-d'),
                    'jam_buka'       => '08:00:00',
                    'jam_tutup'      => '12:00:00',
                    'kuota'          => 20,
                    'nomor_terakhir' => 0,
                    'status'         => 'aktif',
                ]);

                $created++;
            }
        }

        $this->command->info("✅ {$created} sesi dibuat, {$skipped} dilewati (sudah ada).");
        $this->command->info("   Periode : {$today->format('d M Y')} – {$today->copy()->addDays(6)->format('d M Y')}");
        $this->command->info("   Dokter  : {$dokterList->count()} dokter aktif");
        $this->command->info("   Sesi    : Pagi 08:00–12:00 (kuota 20) & Siang 13:00–15:00 (kuota 15)");
    }
}
