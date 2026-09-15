<?php

namespace App\Http\Controllers\Api\Display;

use App\Http\Controllers\Controller;
use App\Models\Antrian;

class DisplayController extends Controller
{
    /**
     * GET /api/display/antrian
     *
     * Endpoint publik (tanpa login) untuk layar Display TV di ruang tunggu.
     * Dipanggil berkala (polling) oleh AntreanDisplay.vue.
     */
    public function antrian()
    {
        $today = today();

        // Nomor yang sedang dipanggil/diperiksa - ambil yang paling baru dipanggil
        $sedangDilayani = Antrian::whereDate('tanggal', $today)
            ->whereIn('status', ['dipanggil', 'skrining'])
            ->with(['poli', 'dokter'])
            ->orderByDesc('waktu_dipanggil')
            ->first();

        // 5 antrian berikutnya yang masih menunggu, urut nomor
        $berikutnya = Antrian::whereDate('tanggal', $today)
            ->where('status', 'menunggu')
            ->with('poli')
            ->orderBy('nomor_antrian')
            ->take(5)
            ->get()
            ->map(fn ($a) => [
                'nomor' => $this->nomorDisplay($a),
                'poli'  => $a->poli?->nama,
            ]);

        $stats = [
            'total'    => Antrian::whereDate('tanggal', $today)->count(),
            'selesai'  => Antrian::whereDate('tanggal', $today)->where('status', 'selesai')->count(),
            'menunggu' => Antrian::whereDate('tanggal', $today)->where('status', 'menunggu')->count(),
        ];

        return response()->json([
            'data' => [
                'sedang_dilayani' => $sedangDilayani ? [
                    'nomor_antrian' => $this->nomorDisplay($sedangDilayani),
                    'poli'          => $sedangDilayani->poli?->nama,
                    'dokter'        => $sedangDilayani->dokter?->nama,
                ] : null,
                'berikutnya' => $berikutnya,
                'stats'      => $stats,
            ],
        ]);
    }

    private function nomorDisplay(Antrian $a): string
    {
        return $a->kode_antrian . '-' . str_pad((string) $a->nomor_antrian, 3, '0', STR_PAD_LEFT);
    }
}
