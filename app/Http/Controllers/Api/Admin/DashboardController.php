<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\SesiAntrian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ── Statistik hari ini ────────────────────────────────────────────
        $totalAntrian = Antrian::whereDate('tanggal', $today)->count();
        $dilayani     = Antrian::whereDate('tanggal', $today)->where('status', 'selesai')->count();
        $menunggu     = Antrian::whereDate('tanggal', $today)->where('status', 'menunggu')->count();
        $dibatal      = Antrian::whereDate('tanggal', $today)->where('status', 'batal')->count();
        $sesiAktif    = SesiAntrian::whereDate('tanggal', $today)
                            ->whereIn('status', ['aktif', 'buka'])->count();

        $totalPasien  = Pasien::count();
        $pasienBaru   = Pasien::whereDate('created_at', $today)->count();
        $totalDokter  = Dokter::where('is_active', true)->count();
        $totalPoli    = Poli::where('is_active', true)->count();

        // ── Antrian per poli hari ini ─────────────────────────────────────
        $antrianPerPoli = Antrian::whereDate('antrian.tanggal', $today)
            ->join('poli', 'antrian.poli_id', '=', 'poli.id')
            ->select('poli.nama as poli', DB::raw('count(*) as total'))
            ->groupBy('poli.nama')
            ->orderByDesc('total')
            ->get();

        // ── Grafik 7 hari terakhir ────────────────────────────────────────
        $grafikMingguan = Antrian::whereBetween('tanggal', [
                Carbon::today()->subDays(6),
                Carbon::today(),
            ])
            ->select(DB::raw('DATE(tanggal) as tanggal'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('tanggal')
            ->get()
            ->map(fn($r) => [
                'tanggal' => Carbon::parse($r->tanggal)->locale('id')->isoFormat('ddd D MMM'),
                'total'   => $r->total,
            ]);

        // ── Antrian aktif hari ini (10 terbaru) ───────────────────────────
        $antrianAktif = Antrian::whereDate('tanggal', $today)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->with(['pasien:id,nama', 'poli:id,nama', 'dokter:id,nama'])
            ->orderBy('nomor_antrian')
            ->take(10)
            ->get()
            ->map(fn($a) => [
                'id'            => $a->id,
                'nomor_display' => ($a->kode_antrian ?? '')
                                 . '-'
                                 . str_pad($a->nomor_antrian ?? 0, 3, '0', STR_PAD_LEFT),
                'pasien'        => $a->pasien?->nama ?? '-',
                'poli'          => $a->poli?->nama ?? '-',
                'dokter'        => $a->dokter?->nama ?? '-',
                'status'        => $a->status,
                'waktu_daftar'  => $a->waktu_daftar
                                    ? Carbon::parse($a->waktu_daftar)->format('H:i')
                                    : '-',
            ]);

        return response()->json([
            'statistik' => [
                'total_antrian' => $totalAntrian,
                'dilayani'      => $dilayani,
                'menunggu'      => $menunggu,
                'dibatal'       => $dibatal,
                'sesi_aktif'    => $sesiAktif,
                'total_pasien'  => $totalPasien,
                'pasien_baru'   => $pasienBaru,
                'total_dokter'  => $totalDokter,
                'total_poli'    => $totalPoli,
            ],
            'antrian_per_poli' => $antrianPerPoli,
            'grafik_mingguan'  => $grafikMingguan,
            'antrian_aktif'    => $antrianAktif,
        ]);
    }
}
