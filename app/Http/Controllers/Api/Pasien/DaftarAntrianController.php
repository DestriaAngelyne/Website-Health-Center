<?php

namespace App\Http\Controllers\Api\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Poli;
use App\Models\SesiAntrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaftarAntrianController extends Controller
{
    // GET /api/pasien/pendaftaran/poli
    public function getPoli()
    {
        $poli = Poli::where('is_active', true)
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'nama'       => $p->nama,
                'kode'       => $p->kode,
                'singkatan'  => $p->singkatan,
                'keterangan' => $p->keterangan ?? null,
            ]);

        return response()->json(['data' => $poli]);
    }

    // GET /api/pasien/pendaftaran/dokter/{poliId}
    public function getDokterByPoli($poliId)
    {
        $dokter = Dokter::where('poli_id', $poliId)
            ->where('is_active', true)
            ->get()
            ->map(fn($d) => [
                'id'        => $d->id,
                'nama'      => $d->nama,
                'spesialis' => $d->spesialis ?? null,
                'foto'      => $d->foto ?? null,
            ]);

        return response()->json(['data' => $dokter]);
    }

    // GET /api/pasien/pendaftaran/sesi?dokter_id=&poli_id=
    public function getSesi(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokter,id',
        ]);

        $mulai   = Carbon::today();
        $selesai = Carbon::today()->addDays(6);

        $query = SesiAntrian::where('dokter_id', $request->dokter_id)
            ->whereBetween('tanggal', [$mulai, $selesai])
            ->whereIn('status', ['aktif', 'buka'])
            ->orderBy('tanggal')
            ->orderBy('jam_buka');

        if ($request->poli_id) {
            $query->where('poli_id', $request->poli_id);
        }

        $sesi = $query->get()->map(function ($s) {
            $terpakai = $s->antrian()->whereNotIn('status', ['batal'])->count();
            $sisa     = max(0, $s->kuota - $terpakai);

            return [
                'id'          => $s->id,
                'tanggal'     => $s->tanggal->format('Y-m-d'),
                'tanggal_fmt' => $s->tanggal->locale('id')->isoFormat('dddd, D MMMM Y'),
                'hari'        => $s->tanggal->locale('id')->isoFormat('dddd'),
                'jam_buka'    => substr($s->jam_buka, 0, 5),
                'jam_tutup'   => substr($s->jam_tutup, 0, 5),
                'kuota'       => $s->kuota,
                'terpakai'    => $terpakai,
                'sisa'        => $sisa,
                'is_full'     => $sisa <= 0,
            ];
        });

        return response()->json(['data' => $sesi]);
    }

    // POST /api/pasien/pendaftaran/daftar
    public function daftar(Request $request)
    {
        $request->validate([
            'poli_id'   => 'required|exists:poli,id',
            'dokter_id' => 'required|exists:dokter,id',
            'sesi_id'   => 'nullable|exists:sesi_antrian,id',
            'tanggal'   => 'nullable|date',
            'jaminan'   => 'nullable|string|in:BPJS,Umum',
            'no_bpjs'   => 'nullable|string',
            'keluhan'   => 'nullable|string|max:500',
        ]);

        $user   = Auth::user();
        $pasien = $user->pasien;

        if (!$pasien) {
            return response()->json([
                'message' => 'Data pasien tidak ditemukan. Hubungi administrator.',
            ], 422);
        }

        return DB::transaction(function () use ($request, $pasien) {

            if ($request->filled('sesi_id')) {
                $sesi = SesiAntrian::with(['poli', 'dokter'])->findOrFail($request->sesi_id);
            } else {
                if (!$request->filled('tanggal')) {
                    return response()->json([
                        'message' => 'Tanggal wajib diisi jika sesi tidak dipilih.',
                    ], 422);
                }

                $tanggal = Carbon::parse($request->tanggal)->toDateString();

                $sesi = SesiAntrian::with(['poli', 'dokter'])
                    ->where('dokter_id', $request->dokter_id)
                    ->where('poli_id',   $request->poli_id)
                    ->where('tanggal',   $tanggal)
                    ->whereIn('status',  ['aktif', 'buka'])
                    ->first();

                if (!$sesi) {
                    $sesi = SesiAntrian::create([
                        'dokter_id'      => $request->dokter_id,
                        'poli_id'        => $request->poli_id,
                        'tanggal'        => $tanggal,
                        'jam_buka'       => '08:00:00',
                        'jam_tutup'      => '14:00:00',
                        'kuota'          => 20,
                        'nomor_terakhir' => 0,
                        'status'         => 'aktif',
                    ]);
                    $sesi->load(['poli', 'dokter']);
                }
            }

            if (!in_array($sesi->status, ['aktif', 'buka'])) {
                return response()->json([
                    'message' => 'Sesi antrian sudah ditutup.',
                ], 422);
            }

            $sudahDaftar = Antrian::where('pasien_id',       $pasien->id)
                ->where('sesi_antrian_id', $sesi->id)
                ->whereNotIn('status', ['batal'])
                ->exists();

            if ($sudahDaftar) {
                return response()->json([
                    'message' => 'Anda sudah terdaftar di sesi antrian ini.',
                ], 422);
            }

            $terpakai = $sesi->antrian()->whereNotIn('status', ['batal'])->count();
            if ($terpakai >= $sesi->kuota) {
                return response()->json([
                    'message' => 'Maaf, kuota antrian sudah penuh.',
                ], 422);
            }

            $nomorUrut   = $sesi->nomor_terakhir + 1;
            $kodeAntrian = strtoupper($sesi->poli->singkatan ?? $sesi->poli->kode ?? 'A');

            $sesi->increment('nomor_terakhir');

            $antrian = Antrian::create([
                'sesi_antrian_id' => $sesi->id,
                'pasien_id'       => $pasien->id,
                'poli_id'         => $request->poli_id,
                'dokter_id'       => $request->dokter_id,
                'kode_antrian'    => $kodeAntrian,
                'nomor_antrian'   => $nomorUrut,
                'tanggal'         => $sesi->tanggal,
                'jenis_kunjungan' => 'baru',
                'jenis_pasien'    => $pasien->jenis_pasien ?? 'umum',
                'status'          => 'menunggu',
                'waktu_daftar'    => now(),
                'catatan'         => $request->keluhan,
            ]);

            return response()->json([
                'message' => 'Pendaftaran antrian berhasil!',
                'antrian' => [
                    'id'            => $antrian->id,
                    'kode_antrian'  => $kodeAntrian,
                    'nomor_antrian' => $nomorUrut,
                    'nomor_display' => $kodeAntrian . '-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT),
                    'status'        => $antrian->status,
                    'poli'          => $sesi->poli->nama,
                    'dokter'        => $sesi->dokter->nama,
                    'tanggal'       => $sesi->tanggal->format('Y-m-d'),
                    'tanggal_fmt'   => $sesi->tanggal->locale('id')->isoFormat('dddd, D MMMM Y'),
                    'jam_buka'      => substr($sesi->jam_buka, 0, 5),
                    'jam_tutup'     => substr($sesi->jam_tutup, 0, 5),
                    'pasien'        => $pasien->nama,
                    'no_rm'         => $pasien->no_rm ?? null,
                ],
            ], 201);
        });
    }

    // GET /api/pasien/pendaftaran/cek-antrian-aktif
    public function cekAntrianAktif()
    {
        $pasien  = Auth::user()->pasien;
        $antrian = Antrian::where('pasien_id', $pasien?->id)
            ->whereDate('tanggal', today())
            ->whereNotIn('status', ['batal', 'selesai'])
            ->with(['poli', 'dokter', 'sesiAntrian'])
            ->first();

        return response()->json([
            'has_antrian' => !is_null($antrian),
            'antrian'     => $antrian ? [
                'id'            => $antrian->id,
                'nomor_display' => $antrian->kode_antrian . '-' . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'poli'          => $antrian->poli->nama,
                'dokter'        => $antrian->dokter->nama,
                'status'        => $antrian->status,
                'jam_buka'      => $antrian->sesiAntrian?->jam_buka,
            ] : null,
        ]);
    }

    // PATCH /api/pasien/pendaftaran/{antrianId}/batalkan
    public function batalkan($antrianId)
    {
        $pasien  = Auth::user()->pasien;
        $antrian = Antrian::where('id',        $antrianId)
            ->where('pasien_id', $pasien?->id)
            ->firstOrFail();

        if ($antrian->status !== 'menunggu') {
            return response()->json([
                'message' => 'Antrian tidak dapat dibatalkan karena sudah diproses.',
            ], 422);
        }

        $antrian->update(['status' => 'batal']);
        $antrian->sesiAntrian?->decrement('nomor_terakhir');

        return response()->json(['message' => 'Antrian berhasil dibatalkan.']);
    }
}
