<?php

namespace App\Http\Controllers\Api\Perawat;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AntrianPoliController extends Controller
{
    /**
     * Menampilkan daftar antrian hari ini (Gambar 3)
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        $query = Antrian::with(['pasien', 'poli'])
                 ->whereDate('tanggal', $today);

        // Aktifkan ini jika sudah ingin memfilter berdasarkan poli perawat
        // if ($user && $user->poli_id) {
        //     $query->where('poli_id', $user->poli_id);
        // }

        $antrian = $query->orderBy('nomor_antrian', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $antrian
        ]);
    }

    /**
     * Mengambil detail satu pasien untuk dipanggil (Gambar 2)
     */
    public function panggilDetail($id = null)
    {
        if ($id) {
            $antrian = Antrian::with(['pasien', 'poli'])->find($id);
        } else {
            $antrian = Antrian::with(['pasien', 'poli'])
                        ->whereDate('tanggal', Carbon::today()->format('Y-m-d'))
                        ->where('status', 'menunggu')
                        ->orderBy('nomor_antrian', 'asc')
                        ->first();
        }

        if (!$antrian) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada antrian'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $antrian
        ]);
    }

    public function mulaiPeriksa($id)
    {
        $antrian = Antrian::find($id);

        if (!$antrian) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memulai: Antrian tidak ditemukan'
            ], 404);
        }

        // Update status dan catat waktu mulai (opsional)
        $antrian->update([
            'status' => 'skrining',
            // 'waktu_panggil' => now() // Jika Anda punya kolom ini di DB
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Status berhasil diperbarui menjadi skrining',
            'data'    => $antrian
        ]);
    }
}
