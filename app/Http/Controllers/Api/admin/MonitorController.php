<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Poli;
use App\Models\SesiAntrian;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MonitorController extends Controller
{
    /**
     * GET /api/admin/antrian/monitor?poli_id=
     */
    public function index(Request $request)
    {
        $today  = Carbon::today();
        $poliId = $request->query('poli_id');

        // ── 1. Poli list untuk dropdown ───────────────────────────────────
        $poliList = Poli::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        // ── 2. Sesi hari ini ──────────────────────────────────────────────
        $sesiQuery = SesiAntrian::with(['poli:id,nama', 'dokter:id,nama'])
            ->whereDate('tanggal', $today);

        if ($poliId) {
            $sesiQuery->where('poli_id', $poliId);
        }

        $sesiList = $sesiQuery->orderBy('jam_buka')->get()->map(function ($s) {
            $terisi = Antrian::where('sesi_antrian_id', $s->id)
                ->whereNotIn('status', ['batal'])
                ->count();

            return [
                'id'        => $s->id,
                'poli'      => $s->poli?->nama ?? '-',
                'dokter'    => $s->dokter?->nama ?? '-',
                'jam_buka'  => substr($s->jam_buka  ?? '', 0, 5),
                'jam_tutup' => substr($s->jam_tutup ?? '', 0, 5),
                'kuota'     => $s->kuota ?? 0,
                'terisi'    => $terisi,
                'status'    => $s->status ?? 'aktif',
            ];
        });

        // ── 3. Antrian hari ini ───────────────────────────────────────────
        $antrianQuery = Antrian::with([
                'pasien:id,nama',
                'poli:id,nama',
                'dokter:id,nama',
            ])
            ->whereDate('tanggal', $today);

        if ($poliId) {
            $antrianQuery->where('poli_id', $poliId);
        }

        $antrianList = $antrianQuery
            ->orderBy('nomor_antrian')
            ->get()
            ->map(function ($a) {
                $kode  = $a->kode_antrian  ?? '';
                $nomor = $a->nomor_antrian ?? 0;

                return [
                    'id'            => $a->id,
                    'nomor_display' => $kode . '-' . str_pad($nomor, 3, '0', STR_PAD_LEFT),
                    'pasien'        => $a->pasien?->nama ?? '-',
                    'poli'          => $a->poli?->nama ?? '-',
                    'dokter'        => $a->dokter?->nama ?? '-',
                    'jaminan'       => $a->jenis_kunjungan === 'bpjs' ? 'BPJS' : 'Umum',
                    'status'        => $a->status ?? 'menunggu',
                    'waktu_daftar'  => $a->waktu_daftar
                                        ? Carbon::parse($a->waktu_daftar)->format('H:i')
                                        : ($a->created_at
                                            ? Carbon::parse($a->created_at)->format('H:i')
                                            : '-'),
                    'waktu_selesai' => $a->waktu_selesai
                                        ? Carbon::parse($a->waktu_selesai)->format('H:i')
                                        : null,
                ];
            });

        // ── 4. Statistik ──────────────────────────────────────────────────
        $base = fn() => Antrian::whereDate('tanggal', $today)
            ->when($poliId, fn($q) => $q->where('poli_id', $poliId));

        $statistik = [
            'total'     => $base()->count(),
            'menunggu'  => $base()->where('status', 'menunggu')->count(),
            'dipanggil' => $base()->whereIn('status', ['dipanggil', 'skrining', 'dalam_antrian_dokter'])->count(),
            'dilayani'  => $base()->where('status', 'dilayani')->count(),
            'selesai'   => $base()->where('status', 'selesai')->count(),
            'batal'     => $base()->where('status', 'batal')->count(),
        ];

        return response()->json([
            'data' => [
                'poli_list' => $poliList,
                'sesi'      => $sesiList,
                'antrian'   => $antrianList,
                'statistik' => $statistik,
            ]
        ]);
    }

    /**
     * PATCH /api/admin/antrian/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:dipanggil,skrining,dalam_antrian_dokter,dilayani,selesai,dilewati,batal',
        ]);

        $antrian         = Antrian::findOrFail($id);
        $antrian->status = $request->status;

        if (in_array($request->status, ['selesai', 'batal', 'dilewati'])) {
            $antrian->waktu_selesai = now();
        }

        $antrian->save();

        return response()->json([
            'message' => 'Status diperbarui.',
            'data'    => $antrian,
        ]);
    }
}
