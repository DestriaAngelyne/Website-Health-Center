<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PendaftaranPoliPageController extends Controller
{
    /**
     * Render halaman Vue pendaftaran poli via Inertia.
     * Kirim data pasien milik user yang login sebagai props.
     */
    public function index(): Response
    {
        $pasien = Pasien::where('user_id', Auth::id())->first();

        return Inertia::render('Pendaftaran/PendaftaranPoli', [
            'pasien' => $pasien ? [
                'id'           => $pasien->id,
                'no_rm'        => $pasien->no_rm,
                'nama'         => $pasien->nama,
                'nik'          => $pasien->nik,
                'no_bpjs'      => $pasien->no_bpjs,
                'jenis_pasien' => $pasien->jenis_pasien,
                'no_hp'        => $pasien->no_hp,
                'tanggal_lahir'=> $pasien->tanggal_lahir?->format('d M Y'),
            ] : null,
        ]);
    }
}
