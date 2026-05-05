<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // GET /admin/laporan/harian?tanggal=2026-03-06
    public function harian(Request $request)
    {
        $tanggal = $request->tanggal
            ? Carbon::parse($request->tanggal)
            : Carbon::today();

        // Statistik utama
        $antrian = Antrian::whereDate('tanggal', $tanggal);

        $total    = (clone $antrian)->count();
        $selesai  = (clone $antrian)->where('status', 'selesai')->count();
        $batal    = (clone $antrian)->where('status', 'batal')->count();
        $menunggu = (clone $antrian)->whereIn('status', ['menunggu', 'dipanggil', 'skrining', 'dalam_antrian_dokter', 'dilayani'])->count();
        $bpjs     = (clone $antrian)->where('jenis_pasien', 'bpjs')->count();
        $umum     = (clone $antrian)->where('jenis_pasien', 'umum')->count();

        // Per poli
        $perPoli = (clone $antrian)
            ->join('poli', 'antrian.poli_id', '=', 'poli.id')
            ->select(
                'poli.nama as poli',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when status='selesai' then 1 else 0 end) as selesai"),
                DB::raw("sum(case when status='batal' then 1 else 0 end) as batal"),
                DB::raw("sum(case when jenis_pasien='bpjs' then 1 else 0 end) as bpjs"),
                DB::raw("sum(case when jenis_pasien='umum' then 1 else 0 end) as umum")
            )
            ->groupBy('poli.nama')
            ->orderByDesc('total')
            ->get();

        // Per dokter
        $perDokter = (clone $antrian)
            ->join('dokter', 'antrian.dokter_id', '=', 'dokter.id')
            ->select(
                'dokter.nama as dokter',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when status='selesai' then 1 else 0 end) as selesai")
            )
            ->groupBy('dokter.nama')
            ->orderByDesc('total')
            ->get();

        // Daftar antrian detail
        $detail = Antrian::whereDate('tanggal', $tanggal)
            ->with(['pasien', 'poli', 'dokter'])
            ->orderBy('nomor_antrian')
            ->get()
            ->map(fn($a) => [
                'nomor'         => $a->kode_antrian . '-' . str_pad($a->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'pasien'        => $a->pasien?->nama ?? '-',
                'no_rm'         => $a->pasien?->no_rm ?? '-',
                'poli'          => $a->poli?->nama ?? '-',
                'dokter'        => $a->dokter?->nama ?? '-',
                'jenis_pasien'  => $a->jenis_pasien,
                'status'        => $a->status,
                'waktu_daftar'  => $a->waktu_daftar?->format('H:i'),
                'waktu_selesai' => $a->waktu_selesai?->format('H:i'),
            ]);

        // Rata-rata waktu tunggu (menit)
        $avgTunggu = Antrian::whereDate('tanggal', $tanggal)
            ->where('status', 'selesai')
            ->whereNotNull('waktu_daftar')
            ->whereNotNull('waktu_selesai')
            ->select(DB::raw('avg(timestampdiff(minute, waktu_daftar, waktu_selesai)) as avg_menit'))
            ->value('avg_menit');

        return response()->json([
            'success' => true,
            'data'    => [
                'tanggal'       => $tanggal->format('Y-m-d'),
                'tanggal_fmt'   => $tanggal->locale('id')->isoFormat('dddd, D MMMM Y'),
                'statistik'     => compact('total', 'selesai', 'batal', 'menunggu', 'bpjs', 'umum'),
                'avg_tunggu'    => $avgTunggu ? round($avgTunggu) : null,
                'per_poli'      => $perPoli,
                'per_dokter'    => $perDokter,
                'detail'        => $detail,
            ],
        ]);
    }

    // GET /admin/laporan/bulanan?bulan=3&tahun=2026
    public function bulanan(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $awal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $akhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $antrian = Antrian::whereBetween('tanggal', [$awal, $akhir]);

        $total   = (clone $antrian)->count();
        $selesai = (clone $antrian)->where('status', 'selesai')->count();
        $batal   = (clone $antrian)->where('status', 'batal')->count();
        $bpjs    = (clone $antrian)->where('jenis_pasien', 'bpjs')->count();
        $umum    = (clone $antrian)->where('jenis_pasien', 'umum')->count();

        // Pasien baru bulan ini
        $pasienBaru = Pasien::whereBetween('created_at', [$awal, $akhir])->count();

        // Grafik harian dalam bulan ini
        $grafikHarian = Antrian::whereBetween('tanggal', [$awal, $akhir])
            ->select(
                DB::raw('DATE(tanggal) as tgl'),
                DB::raw('count(*) as total'),
                DB::raw("sum(case when status='selesai' then 1 else 0 end) as selesai"),
                DB::raw("sum(case when status='batal' then 1 else 0 end) as batal")
            )
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('tgl')
            ->get()
            ->map(fn($r) => [
                'tanggal' => $r->tgl,
                'label'   => Carbon::parse($r->tgl)->format('d'),
                'total'   => $r->total,
                'selesai' => $r->selesai,
                'batal'   => $r->batal,
            ]);

        // Per poli bulan ini
        $perPoli = (clone $antrian)
            ->join('poli', 'antrian.poli_id', '=', 'poli.id')
            ->select(
                'poli.nama as poli',
                DB::raw('count(*) as total'),
                DB::raw("sum(case when status='selesai' then 1 else 0 end) as selesai"),
                DB::raw("sum(case when jenis_pasien='bpjs' then 1 else 0 end) as bpjs"),
                DB::raw("sum(case when jenis_pasien='umum' then 1 else 0 end) as umum")
            )
            ->groupBy('poli.nama')
            ->orderByDesc('total')
            ->get();

        // Top 5 dokter
        $topDokter = (clone $antrian)
            ->join('dokter', 'antrian.dokter_id', '=', 'dokter.id')
            ->select('dokter.nama as dokter', DB::raw('count(*) as total'))
            ->groupBy('dokter.nama')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Hari tersibuk
        $hariTersibuk = Antrian::whereBetween('tanggal', [$awal, $akhir])
            ->select(DB::raw('DATE(tanggal) as tgl'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderByDesc('total')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'bulan'         => (int)$bulan,
                'tahun'         => (int)$tahun,
                'bulan_fmt'     => $awal->locale('id')->isoFormat('MMMM Y'),
                'statistik'     => compact('total', 'selesai', 'batal', 'bpjs', 'umum', 'pasienBaru'),
                'grafik_harian' => $grafikHarian,
                'per_poli'      => $perPoli,
                'top_dokter'    => $topDokter,
                'hari_tersibuk' => $hariTersibuk ? [
                    'tanggal' => $hariTersibuk->tgl,
                    'label'   => Carbon::parse($hariTersibuk->tgl)->locale('id')->isoFormat('dddd, D MMM'),
                    'total'   => $hariTersibuk->total,
                ] : null,
            ],
        ]);
    }
}
