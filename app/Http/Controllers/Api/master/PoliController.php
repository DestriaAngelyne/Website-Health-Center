<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    public function index()
    {
        $poli = Poli::withCount(['dokter', 'sesiAntrian'])
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'kode'         => $p->kode,
                'nama'         => $p->nama,
                'singkatan'    => $p->singkatan,
                'keterangan'   => $p->keterangan,
                'is_active'    => $p->is_active,
                'jumlah_dokter'=> $p->dokter_count,
                'created_at'   => $p->created_at->format('d/m/Y'),
            ]);

        return response()->json(['data' => $poli]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'      => 'required|string|max:10|unique:poli,kode',
            'nama'      => 'required|string|max:100',
            'singkatan' => 'required|string|max:10',
            'keterangan'=> 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $poli = Poli::create([
            'kode'      => strtoupper($request->kode),
            'nama'      => $request->nama,
            'singkatan' => strtoupper($request->singkatan),
            'keterangan'=> $request->keterangan,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json(['message' => 'Poli berhasil ditambahkan.', 'data' => $poli], 201);
    }

    public function show($id)
    {
        $poli = Poli::with(['dokter' => fn($q) => $q->where('is_active', true)])->findOrFail($id);
        return response()->json(['data' => $poli]);
    }

    public function update(Request $request, $id)
    {
        $poli = Poli::findOrFail($id);

        $request->validate([
            'kode'      => 'required|string|max:10|unique:poli,kode,' . $id,
            'nama'      => 'required|string|max:100',
            'singkatan' => 'required|string|max:10',
            'keterangan'=> 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $poli->update([
            'kode'      => strtoupper($request->kode),
            'nama'      => $request->nama,
            'singkatan' => strtoupper($request->singkatan),
            'keterangan'=> $request->keterangan,
            'is_active' => $request->is_active ?? $poli->is_active,
        ]);

        return response()->json(['message' => 'Poli berhasil diperbarui.', 'data' => $poli]);
    }

    public function destroy($id)
    {
        $poli = Poli::findOrFail($id);

        if ($poli->dokter()->count() > 0) {
            return response()->json(['message' => 'Poli tidak dapat dihapus karena masih memiliki dokter.'], 422);
        }

        $poli->delete();
        return response()->json(['message' => 'Poli berhasil dihapus.']);
    }

    public function toggleActive($id)
    {
        $poli = Poli::findOrFail($id);
        $poli->update(['is_active' => !$poli->is_active]);
        return response()->json([
            'message'   => 'Status poli berhasil diubah.',
            'is_active' => $poli->is_active,
        ]);
    }
}
