<?php

namespace App\Http\Controllers\Api\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Kunjungan;
use App\Models\SesiAntrian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    // GET /api/dokter/dashboard
    public function dashboard()
    {
        $user   = Auth::user();
        $dokter = \App\Models\Dokter::where('user_id', $user->id)->first();

        if (!$dokter) {
            return response()->json(['message' => 'Data dokter tidak ditemukan.'], 404);
        }

        $today = Carbon::today();

        $totalPasien  = Antrian::whereDate('tanggal', $today)->where('dokter_id', $dokter->id)->count();
        $menunggu     = Antrian::whereDate('tanggal', $today)->where('dokter_id', $dokter->id)->where('status', 'menunggu')->count();
        $dilayani     = Antrian::whereDate('tanggal', $today)->where('dokter_id', $dokter->id)->where('status', 'dilayani')->count();
        $selesai      = Antrian::whereDate('tanggal', $today)->where('dokter_id', $dokter->id)->where('status', 'selesai')->count();

        $sesi = SesiAntrian::whereDate('tanggal', $today)
            ->where('dokter_id', $dokter->id)
            ->with('poli')
            ->first();

        $pasienAktif = Antrian::whereDate('tanggal', $today)
            ->where('dokter_id', $dokter->id)
            ->whereIn('status', ['dipanggil', 'dilayani'])
            ->with('pasien')
            ->orderBy('nomor_antrian')
            ->first();

        return response()->json([
            'dokter' => [
                'id'           => $dokter->id,
                'nama'         => $dokter->nama,
                'spesialisasi' => $dokter->spesialisasi,
                'poli'         => $dokter->poli?->nama,
            ],
            'statistik' => [
                'total_pasien' => $totalPasien,
                'menunggu'     => $menunggu,
                'dilayani'     => $dilayani,
                'selesai'      => $selesai,
            ],
            'sesi'        => $sesi ? [
                'jam_buka'  => substr($sesi->jam_buka, 0, 5),
                'jam_tutup' => substr($sesi->jam_tutup, 0, 5),
                'kuota'     => $sesi->kuota,
                'status'    => $sesi->status,
                'poli'      => $sesi->poli?->nama,
            ] : null,
            'pasien_aktif' => $pasienAktif ? [
                'id'            => $pasienAktif->id,
                'nomor_display' => $pasienAktif->kode_antrian . '-' . str_pad($pasienAktif->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'pasien'        => $pasienAktif->pasien?->nama,
                'status'        => $pasienAktif->status,
            ] : null,
        ]);
    }

    // GET /api/dokter/antrian
    public function index(Request $request)
    {
        $user   = Auth::user();
        $dokter = \App\Models\Dokter::where('user_id', $user->id)->first();

        if (!$dokter) {
            return response()->json(['message' => 'Data dokter tidak ditemukan.'], 404);
        }

        $query = Antrian::whereDate('tanggal', today())
            ->where('dokter_id', $dokter->id)
            ->with(['pasien', 'poli', 'kunjungan'])
            ->orderBy('nomor_antrian');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $antrian = $query->get()->map(fn($a) => [
            'id'              => $a->id,
            'nomor_display'   => $a->kode_antrian . '-' . str_pad($a->nomor_antrian, 3, '0', STR_PAD_LEFT),
            'pasien'          => $a->pasien?->nama,
            'pasien_id'       => $a->pasien_id,
            'no_rm'           => $a->pasien?->no_rm,
            'umur'            => $a->pasien?->tanggal_lahir
                ? Carbon::parse($a->pasien->tanggal_lahir)->age . ' th'
                : '-',
            'jenis_kelamin'   => $a->pasien?->jenis_kelamin,
            'jenis_pasien'    => $a->jenis_pasien,
            'jenis_kunjungan' => $a->jenis_kunjungan,
            'status'          => $a->status,
            'waktu_daftar'    => $a->waktu_daftar?->format('H:i'),
            'waktu_dipanggil' => $a->waktu_dipanggil?->format('H:i'),
            'has_kunjungan'   => $a->kunjungan !== null,
            'kunjungan_id'    => $a->kunjungan?->id,
            'catatan'         => $a->catatan,
        ]);

        return response()->json(['data' => $antrian, 'dokter_id' => $dokter->id]);
    }

    // PATCH /api/dokter/antrian/{id}/panggil
    public function panggil($id)
    {
        $antrian = Antrian::findOrFail($id);

        if (!in_array($antrian->status, ['menunggu', 'skrining_selesai', 'dalam_antrian_dokter'])) {
            return response()->json(['message' => 'Status antrian tidak valid untuk dipanggil.'], 422);
        }

        $antrian->update([
            'status'          => 'dipanggil',
            'waktu_dipanggil' => now(),
        ]);

        return response()->json(['message' => 'Pasien berhasil dipanggil.']);
    }

    // PATCH /api/dokter/antrian/{id}/mulai
    public function mulai($id)
    {
        $antrian = Antrian::with('pasien', 'poli')->findOrFail($id);

        if ($antrian->status !== 'dipanggil') {
            return response()->json(['message' => 'Pasien belum dipanggil.'], 422);
        }

        $user   = Auth::user();
        $dokter = \App\Models\Dokter::where('user_id', $user->id)->first();

        return DB::transaction(function () use ($antrian, $dokter) {
            $antrian->update([
                'status'              => 'dilayani',
                'waktu_mulai_dilayani'=> now(),
            ]);

            // Buat record kunjungan jika belum ada
            $kunjungan = Kunjungan::firstOrCreate(
                ['antrian_id' => $antrian->id],
                [
                    'pasien_id'         => $antrian->pasien_id,
                    'dokter_id'         => $antrian->dokter_id,
                    'poli_id'           => $antrian->poli_id,
                    'tanggal_kunjungan' => today(),
                    'no_kunjungan'      => 'KJN-' . date('Ymd') . '-' . str_pad($antrian->id, 4, '0', STR_PAD_LEFT),
                    'status'            => 'dalam_proses',
                ]
            );

            return response()->json([
                'message'      => 'Sesi layanan dimulai.',
                'kunjungan_id' => $kunjungan->id,
            ]);
        });
    }

    // PATCH /api/dokter/antrian/{id}/lewati
    public function lewati($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'dilewati']);
        return response()->json(['message' => 'Antrian dilewati.']);
    }
}