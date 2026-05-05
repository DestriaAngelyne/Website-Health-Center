<?php

namespace App\Http\Controllers\Api\Publik;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use Illuminate\Http\Request;

class AntrianStatusController extends Controller
{
    /**
     * GET /api/antrian/status?nomor=F-001
     * Public endpoint — tidak butuh login
     */
    public function show(Request $request)
    {
        $nomor = trim($request->query('nomor', ''));

        if (empty($nomor)) {
            return response()->json([
                'message' => 'Parameter nomor wajib diisi.',
            ], 422);
        }

        // Cari antrian berdasarkan nomor_display atau gabungan kode+nomor
        $antrian = Antrian::with(['sesiAntrian.dokter', 'sesiAntrian.poli', 'pasien'])
            ->where(function ($q) use ($nomor) {
                // Format: F-001 → kode_antrian=F, nomor_antrian=1
                $q->whereRaw("CONCAT(kode_antrian, '-', LPAD(nomor_antrian, 3, '0')) = ?", [$nomor]);

                // Kalau ada kolom nomor_display langsung
                if (\Schema::hasColumn('antrians', 'nomor_display')) {
                    $q->orWhere('nomor_display', $nomor);
                }
            })
            ->first();

        if (! $antrian) {
            return response()->json([
                'message' => "Nomor antrian \"{$nomor}\" tidak ditemukan.",
            ], 404);
        }

        $sesi   = $antrian->sesiAntrian;
        $dokter = $sesi?->dokter;
        $poli   = $sesi?->poli;
        $pasien = $antrian->pasien;

        // Hitung posisi dalam antrian (berapa yang masih menunggu di depan)
        $posisi = null;
        $totalAntrian   = 0;
        $sudahDilayani  = 0;

        if ($sesi && $antrian->status === 'menunggu') {
            $posisi = Antrian::where('sesi_antrian_id', $sesi->id)
                ->where('status', 'menunggu')
                ->where('nomor_antrian', '<', $antrian->nomor_antrian)
                ->count();

            $totalAntrian = Antrian::where('sesi_antrian_id', $sesi->id)
                ->whereNotIn('status', ['batal'])
                ->count();

            $sudahDilayani = Antrian::where('sesi_antrian_id', $sesi->id)
                ->whereIn('status', ['selesai', 'dilayani'])
                ->count();
        }

        return response()->json([
            'data' => [
                // Identitas antrian
                'id'             => $antrian->id,
                'nomor_antrian'  => $antrian->nomor_antrian,
                'kode_antrian'   => $antrian->kode_antrian,
                'nomor_display'  => $antrian->kode_antrian . '-' . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'status'         => $antrian->status,
                'jaminan'        => $antrian->jaminan,

                // Info pasien (nama saja, tidak expose data sensitif)
                'nama_pasien'    => $pasien?->nama_lengkap ?? $pasien?->nama ?? '-',

                // Info sesi & jadwal
                'tanggal'        => $sesi?->tanggal,
                'jam_buka'       => $sesi ? substr($sesi->jam_buka,  0, 5) : null,
                'jam_tutup'      => $sesi ? substr($sesi->jam_tutup, 0, 5) : null,

                // Info poli & dokter
                'nama_poli'      => $poli?->nama ?? $poli?->nama_poli ?? '-',
                'nama_dokter'    => $dokter?->nama ?? $dokter?->nama_dokter ?? '-',

                // Posisi antrian
                'posisi'         => $posisi,
                'total_antrian'  => $totalAntrian,
                'sudah_dilayani' => $sudahDilayani,
            ],
        ]);
    }
}
