<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    public function index()
    {
        $dokter = Dokter::with('poli')
            ->orderBy('nama')
            ->get()
            ->map(fn($d) => [
                'id'         => $d->id,
                'nip'        => $d->nip,
                'nama'       => $d->nama,
                'spesialis'  => $d->spesialis,
                'poli_id'    => $d->poli_id,
                'poli'       => $d->poli,
                'no_hp'      => $d->no_hp,
                'str_number' => $d->str_number,
                'is_active'  => $d->is_active,
                'created_at' => $d->created_at,
            ]);

        return response()->json(['data' => $dokter]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'        => 'nullable|string|max:20|unique:dokter,nip',
            'nama'       => 'required|string|max:100',
            'spesialis'  => 'nullable|string|max:100',
            'poli_id'    => 'required|exists:poli,id',
            'no_hp'      => 'nullable|string|max:20',
            'str_number' => 'nullable|string|max:50',
            'is_active'  => 'boolean',
        ]);

        $dokter = Dokter::create([
            'poli_id'    => $request->poli_id,
            'nip'        => $request->nip,
            'nama'       => $request->nama,
            'spesialis'  => $request->spesialis,
            'str_number' => $request->str_number,
            'no_hp'      => $request->no_hp,
            'is_active'  => $request->is_active ?? true,
        ]);

        return response()->json(['message' => 'Dokter berhasil ditambahkan.', 'data' => $dokter], 201);
    }

    public function show($id)
    {
        $dokter = Dokter::with(['poli', 'jadwalDokter'])->findOrFail($id);
        return response()->json(['data' => $dokter]);
    }

    public function update(Request $request, $id)
    {
        $dokter = Dokter::findOrFail($id);

        $request->validate([
            'nip'        => 'nullable|string|max:20|unique:dokter,nip,' . $id,
            'nama'       => 'required|string|max:100',
            'spesialis'  => 'nullable|string|max:100',
            'poli_id'    => 'required|exists:poli,id',
            'no_hp'      => 'nullable|string|max:20',
            'str_number' => 'nullable|string|max:50',
            'is_active'  => 'boolean',
        ]);

        $dokter->update([
            'poli_id'    => $request->poli_id,
            'nip'        => $request->nip,
            'nama'       => $request->nama,
            'spesialis'  => $request->spesialis,
            'str_number' => $request->str_number,
            'no_hp'      => $request->no_hp,
            'is_active'  => $request->is_active ?? $dokter->is_active,
        ]);

        return response()->json(['message' => 'Dokter berhasil diperbarui.', 'data' => $dokter]);
    }

    public function destroy($id)
    {
        $dokter = Dokter::findOrFail($id);
        $dokter->delete();
        return response()->json(['message' => 'Dokter berhasil dihapus.']);
    }

    public function toggleActive($id)
    {
        $dokter = Dokter::findOrFail($id);
        $dokter->update(['is_active' => !$dokter->is_active]);
        return response()->json([
            'message'   => 'Status dokter berhasil diubah.',
            'is_active' => $dokter->is_active,
        ]);
    }
}
