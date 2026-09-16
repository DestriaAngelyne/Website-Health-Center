<?php

namespace App\Http\Controllers\Api\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Daftar notifikasi milik user yang sedang login (terbaru dulu),
     * plus jumlah yang belum dibaca.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifikasi = $user->notifikasi()
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return response()->json([
            'data'   => $notifikasi,
            'unread' => $user->notifikasi()->whereNull('dibaca_at')->count(),
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function tandaiBaca($id)
    {
        $notifikasi = Auth::user()->notifikasi()->findOrFail($id);

        if (!$notifikasi->dibaca_at) {
            $notifikasi->update(['dibaca_at' => now()]);
        }

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    /**
     * Tandai semua notifikasi milik user sebagai sudah dibaca.
     */
    public function tandaiSemuaBaca()
    {
        Auth::user()->notifikasi()->whereNull('dibaca_at')->update(['dibaca_at' => now()]);

        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
