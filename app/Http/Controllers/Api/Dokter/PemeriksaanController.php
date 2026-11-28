<?php

namespace App\Http\Controllers\Api\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PemeriksaanController extends Controller
{
    /** Antrean hanya boleh diakses oleh dokter pemiliknya. */
    private function antrianMilik(Request $request, int $id): Antrian
    {
        $dokter = Dokter::where('user_id', $request->user()->id)->first();
        abort_if(! $dokter, 404, 'Akun ini belum terhubung dengan data dokter.');

        return Antrian::with('pasien')
            ->where('id', $id)
            ->where('dokter_id', $dokter->id)
            ->firstOrFail();
    }

    private function format(Antrian $a): array
    {
        return [
            'id'                  => $a->id,
            'nomor'               => $a->kode_antrian . '-' . str_pad((string) $a->nomor_antrian, 3, '0', STR_PAD_LEFT),
            'status'              => $a->status,
            'jenis_kunjungan'     => $a->jenis_kunjungan,
            'jenis_pasien'        => $a->jenis_pasien,
            'keluhan'             => $a->catatan,
            'catatan_dokter'      => $a->catatan_dokter,
            'waktu_daftar'        => $a->waktu_daftar,
            'waktu_mulai_periksa' => $a->waktu_mulai_periksa,
            'waktu_selesai'       => $a->waktu_selesai,
            'pasien'              => [
                'nama'          => $a->pasien?->nama,
                'no_rm'         => $a->pasien?->no_rm,
                'no_bpjs'       => $a->pasien?->no_bpjs,
                'jenis_kelamin' => $a->pasien?->jenis_kelamin,
                'umur'          => $a->pasien?->tanggal_lahir ? Carbon::parse($a->pasien->tanggal_lahir)->age : null,
            ],
        ];
    }

    public function show(Request $request, int $id)
    {
        return response()->json(['data' => $this->format($this->antrianMilik($request, $id))]);
    }

    public function mulai(Request $request, int $id)
    {
        $antrian = $this->antrianMilik($request, $id);

        $hasil = DB::transaction(function () use ($antrian) {
            $a = Antrian::whereKey($antrian->id)->lockForUpdate()->first();
            if ($a->status !== 'siap_diperiksa') {
                return null;
            }
            $a->update(['status' => 'diperiksa', 'waktu_mulai_periksa' => now()]);
            return $a;
        });

        if (! $hasil) {
            return response()->json(['message' => 'Pasien belum siap diperiksa atau sudah ditangani.'], 422);
        }

        return response()->json(['message' => 'Pemeriksaan dimulai.', 'data' => $this->format($hasil->load('pasien'))]);
    }

    public function catatan(Request $request, int $id)
    {
        $data = $request->validate(['catatan_dokter' => 'nullable|string|max:2000']);
        $antrian = $this->antrianMilik($request, $id);

        if ($antrian->status !== 'diperiksa') {
            return response()->json(['message' => 'Catatan hanya bisa diisi saat pasien sedang diperiksa.'], 422);
        }

        $antrian->update(['catatan_dokter' => $data['catatan_dokter'] ?? null]);

        return response()->json(['message' => 'Catatan tersimpan.', 'data' => $this->format($antrian->fresh('pasien'))]);
    }

    public function selesai(Request $request, int $id)
    {
        $data = $request->validate(
            ['catatan_dokter' => 'required|string|max:2000'],
            ['catatan_dokter.required' => 'Catatan pemeriksaan wajib diisi.']
        );
        $antrian = $this->antrianMilik($request, $id);

        $hasil = DB::transaction(function () use ($antrian, $data) {
            $a = Antrian::whereKey($antrian->id)->lockForUpdate()->first();
            if ($a->status !== 'diperiksa') {
                return null;
            }
            $a->update([
                'catatan_dokter' => $data['catatan_dokter'],
                'status'         => 'selesai',
                'waktu_selesai'  => now(),
            ]);
            return $a;
        });

        if (! $hasil) {
            return response()->json(['message' => 'Pasien belum diperiksa atau sudah selesai.'], 422);
        }

        return response()->json(['message' => 'Pemeriksaan selesai.', 'data' => $this->format($hasil->load('pasien'))]);
    }
}