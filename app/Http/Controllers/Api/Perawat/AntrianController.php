<?php

namespace App\Http\Controllers\Api\Perawat;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\SesiAntrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AntrianController extends Controller
{
    /**
     * GET /api/perawat/antrian
     */
    public function index()
    {
        $today = Carbon::today()->format('Y-m-d');

        $sesi = SesiAntrian::with(['poli', 'dokter'])
            ->whereDate('tanggal', $today)
            ->whereIn('status', ['aktif', 'buka'])
            ->first();

        $antrian = Antrian::with(['pasien', 'poli', 'dokter'])
            ->whereDate('tanggal', $today)
            ->whereNotIn('status', ['batal'])
            ->orderBy('nomor_antrian', 'asc')
            ->get()
            ->map(fn($a) => [
                'id'            => $a->id,
                'kode_antrian'  => $a->kode_antrian,
                'nomor_antrian' => $a->nomor_antrian,
                'nomor_display' => ($a->kode_antrian ?? 'A') . '-' . str_pad($a->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'status'        => $a->status,
                'jenis_pasien'  => $a->jenis_pasien,
                'waktu_daftar'  => $a->waktu_daftar,
                'catatan'       => $a->catatan,
                'pasien'        => $a->pasien ? [
                    'id'    => $a->pasien->id,
                    'nama'  => $a->pasien->nama,
                    'no_rm' => $a->pasien->no_rm,
                ] : null,
                'poli'          => $a->poli?->nama ?? '-',
            ]);

        return response()->json([
            'status' => 'success',
            'data'   => $antrian,
            'poli'   => $sesi?->poli?->nama ?? '-',
            'sesi'   => $sesi
                ? substr($sesi->jam_buka, 0, 5) . ' - ' . substr($sesi->jam_tutup, 0, 5)
                : 'Tidak ada sesi aktif',
        ]);
    }

    /**
     * GET /api/perawat/antrian/panggil/{id?}
     * Dipanggil oleh Vue dengan path /perawat/antrian/panggil/{id}
     */
    public function panggilDetail($id = null)
    {
        if ($id && $id !== '') {
            $antrian = Antrian::with(['pasien', 'poli', 'dokter', 'sesiAntrian'])->find($id);
        } else {
            $antrian = Antrian::with(['pasien', 'poli', 'dokter', 'sesiAntrian'])
                ->whereDate('tanggal', Carbon::today())
                ->where('status', 'menunggu')
                ->orderBy('nomor_antrian', 'asc')
                ->first();
        }

        if (!$antrian || !$antrian->pasien) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada data antrian yang tersedia saat ini.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'            => $antrian->id,
                'kode_antrian'  => $antrian->kode_antrian,
                'nomor_antrian' => $antrian->nomor_antrian,
                'nomor_display' => ($antrian->kode_antrian ?? 'A') . '-' . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'status'        => $antrian->status,
                'jenis_pasien'  => $antrian->jenis_pasien,
                'catatan'       => $antrian->catatan,
                'poli'          => $antrian->poli?->nama ?? '-',
                'dokter'        => $antrian->dokter?->nama ?? '-',
                'jam_buka'      => $antrian->sesiAntrian ? substr($antrian->sesiAntrian->jam_buka, 0, 5) : '-',
                'jam_tutup'     => $antrian->sesiAntrian ? substr($antrian->sesiAntrian->jam_tutup, 0, 5) : '-',
                'pasien'        => [
                    'id'             => $antrian->pasien->id,
                    'nama'           => $antrian->pasien->nama,
                    'no_rm'          => $antrian->pasien->no_rm,
                    'nik'            => $antrian->pasien->nik,
                    'tanggal_lahir'  => $antrian->pasien->tanggal_lahir,
                    'jenis_kelamin'  => $antrian->pasien->jenis_kelamin,
                    'no_hp'          => $antrian->pasien->no_hp,
                    'alamat'         => $antrian->pasien->alamat,
                    'golongan_darah' => $antrian->pasien->golongan_darah,
                ],
            ],
        ]);
    }

    /**
     * PATCH /api/perawat/antrian/{id}/panggil
     */
    public function panggil($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'dipanggil', 'waktu_dipanggil' => now()]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pasien berhasil dipanggil.',
            'data'    => $antrian,
        ]);
    }

    /**
     * POST /api/perawat/antrian/mulai/{id}
     * Dipanggil Vue saat klik "Mulai Periksa"
     */
    public function mulai($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update([
            'status'               => 'skrining',
            'waktu_mulai_skrining' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pasien siap dilakukan skrining.',
            'data'    => $antrian,
        ]);
    }

    /**
     * PATCH /api/perawat/antrian/{id}/selesai
     */
    public function selesai($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update([
            'status'                 => 'selesai',
            'waktu_selesai_skrining' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Antrian selesai.',
            'data'    => $antrian,
        ]);
    }

    /**
     * POST /api/perawat/antrian/tutup-sesi
     */
    public function tutupSesi()
    {
        SesiAntrian::whereDate('tanggal', Carbon::today())
            ->whereIn('status', ['aktif', 'buka'])
            ->update(['status' => 'tutup']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sesi berhasil ditutup.',
        ]);
    }
}
