<?php

namespace App\Http\Controllers\Api\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     * GET /api/pasien/riwayat
     * Ambil riwayat kunjungan pasien (antrian yang sudah selesai)
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $pasien = $user->pasien;

        if (!$pasien) {
            return response()->json(['message' => 'Data pasien tidak ditemukan.'], 404);
        }

        $limit = $request->query('limit', 10);

        $riwayat = Antrian::with(['poli', 'dokter', 'sesiAntrian'])
            ->where('pasien_id', $pasien->id)
            ->where('status', 'selesai')
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate($limit);

        $data = $riwayat->map(function ($a) {
            return [
                'id'           => $a->id,
                'tanggal'      => $a->tanggal,
                'tanggal_fmt'  => \Carbon\Carbon::parse($a->tanggal)
                                    ->locale('id')->isoFormat('D MMMM Y'),
                'nomor_display'=> ($a->kode_antrian ?? 'A') . '-' . str_pad($a->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'poli'         => $a->poli->nama         ?? '-',
                'dokter'       => $a->dokter->nama        ?? '-',
                'diagnosa'     => $a->diagnosa            ?? null,
                'catatan'      => $a->catatan             ?? null,
                'jaminan'      => $a->jenis_pasien        ?? '-',
                'status'       => $a->status,
            ];
        });

        return response()->json([
            'data'  => $data,
            'total' => $riwayat->total(),
            'page'  => $riwayat->currentPage(),
        ]);
    }

    /**
     * GET /api/pasien/riwayat/{id}
     * Detail satu riwayat kunjungan
     */
    public function show($id)
    {
        $user   = Auth::user();
        $pasien = $user->pasien;

        if (!$pasien) {
            return response()->json(['message' => 'Data pasien tidak ditemukan.'], 404);
        }

        $antrian = Antrian::with(['poli', 'dokter', 'sesiAntrian'])
            ->where('pasien_id', $pasien->id)
            ->where('status', 'selesai')
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'           => $antrian->id,
                'tanggal'      => $antrian->tanggal,
                'tanggal_fmt'  => \Carbon\Carbon::parse($antrian->tanggal)
                                    ->locale('id')->isoFormat('dddd, D MMMM Y'),
                'nomor_display'=> ($antrian->kode_antrian ?? 'A') . '-' . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'poli'         => $antrian->poli->nama    ?? '-',
                'dokter'       => $antrian->dokter->nama  ?? '-',
                'jam_buka'     => $antrian->sesiAntrian?->jam_buka    ? substr($antrian->sesiAntrian->jam_buka, 0, 5)  : '-',
                'jam_tutup'    => $antrian->sesiAntrian?->jam_tutup   ? substr($antrian->sesiAntrian->jam_tutup, 0, 5) : '-',
                'diagnosa'     => $antrian->diagnosa      ?? null,
                'catatan'      => $antrian->catatan       ?? null,
                'resep'        => $antrian->resep         ?? null,
                'jaminan'      => $antrian->jenis_pasien  ?? '-',
                'status'       => $antrian->status,
            ],
        ]);
    }
}
