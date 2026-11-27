<?php

namespace App\Http\Controllers\Api\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['tanggal' => 'nullable|date']);

        $dokter = Dokter::with('poli')->where('user_id', $request->user()->id)->first();

        if (! $dokter) {
            return response()->json([
                'message' => 'Akun ini belum terhubung dengan data dokter. Hubungi administrator.',
            ], 404);
        }

        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)->toDateString()
            : now()->toDateString();

        $daftar = Antrian::with('pasien')
            ->where('dokter_id', $dokter->id)
            ->whereDate('tanggal', $tanggal)
            ->orderBy('nomor_antrian')
            ->get();

        $hitung = fn(array $status) => $daftar->whereIn('status', $status)->count();

        return response()->json([
            'data' => [
                'dokter' => [
                    'id'        => $dokter->id,
                    'nama'      => $dokter->nama,
                    'spesialis' => $dokter->spesialis,
                    'poli'      => $dokter->poli?->nama,
                ],
                'tanggal'   => $tanggal,
                'ringkasan' => [
                    'total'    => $daftar->count(),
                    'menunggu' => $hitung(['menunggu']),
                    'diproses' => $hitung(['dipanggil', 'skrining']),
                    'selesai'  => $hitung(['selesai']),
                    'dilewati' => $hitung(['dilewati']),
                    'batal'    => $hitung(['batal']),
                ],
                'antrian' => $daftar->map(fn(Antrian $a) => [
                    'id'              => $a->id,
                    'nomor'           => $a->kode_antrian . '-' . str_pad((string) $a->nomor_antrian, 3, '0', STR_PAD_LEFT),
                    'status'          => $a->status,
                    'jenis_kunjungan' => $a->jenis_kunjungan,
                    'jenis_pasien'    => $a->jenis_pasien,
                    'keluhan'         => $a->catatan,
                    'waktu_daftar'    => $a->waktu_daftar,
                    'pasien'          => [
                        'nama'          => $a->pasien?->nama,
                        'no_rm'         => $a->pasien?->no_rm,
                        'jenis_kelamin' => $a->pasien?->jenis_kelamin,
                        'umur'          => $a->pasien?->tanggal_lahir ? Carbon::parse($a->pasien->tanggal_lahir)->age : null,
                    ],
                ])->values(),
            ],
        ]);
    }
}