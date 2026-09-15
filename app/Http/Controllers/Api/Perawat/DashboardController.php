<?php

namespace App\Http\Controllers\Api\Perawat;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $hariIni = Carbon::today()->format('Y-m-d');

        $query = Antrian::whereDate('tanggal', $hariIni);

        if ($user && $user->poli_id) {
            $query->where('poli_id', $user->poli_id);
        }

        $antrianHariIni = $query->get();

        // Antrian per poli
        $antrianPerPoli = Antrian::with('poli')
            ->whereDate('tanggal', $hariIni)
            ->get()
            ->groupBy('poli_id')
            ->map(fn($items) => [
                'nama'   => $items->first()->poli?->nama ?? '-',
                'jumlah' => $items->count(),
            ])
            ->values();

        return response()->json([
            'status'           => 'success',
            'hari_ini'         => Carbon::now()->translatedFormat('d F Y'),
            'statistik'        => [
                'total_antrian' => $antrianHariIni->count(),
                'menunggu'      => $antrianHariIni->where('status', 'menunggu')->count(),
                'selesai'       => $antrianHariIni->where('status', 'selesai')->count(),
                'batal'         => $antrianHariIni->where('status', 'batal')->count(),
            ],
            'antrian_per_poli' => $antrianPerPoli,
        ]);
    }
}
