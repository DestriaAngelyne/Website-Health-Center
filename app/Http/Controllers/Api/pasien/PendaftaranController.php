<?php

namespace App\Http\Controllers\Api\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\SesiAntrian;
use App\Models\Antrian;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    // =========================================================================
    // PUBLIC — Master Data (tanpa login)
    // =========================================================================

    /**
     * GET /api/poli
     * Daftar semua poli aktif
     */
    public function getPoli(): JsonResponse
    {
        $poli = Poli::where('is_active', true)
            ->select('id', 'kode', 'nama', 'singkatan', 'keterangan')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $poli,
        ]);
    }

    /**
     * GET /api/dokter/{poli_id}
     * Dokter aktif berdasarkan poli, beserta jadwalnya
     */
    public function getDokterByPoli(int $poli_id): JsonResponse
    {
        $dokter = Dokter::where('poli_id', $poli_id)
            ->where('is_active', true)
            ->with(['jadwalDokter' => function ($q) {
                $q->where('is_active', true)
                  ->select('id', 'dokter_id', 'hari', 'jam_mulai', 'jam_selesai', 'kuota')
                  ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')");
            }])
            ->select('id', 'nama', 'spesialis', 'foto', 'no_hp', 'poli_id')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $dokter,
        ]);
    }

    /**
     * GET /api/jadwal/{dokter_id}
     * Semua jadwal aktif milik dokter
     */
    public function getJadwalByDokter(int $dokter_id): JsonResponse
    {
        $jadwal = JadwalDokter::where('dokter_id', $dokter_id)
            ->where('is_active', true)
            ->select('id', 'hari', 'jam_mulai', 'jam_selesai', 'kuota')
            ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $jadwal,
        ]);
    }

    /**
     * GET /api/tanggal-tersedia/{dokter_id}
     * 14 hari ke depan yang dokter punya jadwal + info kuota per hari
     */
    public function getTanggalTersedia(int $dokter_id): JsonResponse
    {
        $hariAktif = JadwalDokter::where('dokter_id', $dokter_id)
            ->where('is_active', true)
            ->pluck('hari')
            ->toArray();

        if (empty($hariAktif)) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $result = [];
        for ($i = 0; $i < 14; $i++) {
            $date     = Carbon::today()->addDays($i);
            $namaHari = $this->hariIndo($date->dayOfWeek);

            if (!in_array($namaHari, $hariAktif)) continue;

            // Cek kuota untuk tanggal ini
            $sesi = SesiAntrian::where('dokter_id', $dokter_id)
                ->where('tanggal', $date->toDateString())
                ->first();

            $jadwal   = JadwalDokter::where('dokter_id', $dokter_id)
                ->where('hari', $namaHari)
                ->where('is_active', true)
                ->first();

            $kuota    = $sesi ? $sesi->kuota : ($jadwal?->kuota ?? 20);
            $terpakai = $sesi
                ? Antrian::where('sesi_antrian_id', $sesi->id)->whereNotIn('status', ['batal'])->count()
                : 0;

            // Skip tanggal yang sudah selesai/ditutup
            if ($sesi && in_array($sesi->status, ['selesai', 'ditutup']) && $terpakai >= $kuota) continue;

            $result[] = [
                'tanggal'  => $date->toDateString(),
                'hari'     => ucfirst($namaHari),
                'label'    => $i === 0 ? 'Hari Ini' : ($i === 1 ? 'Besok' : $date->translatedFormat('D, d M Y')),
                'sisa'     => max(0, $kuota - $terpakai),
                'tersedia' => ($kuota - $terpakai) > 0,
            ];
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    /**
     * GET /api/jadwal/{dokter_id}?tanggal=2026-02-27
     * Sesi + kuota realtime untuk dokter di tanggal tertentu
     * Dipakai saat pasien sudah pilih tanggal di form
     */
    public function getKuotaHarian(Request $request, int $dokter_id): JsonResponse
    {
        $request->validate(['tanggal' => 'required|date|after_or_equal:today']);

        $tanggal  = Carbon::parse($request->tanggal);
        $namaHari = $this->hariIndo($tanggal->dayOfWeek);

        $jadwals = JadwalDokter::where('dokter_id', $dokter_id)
            ->where('hari', $namaHari)
            ->where('is_active', true)
            ->get();

        if ($jadwals->isEmpty()) {
            return response()->json([
                'success' => true,
                'data'    => [],
                'message' => 'Dokter tidak memiliki jadwal pada hari tersebut.',
            ]);
        }

        $result = $jadwals->map(function ($jadwal) use ($dokter_id, $tanggal) {
            $sesi = SesiAntrian::where('poli_id', $jadwal->poli_id)
                ->where('dokter_id', $dokter_id)
                ->where('tanggal', $tanggal->toDateString())
                ->first();

            $kuota    = $sesi ? $sesi->kuota : $jadwal->kuota;
            $terpakai = $sesi
                ? Antrian::where('sesi_antrian_id', $sesi->id)->whereNotIn('status', ['batal'])->count()
                : 0;
            $sisa     = max(0, $kuota - $terpakai);

            return [
                'jadwal_id'   => $jadwal->id,
                'poli_id'     => $jadwal->poli_id,
                'hari'        => $jadwal->hari,
                'jam_mulai'   => $jadwal->jam_mulai,
                'jam_selesai' => $jadwal->jam_selesai,
                'kuota'       => $kuota,
                'terpakai'    => $terpakai,
                'sisa'        => $sisa,
                'status_sesi' => $sesi?->status ?? 'belum_dibuka',
                'bisa_daftar' => $sisa > 0 && !in_array($sesi?->status, ['selesai', 'ditutup']),
                'sesi_id'     => $sesi?->id,
            ];
        });

        return response()->json(['success' => true, 'data' => $result]);
    }

    // =========================================================================
    // PROTECTED — Pasien harus login (role:pasien)
    // =========================================================================

    /**
     * POST /api/pasien/pendaftaran
     * Buat nomor antrean baru (atomic, race-condition safe)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'poli_id'         => 'required|exists:poli,id',
            'dokter_id'       => 'required|exists:dokter,id',
            'jadwal_id'       => 'required|exists:jadwal_dokter,id',
            'tanggal'         => 'required|date|after_or_equal:today',
            'jenis_pasien'    => 'required|in:umum,bpjs',
            'jenis_kunjungan' => 'required|in:baru,lama',
        ]);

        // Pastikan user yang login punya data pasien
        $pasien = Pasien::where('user_id', Auth::id())->first();

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Data pasien tidak ditemukan. Harap lengkapi profil pasien terlebih dahulu.',
            ], 422);
        }

        // Cegah daftar dobel di poli yang sama pada hari yang sama
        $sudahDaftar = Antrian::where('pasien_id', $pasien->id)
            ->where('poli_id', $request->poli_id)
            ->where('tanggal', $request->tanggal)
            ->whereNotIn('status', ['batal'])
            ->exists();

        if ($sudahDaftar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah terdaftar di poli ini pada tanggal tersebut.',
            ], 409);
        }

        try {
            $antrian = DB::transaction(function () use ($request, $pasien) {

                $poli   = Poli::findOrFail($request->poli_id);
                $jadwal = JadwalDokter::findOrFail($request->jadwal_id);

                // Lock sesi_antrian untuk hindari race condition
                $sesi = SesiAntrian::lockForUpdate()
                    ->where('poli_id', $request->poli_id)
                    ->where('dokter_id', $request->dokter_id)
                    ->where('tanggal', $request->tanggal)
                    ->first();

                // Buat sesi otomatis jika belum ada (pendaftaran online pertama)
                if (!$sesi) {
                    $sesi = SesiAntrian::create([
                        'poli_id'   => $request->poli_id,
                        'dokter_id' => $request->dokter_id,
                        'tanggal'   => $request->tanggal,
                        'jam_buka'  => $jadwal->jam_mulai,
                        'jam_tutup' => $jadwal->jam_selesai,
                        'kuota'     => $jadwal->kuota,
                        'status'    => 'menunggu',
                    ]);
                }

                // Validasi sesi tidak ditutup
                if (in_array($sesi->status, ['selesai', 'ditutup'])) {
                    throw ValidationException::withMessages([
                        'sesi' => 'Pendaftaran untuk jadwal ini sudah ditutup.',
                    ]);
                }

                // Hitung terpakai (dalam transaksi = atomic)
                $terpakai = Antrian::where('sesi_antrian_id', $sesi->id)
                    ->whereNotIn('status', ['batal'])
                    ->count();

                if ($terpakai >= $sesi->kuota) {
                    throw ValidationException::withMessages([
                        'kuota' => 'Maaf, kuota pendaftaran sudah penuh. Silakan pilih jadwal lain.',
                    ]);
                }

                // Generate kode antrean: huruf pertama kode poli + 3 digit urutan
                // Contoh: Poli "UMUM" (kode=UM) → U001, U002, ...
                $nomorUrut   = $terpakai + 1;
                $kodeAntrian = strtoupper($poli->kode[0]) . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

                $antrian = Antrian::create([
                    'sesi_antrian_id' => $sesi->id,
                    'pasien_id'       => $pasien->id,
                    'poli_id'         => $request->poli_id,
                    'dokter_id'       => $request->dokter_id,
                    'kode_antrian'    => $kodeAntrian,
                    'nomor_antrian'   => $nomorUrut,
                    'tanggal'         => $request->tanggal,
                    'jenis_kunjungan' => $request->jenis_kunjungan,
                    'jenis_pasien'    => $request->jenis_pasien,
                    'status'          => 'menunggu',
                    'waktu_daftar'    => now(),
                ]);

                // Update counter nomor_terakhir di sesi
                $sesi->increment('nomor_terakhir');

                return $antrian->load(['poli', 'dokter', 'pasien', 'sesiAntrian']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil!',
                'data'    => [
                    'id'                       => $antrian->id,
                    'kode_antrian'             => $antrian->kode_antrian,
                    'nomor_antrian'            => $antrian->nomor_antrian,
                    'tanggal'                  => $antrian->tanggal->format('d M Y'),
                    'poli'                     => $antrian->poli->nama,
                    'dokter'                   => $antrian->dokter->nama,
                    'pasien'                   => $antrian->pasien->nama,
                    'no_rm'                    => $antrian->pasien->no_rm,
                    'jam_mulai'                => $antrian->sesiAntrian->jam_buka,
                    'jam_selesai'              => $antrian->sesiAntrian->jam_tutup,
                    'jenis_pasien'             => $antrian->jenis_pasien,
                    'jenis_kunjungan'          => $antrian->jenis_kunjungan,
                    'status'                   => $antrian->status,
                    'waktu_daftar'             => $antrian->waktu_daftar->format('d M Y, H:i'),
                    'estimasi_antrian_sebelum' => $antrian->nomor_antrian - 1,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    /**
     * GET /api/pasien/antrian/riwayat
     * Riwayat antrean pasien yang sedang login
     */
    public function riwayatAntrian(): JsonResponse
    {
        $pasien = Pasien::where('user_id', Auth::id())->first();

        if (!$pasien) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $data = Antrian::where('pasien_id', $pasien->id)
            ->with([
                'poli:id,nama,kode',
                'dokter:id,nama,spesialis',
                'sesiAntrian:id,jam_buka,jam_tutup',
            ])
            ->select(
                'id', 'kode_antrian', 'nomor_antrian', 'tanggal',
                'poli_id', 'dokter_id', 'sesi_antrian_id',
                'status', 'jenis_pasien', 'jenis_kunjungan', 'waktu_daftar'
            )
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/pasien/antrian/{id}
     * Detail satu antrean milik pasien yang login
     */
    public function detailAntrian(int $id): JsonResponse
    {
        $pasien = Pasien::where('user_id', Auth::id())->first();

        if (!$pasien) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan.'], 404);
        }

        $antrian = Antrian::where('id', $id)
            ->where('pasien_id', $pasien->id) // pastikan milik pasien ini
            ->with(['poli', 'dokter', 'sesiAntrian'])
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Data antrean tidak ditemukan.'], 404);
        }

        // Hitung posisi antrean saat ini
        $posisiSekarang = Antrian::where('sesi_antrian_id', $antrian->sesi_antrian_id)
            ->whereIn('status', ['dipanggil', 'skrining', 'dalam_antrian_dokter', 'dilayani'])
            ->max('nomor_antrian') ?? 0;

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                => $antrian->id,
                'kode_antrian'      => $antrian->kode_antrian,
                'nomor_antrian'     => $antrian->nomor_antrian,
                'tanggal'           => $antrian->tanggal->format('d M Y'),
                'poli'              => $antrian->poli->nama,
                'dokter'            => $antrian->dokter->nama,
                'jam_praktik'       => $antrian->sesiAntrian->jam_buka . ' – ' . $antrian->sesiAntrian->jam_tutup,
                'status'            => $antrian->status,
                'jenis_pasien'      => $antrian->jenis_pasien,
                'jenis_kunjungan'   => $antrian->jenis_kunjungan,
                'waktu_daftar'      => $antrian->waktu_daftar?->format('d M Y, H:i'),
                'sedang_dipanggil'  => $posisiSekarang,
                'sisa_antrian'      => max(0, $antrian->nomor_antrian - $posisiSekarang - 1),
            ],
        ]);
    }

    // =========================================================================
    // Helper
    // =========================================================================

    private function hariIndo(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        };
    }
}
