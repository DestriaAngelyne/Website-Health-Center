<?php

namespace App\Http\Controllers\Api\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AntrianController extends Controller
{
    public function hariIni(Request $request)
    {
        $user = Auth::user();

        // Menggunakan relasi atau query manual untuk mendapatkan data pasien
        $pasien = Pasien::where('user_id', $user->id)->first();

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Data profil pasien tidak ditemukan.',
                'data' => null
            ], 200);
        }

        // REVISI: Menggunakan kolom 'tanggal' (date) bukan 'created_at' (timestamp)
        // Agar lebih akurat sesuai pendaftaran
        $antrian = Antrian::with(['sesiAntrian', 'poli', 'dokter'])
            ->where('pasien_id', $pasien->id)
            ->whereDate('tanggal', Carbon::today())
            ->whereNotIn('status', ['batal', 'selesai']) // Tampilkan yang masih proses
            ->latest()
            ->first();

        if (!$antrian) {
            return response()->json([
                'success' => true,
                'message' => 'Anda belum memiliki antrian aktif hari ini.',
                'data' => null
            ], 200);
        }

        // Hitung posisi: Berapa orang di depan pasien ini yang statusnya 'menunggu'
        $posisi = Antrian::where('sesi_antrian_id', $antrian->sesi_antrian_id)
            ->where('status', 'menunggu')
            ->where('nomor_antrian', '<', $antrian->nomor_antrian)
            ->count();

        // Nomor yang sedang dipanggil oleh perawat/dokter di sesi tersebut
        $sedangDipanggil = Antrian::where('sesi_antrian_id', $antrian->sesi_antrian_id)
            ->where('status', 'dipanggil')
            ->max('nomor_antrian'); // Ambil nomor terakhir yang dipanggil

        return response()->json([
            'success' => true,
            'data' => [
                'id'               => $antrian->id,
                'nomor_antrian'    => $antrian->nomor_antrian,
                'kode_antrian'     => $antrian->kode_antrian,
                'nomor_display'    => $antrian->kode_antrian . '-' . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'status'           => $antrian->status,
                'posisi'           => $posisi,
                'antrian_sekarang' => $sedangDipanggil ?? 0,
                'poli'             => $antrian->poli?->nama ?? '-',
                'dokter'           => $antrian->dokter?->nama ?? '-',
                'tanggal'          => $antrian->tanggal->format('d M Y'),
            ]
        ]);
    }
}
