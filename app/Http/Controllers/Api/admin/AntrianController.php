<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Poli;
use App\Models\SesiAntrian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    // GET /admin/antrian/monitor
    public function monitor(Request $request)
    {
        $today = Carbon::today();
        $poli_id = $request->poli_id;

        // Semua poli aktif
        $poliList = Poli::where('is_active', true)->orderBy('nama')->get(['id', 'nama']);

        // Sesi hari ini
        $sesiQuery = SesiAntrian::whereDate('tanggal', $today)
            ->with(['poli', 'dokter']);
        if ($poli_id) $sesiQuery->where('poli_id', $poli_id);
        $sesiList = $sesiQuery->get()->map(fn($s) => [
            'id'          => $s->id,
            'poli'        => $s->poli?->nama,
            'dokter'      => $s->dokter?->nama,
            'jam_buka'    => $s->jam_buka,
            'jam_tutup'   => $s->jam_tutup,
            'kuota'       => $s->kuota,
            'terisi'      => $s->terisi ?? Antrian::where('sesi_antrian_id', $s->id)->whereNotIn('status', ['batal'])->count(),
            'status'      => $s->status,
        ]);

        // Antrian hari ini
        $antrianQuery = Antrian::whereDate('tanggal', $today)
            ->with(['pasien', 'poli', 'dokter'])
            ->orderBy('nomor_antrian');
        if ($poli_id) $antrianQuery->where('poli_id', $poli_id);
        $antrian = $antrianQuery->get()->map(fn($a) => [
            'id'            => $a->id,
            'nomor_display' => $a->kode_antrian . '-' . str_pad($a->nomor_antrian, 3, '0', STR_PAD_LEFT),
            'kode_antrian'  => $a->kode_antrian,
            'nomor_antrian' => $a->nomor_antrian,
            'pasien'        => $a->pasien?->nama ?? '-',
            'poli'          => $a->poli?->nama ?? '-',
            'dokter'        => $a->dokter?->nama ?? '-',
            'status'        => $a->status,
            'jenis_pasien'  => $a->jenis_pasien,
            'waktu_daftar'  => $a->waktu_daftar?->format('H:i'),
            'waktu_dipanggil' => $a->waktu_dipanggil?->format('H:i'),
            'waktu_selesai' => $a->waktu_selesai?->format('H:i'),
        ]);

        // Statistik per status
        $statusCount = Antrian::whereDate('tanggal', $today)
            ->when($poli_id, fn($q) => $q->where('poli_id', $poli_id))
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'success'     => true,
            'data'        => [
                'poli_list'    => $poliList,
                'sesi'         => $sesiList,
                'antrian'      => $antrian,
                'statistik'    => [
                    'total'     => $antrian->count(),
                    'menunggu'  => $statusCount['menunggu'] ?? 0,
                    'dilayani'  => $statusCount['dilayani'] ?? 0,
                    'selesai'   => $statusCount['selesai'] ?? 0,
                    'batal'     => $statusCount['batal'] ?? 0,
                    'dipanggil' => $statusCount['dipanggil'] ?? 0,
                ],
            ],
        ]);
    }

    // PATCH /admin/antrian/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dipanggil,skrining,dalam_antrian_dokter,dilayani,selesai,dilewati,batal',
        ]);

        $antrian = Antrian::findOrFail($id);
        $now     = Carbon::now();

        $timestamps = match($request->status) {
            'dipanggil'             => ['waktu_dipanggil'        => $now],
            'skrining'              => ['waktu_mulai_skrining'   => $now],
            'dalam_antrian_dokter'  => ['waktu_selesai_skrining' => $now],
            'dilayani'              => ['waktu_mulai_dilayani'   => $now],
            'selesai'               => ['waktu_selesai'          => $now],
            default                 => [],
        };

        $antrian->update(array_merge(['status' => $request->status], $timestamps));

        return response()->json(['success' => true, 'message' => 'Status antrian diperbarui.', 'data' => $antrian]);
    }
}
