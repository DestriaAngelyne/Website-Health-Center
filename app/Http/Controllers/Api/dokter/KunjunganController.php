<?php

namespace App\Http\Controllers\Api\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\Rujukan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KunjunganController extends Controller
{
    // GET /api/dokter/kunjungan/{antrian_id}
    public function show($antrian_id)
    {
        $antrian   = Antrian::with(['pasien', 'poli', 'dokter'])->findOrFail($antrian_id);
        $kunjungan = Kunjungan::with(['resep.detail.obat', 'rujukan', 'tindakan'])
            ->where('antrian_id', $antrian_id)
            ->first();

        $pasien = $antrian->pasien;

        // Riwayat kunjungan sebelumnya
        $riwayat = Kunjungan::where('pasien_id', $pasien->id)
            ->where('status', 'selesai')
            ->orderByDesc('tanggal_kunjungan')
            ->limit(5)
            ->get()
            ->map(fn($k) => [
                'id'               => $k->id,
                'tanggal'          => $k->tanggal_kunjungan?->format('d/m/Y'),
                'diagnosa'         => $k->diagnosa_nama,
                'diagnosa_kode'    => $k->diagnosa_kode,
                'catatan_dokter'   => $k->catatan_dokter,
            ]);

        return response()->json([
            'antrian' => [
                'id'            => $antrian->id,
                'nomor_display' => $antrian->kode_antrian . '-' . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT),
                'jenis_pasien'  => $antrian->jenis_pasien,
                'jenis_kunjungan'=> $antrian->jenis_kunjungan,
                'catatan'       => $antrian->catatan,
                'status'        => $antrian->status,
            ],
            'pasien' => [
                'id'            => $pasien->id,
                'nama'          => $pasien->nama,
                'no_rm'         => $pasien->no_rm,
                'nik'           => $pasien->nik,
                'tanggal_lahir' => $pasien->tanggal_lahir,
                'umur'          => $pasien->tanggal_lahir
                    ? Carbon::parse($pasien->tanggal_lahir)->age . ' tahun'
                    : '-',
                'jenis_kelamin' => $pasien->jenis_kelamin,
                'no_hp'         => $pasien->no_hp,
                'alamat'        => $pasien->alamat,
                'no_bpjs'       => $pasien->no_bpjs,
                'golongan_darah'=> $pasien->golongan_darah ?? null,
                'alergi'        => $pasien->alergi ?? null,
            ],
            'poli'   => $antrian->poli?->nama,
            'dokter' => $antrian->dokter?->nama,
            'kunjungan' => $kunjungan ? [
                'id'                      => $kunjungan->id,
                'no_kunjungan'            => $kunjungan->no_kunjungan,
                'berat_badan'             => $kunjungan->berat_badan,
                'tinggi_badan'            => $kunjungan->tinggi_badan,
                'tekanan_darah'           => $kunjungan->tekanan_darah,
                'nadi'                    => $kunjungan->nadi,
                'suhu'                    => $kunjungan->suhu,
                'respirasi'               => $kunjungan->respirasi,
                'saturasi_oksigen'        => $kunjungan->saturasi_oksigen,
                'keluhan'                 => $kunjungan->keluhan,
                'anamnesis'               => $kunjungan->anamnesis,
                'pemeriksaan_fisik'       => $kunjungan->pemeriksaan_fisik,
                'diagnosa_kode'           => $kunjungan->diagnosa_kode,
                'diagnosa_nama'           => $kunjungan->diagnosa_nama,
                'diagnosa_sekunder_kode'  => $kunjungan->diagnosa_sekunder_kode,
                'diagnosa_sekunder_nama'  => $kunjungan->diagnosa_sekunder_nama,
                'rencana_terapi'          => $kunjungan->rencana_terapi,
                'catatan_dokter'          => $kunjungan->catatan_dokter,
                'status'                  => $kunjungan->status,
                'resep'                   => $kunjungan->resep ? [
                    'id'      => $kunjungan->resep->id,
                    'no_resep'=> $kunjungan->resep->no_resep,
                    'status'  => $kunjungan->resep->status,
                    'catatan' => $kunjungan->resep->catatan,
                    'detail'  => $kunjungan->resep->detail->map(fn($d) => [
                        'id'       => $d->id,
                        'obat_id'  => $d->obat_id,
                        'obat'     => $d->obat?->nama,
                        'satuan'   => $d->obat?->satuan,
                        'jumlah'   => $d->jumlah,
                        'aturan'   => $d->aturan_pakai,
                        'keterangan'=> $d->keterangan,
                    ]),
                ] : null,
                'rujukan' => $kunjungan->rujukan ? [
                    'id'            => $kunjungan->rujukan->id,
                    'no_rujukan'    => $kunjungan->rujukan->no_rujukan,
                    'faskes_tujuan' => $kunjungan->rujukan->faskes_tujuan,
                    'poli_tujuan'   => $kunjungan->rujukan->poli_tujuan,
                    'diagnosa_nama' => $kunjungan->rujukan->diagnosa_nama,
                    'alasan'        => $kunjungan->rujukan->alasan_rujukan,
                ] : null,
            ] : null,
            'riwayat' => $riwayat,
        ]);
    }

    // POST /api/dokter/kunjungan/{antrian_id}/simpan
    public function simpan(Request $request, $antrian_id)
    {
        $request->validate([
            'keluhan'          => 'required|string',
            'diagnosa_kode'    => 'nullable|string|max:20',
            'diagnosa_nama'    => 'required|string|max:255',
            'rencana_terapi'   => 'nullable|string',
            'catatan_dokter'   => 'nullable|string',
            'berat_badan'      => 'nullable|numeric',
            'tinggi_badan'     => 'nullable|numeric',
            'tekanan_darah'    => 'nullable|string|max:20',
            'nadi'             => 'nullable|integer',
            'suhu'             => 'nullable|numeric',
            'respirasi'        => 'nullable|integer',
            'saturasi_oksigen' => 'nullable|integer',
            'anamnesis'        => 'nullable|string',
            'pemeriksaan_fisik'=> 'nullable|string',
            'diagnosa_sekunder_kode' => 'nullable|string|max:20',
            'diagnosa_sekunder_nama' => 'nullable|string|max:255',
        ]);

        $kunjungan = Kunjungan::where('antrian_id', $antrian_id)->firstOrFail();

        $kunjungan->update([
            'keluhan'                => $request->keluhan,
            'anamnesis'              => $request->anamnesis,
            'pemeriksaan_fisik'      => $request->pemeriksaan_fisik,
            'diagnosa_kode'          => $request->diagnosa_kode,
            'diagnosa_nama'          => $request->diagnosa_nama,
            'diagnosa_sekunder_kode' => $request->diagnosa_sekunder_kode,
            'diagnosa_sekunder_nama' => $request->diagnosa_sekunder_nama,
            'rencana_terapi'         => $request->rencana_terapi,
            'catatan_dokter'         => $request->catatan_dokter,
            'berat_badan'            => $request->berat_badan,
            'tinggi_badan'           => $request->tinggi_badan,
            'tekanan_darah'          => $request->tekanan_darah,
            'nadi'                   => $request->nadi,
            'suhu'                   => $request->suhu,
            'respirasi'              => $request->respirasi,
            'saturasi_oksigen'       => $request->saturasi_oksigen,
        ]);

        return response()->json(['message' => 'Rekam medis berhasil disimpan.', 'kunjungan_id' => $kunjungan->id]);
    }

    // POST /api/dokter/kunjungan/{antrian_id}/selesai
    public function selesai($antrian_id)
    {
        return DB::transaction(function () use ($antrian_id) {
            $antrian   = Antrian::findOrFail($antrian_id);
            $kunjungan = Kunjungan::where('antrian_id', $antrian_id)->firstOrFail();

            if (empty($kunjungan->diagnosa_nama)) {
                return response()->json(['message' => 'Diagnosa belum diisi.'], 422);
            }

            $kunjungan->update(['status' => 'selesai', 'selesai_at' => now()]);
            $antrian->update(['status' => 'selesai', 'waktu_selesai' => now()]);

            return response()->json(['message' => 'Kunjungan selesai.']);
        });
    }

    // POST /api/dokter/kunjungan/{kunjungan_id}/resep
    public function simpanResep(Request $request, $kunjungan_id)
    {
        $request->validate([
            'catatan' => 'nullable|string',
            'detail'  => 'required|array|min:1',
            'detail.*.obat_id'    => 'required|exists:obat,id',
            'detail.*.jumlah'     => 'required|integer|min:1',
            'detail.*.aturan_pakai' => 'required|string|max:100',
            'detail.*.keterangan' => 'nullable|string|max:255',
        ]);

        $kunjungan = Kunjungan::findOrFail($kunjungan_id);

        return DB::transaction(function () use ($request, $kunjungan) {
            $resep = Resep::updateOrCreate(
                ['kunjungan_id' => $kunjungan->id],
                [
                    'dokter_id' => $kunjungan->dokter_id,
                    'no_resep'  => 'RES-' . date('Ymd') . '-' . str_pad($kunjungan->id, 4, '0', STR_PAD_LEFT),
                    'catatan'   => $request->catatan,
                    'status'    => 'menunggu',
                ]
            );

            // Hapus detail lama lalu insert ulang
            $resep->detail()->delete();

            foreach ($request->detail as $d) {
                ResepDetail::create([
                    'resep_id'    => $resep->id,
                    'obat_id'     => $d['obat_id'],
                    'jumlah'      => $d['jumlah'],
                    'aturan_pakai'=> $d['aturan_pakai'],
                    'keterangan'  => $d['keterangan'] ?? null,
                ]);
            }

            return response()->json(['message' => 'Resep berhasil disimpan.', 'resep_id' => $resep->id], 201);
        });
    }

    // POST /api/dokter/kunjungan/{kunjungan_id}/rujukan
    public function simpanRujukan(Request $request, $kunjungan_id)
    {
        $request->validate([
            'faskes_tujuan'              => 'required|string|max:255',
            'poli_tujuan'                => 'required|string|max:100',
            'diagnosa_kode'              => 'nullable|string|max:20',
            'diagnosa_nama'              => 'required|string|max:255',
            'alasan_rujukan'             => 'required|string',
            'terapi_yang_telah_diberikan'=> 'nullable|string',
            'catatan'                    => 'nullable|string',
            'jenis'                      => 'nullable|in:internal,eksternal',
            'tanggal_berlaku'            => 'nullable|date',
        ]);

        $kunjungan = Kunjungan::findOrFail($kunjungan_id);

        $rujukan = Rujukan::updateOrCreate(
            ['kunjungan_id' => $kunjungan->id],
            [
                'pasien_id'                  => $kunjungan->pasien_id,
                'dokter_id'                  => $kunjungan->dokter_id,
                'no_rujukan'                 => 'RUJ-' . date('Ymd') . '-' . str_pad($kunjungan->id, 4, '0', STR_PAD_LEFT),
                'tanggal_rujukan'            => today(),
                'tanggal_berlaku'            => $request->tanggal_berlaku,
                'faskes_tujuan'              => $request->faskes_tujuan,
                'poli_tujuan'                => $request->poli_tujuan,
                'diagnosa_kode'              => $request->diagnosa_kode,
                'diagnosa_nama'              => $request->diagnosa_nama,
                'alasan_rujukan'             => $request->alasan_rujukan,
                'terapi_yang_telah_diberikan'=> $request->terapi_yang_telah_diberikan,
                'catatan'                    => $request->catatan,
                'jenis'                      => $request->jenis ?? 'eksternal',
                'status'                     => 'aktif',
            ]
        );

        return response()->json(['message' => 'Rujukan berhasil disimpan.', 'rujukan_id' => $rujukan->id], 201);
    }

    // GET /api/dokter/riwayat
    public function riwayat(Request $request)
    {
        $user   = Auth::user();
        $dokter = \App\Models\Dokter::where('user_id', $user->id)->first();

        $query = Kunjungan::where('dokter_id', $dokter->id)
            ->with(['pasien', 'poli'])
            ->orderByDesc('tanggal_kunjungan');

        if ($request->search) {
            $query->whereHas('pasien', fn($q) =>
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('no_rm', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->tanggal) {
            $query->whereDate('tanggal_kunjungan', $request->tanggal);
        }

        $data = $query->paginate(20)->through(fn($k) => [
            'id'            => $k->id,
            'tanggal'       => $k->tanggal_kunjungan?->format('d/m/Y'),
            'pasien'        => $k->pasien?->nama,
            'no_rm'         => $k->pasien?->no_rm,
            'poli'          => $k->poli?->nama,
            'diagnosa'      => $k->diagnosa_nama,
            'diagnosa_kode' => $k->diagnosa_kode,
            'status'        => $k->status,
        ]);

        return response()->json($data);
    }
}
