<?php

namespace App\Http\Controllers\Api\Loket;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\SesiAntrian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    // GET /api/loket/dashboard
    public function dashboard()
    {
        $today = Carbon::today();
        $hariIni = $today->locale('id')->isoFormat('dddd');

        $totalAntrian = Antrian::whereDate('tanggal', $today)->count();
        $menunggu     = Antrian::whereDate('tanggal', $today)->where('status', 'menunggu')->count();
        $dilayani     = Antrian::whereDate('tanggal', $today)->where('status', 'selesai')->count();
        $sesiAktif    = SesiAntrian::whereDate('tanggal', $today)->whereIn('status', ['aktif', 'buka'])->count();

        $antrianPerPoli = Antrian::whereDate('tanggal', $today)
            ->join('poli', 'antrian.poli_id', '=', 'poli.id')
            ->select('poli.nama as poli', DB::raw('count(*) as total'))
            ->groupBy('poli.nama')
            ->get();

        return response()->json([
            'statistik' => [
                'total_antrian' => $totalAntrian,
                'menunggu'      => $menunggu,
                'dilayani'      => $dilayani,
                'sesi_aktif'    => $sesiAktif,
            ],
            'antrian_per_poli' => $antrianPerPoli,
            'hari_ini'         => $hariIni,
        ]);
    }

    // GET /api/loket/sesi - semua sesi hari ini
    public function getSesi()
    {
        $sesi = SesiAntrian::whereDate('tanggal', today())
            ->with(['poli', 'dokter'])
            ->orderBy('jam_buka')
            ->get()
            ->map(function ($s) {
                $terpakai = $s->antrian()->whereNotIn('status', ['batal'])->count();
                return [
                    'id'         => $s->id,
                    'poli'       => $s->poli?->nama,
                    'dokter'     => $s->dokter?->nama,
                    'jam_buka'   => substr($s->jam_buka,  0, 5),
                    'jam_tutup'  => substr($s->jam_tutup, 0, 5),
                    'kuota'      => $s->kuota,
                    'terpakai'   => $terpakai,
                    'sisa'       => max(0, $s->kuota - $terpakai),
                    'status'     => $s->status,
                    'dibuka_oleh'=> $s->dibuka_oleh,
                ];
            });

        return response()->json(['data' => $sesi]);
    }

    // POST /api/loket/sesi/buka - buka sesi dari jadwal
    public function bukaSesi(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokter,id',
            'poli_id'   => 'required|exists:poli,id',
            'jam_buka'  => 'required|date_format:H:i',
            'jam_tutup' => 'required|date_format:H:i',
            'kuota'     => 'required|integer|min:1',
        ]);

        // Cek sudah ada sesi hari ini
        $exists = SesiAntrian::where('dokter_id', $request->dokter_id)
            ->whereDate('tanggal', today())
            ->where('status', '!=', 'ditutup')
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Sesi untuk dokter ini hari ini sudah dibuka.'], 422);
        }

        $sesi = SesiAntrian::create([
            'poli_id'       => $request->poli_id,
            'dokter_id'     => $request->dokter_id,
            'tanggal'       => today(),
            'jam_buka'      => $request->jam_buka,
            'jam_tutup'     => $request->jam_tutup,
            'kuota'         => $request->kuota,
            'nomor_terakhir'=> 0,
            'status'        => 'aktif',
            'dibuka_oleh'   => Auth::user()->name,
        ]);

        return response()->json(['message' => 'Sesi antrian berhasil dibuka.', 'data' => $sesi], 201);
    }

    // POST /api/loket/sesi/buka-semua - buka semua sesi dari jadwal hari ini
    public function bukaSesiOtomatis()
    {
        $hariIni  = Carbon::today()->locale('id')->isoFormat('dddd');
        $jadwals  = JadwalDokter::where('hari', $hariIni)
            ->where('is_active', true)
            ->with(['dokter.poli'])
            ->get();

        $dibuka = 0;
        foreach ($jadwals as $jadwal) {
            $exists = SesiAntrian::where('dokter_id', $jadwal->dokter_id)
                ->whereDate('tanggal', today())
                ->exists();

            if (!$exists && $jadwal->dokter && $jadwal->dokter->poli) {
                SesiAntrian::create([
                    'poli_id'        => $jadwal->dokter->poli_id,
                    'dokter_id'      => $jadwal->dokter_id,
                    'tanggal'        => today(),
                    'jam_buka'       => $jadwal->jam_buka,
                    'jam_tutup'      => $jadwal->jam_tutup,
                    'kuota'          => $jadwal->kuota,
                    'nomor_terakhir' => 0,
                    'status'         => 'aktif',
                    'dibuka_oleh'    => Auth::user()->name,
                ]);
                $dibuka++;
            }
        }

        return response()->json(['message' => "$dibuka sesi berhasil dibuka otomatis."]);
    }

    // PATCH /api/loket/sesi/{id}/tutup
    public function tutupSesi($id)
    {
        $sesi = SesiAntrian::findOrFail($id);
        $sesi->update([
            'status'      => 'ditutup',
            'ditutup_oleh'=> Auth::user()->name,
        ]);
        return response()->json(['message' => 'Sesi antrian berhasil ditutup.']);
    }

    // GET /api/loket/antrian - antrian hari ini
    public function getAntrian(Request $request)
    {
        $query = Antrian::whereDate('tanggal', today())
            ->with(['pasien', 'poli', 'dokter'])
            ->orderBy('nomor_antrian');

        if ($request->poli_id)  $query->where('poli_id',  $request->poli_id);
        if ($request->status)   $query->where('status',   $request->status);
        if ($request->search) {
            $query->whereHas('pasien', fn($q) =>
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('no_rm', 'like', '%' . $request->search . '%')
            );
        }

        $antrian = $query->get()->map(fn($a) => [
            'id'            => $a->id,
            'nomor_display' => $a->kode_antrian . '-' . str_pad($a->nomor_antrian, 3, '0', STR_PAD_LEFT),
            'pasien'        => $a->pasien?->nama,
            'no_rm'         => $a->pasien?->no_rm,
            'poli'          => $a->poli?->nama,
            'dokter'        => $a->dokter?->nama,
            'status'        => $a->status,
            'jenis_pasien'  => $a->jenis_pasien,
            'waktu_daftar'  => $a->waktu_daftar?->format('H:i'),
        ]);

        return response()->json(['data' => $antrian]);
    }

    // POST /api/loket/antrian/daftar - daftarkan pasien manual
    public function daftarManual(Request $request)
    {
        $request->validate([
            'pasien_id'  => 'required|exists:pasien,id',
            'sesi_id'    => 'required|exists:sesi_antrian,id',
            'poli_id'    => 'required|exists:poli,id',
            'dokter_id'  => 'required|exists:dokter,id',
            'jenis_pasien'=> 'nullable|in:bpjs,umum',
            'catatan'    => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request) {
            $sesi   = SesiAntrian::with(['poli', 'dokter'])->findOrFail($request->sesi_id);
            $pasien = Pasien::findOrFail($request->pasien_id);

            if (!in_array($sesi->status, ['aktif', 'buka'])) {
                return response()->json(['message' => 'Sesi antrian sudah ditutup.'], 422);
            }

            $sudahDaftar = Antrian::where('pasien_id', $pasien->id)
                ->where('sesi_antrian_id', $sesi->id)
                ->whereNotIn('status', ['batal'])
                ->exists();

            if ($sudahDaftar) {
                return response()->json(['message' => 'Pasien sudah terdaftar di sesi ini.'], 422);
            }

            $terpakai = $sesi->antrian()->whereNotIn('status', ['batal'])->count();
            if ($terpakai >= $sesi->kuota) {
                return response()->json(['message' => 'Kuota antrian sudah penuh.'], 422);
            }

            $nomorUrut   = $sesi->nomor_terakhir + 1;
            $kodeAntrian = strtoupper($sesi->poli->singkatan ?? $sesi->poli->kode ?? 'A');
            $sesi->increment('nomor_terakhir');

            $antrian = Antrian::create([
                'sesi_antrian_id' => $sesi->id,
                'pasien_id'       => $pasien->id,
                'poli_id'         => $request->poli_id,
                'dokter_id'       => $request->dokter_id,
                'loket_user_id'   => Auth::id(),
                'kode_antrian'    => $kodeAntrian,
                'nomor_antrian'   => $nomorUrut,
                'tanggal'         => today(),
                'jenis_kunjungan' => 'baru',
                'jenis_pasien'    => $request->jenis_pasien ?? 'umum',
                'status'          => 'menunggu',
                'waktu_daftar'    => now(),
                'catatan'         => $request->catatan,
            ]);

            return response()->json([
                'message' => 'Pasien berhasil didaftarkan.',
                'antrian' => [
                    'id'            => $antrian->id,
                    'nomor_display' => $kodeAntrian . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT),
                    'pasien'        => $pasien->nama,
                    'poli'          => $sesi->poli->nama,
                    'dokter'        => $sesi->dokter->nama,
                    'status'        => 'menunggu',
                ],
            ], 201);
        });
    }

    // PATCH /api/loket/antrian/{id}/batalkan
    public function batalkan($id)
    {
        $antrian = Antrian::findOrFail($id);
        if (!in_array($antrian->status, ['menunggu'])) {
            return response()->json(['message' => 'Antrian tidak dapat dibatalkan.'], 422);
        }
        $antrian->update(['status' => 'batal']);
        $antrian->sesiAntrian?->decrement('nomor_terakhir');
        return response()->json(['message' => 'Antrian berhasil dibatalkan.']);
    }

    // GET /api/loket/pasien - cari pasien
    public function cariPasien(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $pasien = Pasien::where('nama',  'like', '%' . $request->q . '%')
            ->orWhere('no_rm',   'like', '%' . $request->q . '%')
            ->orWhere('nik',     'like', '%' . $request->q . '%')
            ->orWhere('no_hp',   'like', '%' . $request->q . '%')
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'no_rm' => $p->no_rm,
                'nama'  => $p->nama,
                'nik'   => $p->nik,
                'no_hp' => $p->no_hp,
                'jenis_pasien' => $p->jenis_pasien,
            ]);

        return response()->json(['data' => $pasien]);
    }
}