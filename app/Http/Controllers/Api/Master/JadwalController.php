<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\JadwalDokter;
use App\Models\Dokter;
use Illuminate\Http\Request;
class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalDokter::with(['dokter.poli'])->orderBy('hari');

        if ($request->dokter_id) {
            $query->where('dokter_id', $request->dokter_id);
        }
        if ($request->poli_id) {
            $query->whereHas('dokter', fn($q) => $q->where('poli_id', $request->poli_id));
        }

        $jadwal = $query->get()->map(fn($j) => [
            'id'        => $j->id,
            'dokter_id' => $j->dokter_id,
            'dokter'    => $j->dokter?->nama,
            'poli'      => $j->dokter?->poli?->nama,
            'hari'      => $j->hari,
            'jam_buka'  => substr($j->jam_buka,  0, 5),
            'jam_tutup' => substr($j->jam_tutup, 0, 5),
            'kuota'     => $j->kuota,
            'is_active' => $j->is_active,
        ]);

        return response()->json(['data' => $jadwal]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokter,id',
            'hari'      => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_buka'  => 'required|date_format:H:i',
            'jam_tutup' => 'required|date_format:H:i|after:jam_buka',
            'kuota'     => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        // Cek duplikat
        $exists = JadwalDokter::where('dokter_id', $request->dokter_id)
            ->where('hari', $request->hari)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Jadwal untuk dokter ini di hari yang sama sudah ada.'], 422);
        }

        $jadwal = JadwalDokter::create([
            'dokter_id' => $request->dokter_id,
            'hari'      => $request->hari,
            'jam_buka'  => $request->jam_buka,
            'jam_tutup' => $request->jam_tutup,
            'kuota'     => $request->kuota,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json(['message' => 'Jadwal berhasil ditambahkan.', 'data' => $jadwal], 201);
    }

    public function show($id)
    {
        $jadwal = JadwalDokter::with('dokter.poli')->findOrFail($id);
        return response()->json(['data' => $jadwal]);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalDokter::findOrFail($id);

        $request->validate([
            'dokter_id' => 'required|exists:dokter,id',
            'hari'      => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_buka'  => 'required|date_format:H:i',
            'jam_tutup' => 'required|date_format:H:i|after:jam_buka',
            'kuota'     => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $jadwal->update([
            'dokter_id' => $request->dokter_id,
            'hari'      => $request->hari,
            'jam_buka'  => $request->jam_buka,
            'jam_tutup' => $request->jam_tutup,
            'kuota'     => $request->kuota,
            'is_active' => $request->is_active ?? $jadwal->is_active,
        ]);

        return response()->json(['message' => 'Jadwal berhasil diperbarui.', 'data' => $jadwal]);
    }

    public function destroy($id)
    {
        JadwalDokter::findOrFail($id)->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }
}
