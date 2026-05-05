<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    // GET /api/admin/master/obat
    public function index(Request $request)
    {
        $query = Obat::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_obat',  'like', "%{$request->search}%")
                  ->orWhere('kode_obat', 'like', "%{$request->search}%");
            });
        }

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->status === 'aktif') {
            $query->where('is_active', true);
        } elseif ($request->status === 'nonaktif') {
            $query->where('is_active', false);
        }

        if ($request->stok === 'kritis') {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        }

        $obat = $query->orderBy('nama_obat')->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => $obat->items(),
            'meta' => [
                'total'        => $obat->total(),
                'per_page'     => $obat->perPage(),
                'current_page' => $obat->currentPage(),
                'last_page'    => $obat->lastPage(),
            ],
        ]);
    }

    // GET /api/admin/master/obat/{id}
    public function show($id)
    {
        $obat = Obat::findOrFail($id);
        return response()->json(['data' => $obat]);
    }

    // POST /api/admin/master/obat
    public function store(Request $request)
    {
        $request->validate([
            'nama_obat'    => 'required|string|max:150',
            'kategori'     => 'nullable|string|max:50',
            'satuan'       => 'required|string|max:30',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga'        => 'required|numeric|min:0',
            'deskripsi'    => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $obat = Obat::create([
            'kode_obat'    => Obat::generateKode(),
            'nama_obat'    => $request->nama_obat,
            'kategori'     => $request->kategori,
            'satuan'       => $request->satuan,
            'stok'         => $request->stok,
            'stok_minimum' => $request->stok_minimum,
            'harga'        => $request->harga,
            'deskripsi'    => $request->deskripsi,
            'is_active'    => $request->is_active ?? true,
        ]);

        return response()->json(['message' => 'Obat berhasil ditambahkan.', 'data' => $obat], 201);
    }

    // PUT /api/admin/master/obat/{id}
    public function update(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $request->validate([
            'nama_obat'    => 'required|string|max:150',
            'kategori'     => 'nullable|string|max:50',
            'satuan'       => 'required|string|max:30',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga'        => 'required|numeric|min:0',
            'deskripsi'    => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $obat->update($request->only([
            'nama_obat', 'kategori', 'satuan',
            'stok', 'stok_minimum', 'harga', 'deskripsi', 'is_active',
        ]));

        return response()->json(['message' => 'Obat berhasil diperbarui.', 'data' => $obat]);
    }

    // DELETE /api/admin/master/obat/{id}
    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();
        return response()->json(['message' => 'Obat berhasil dihapus.']);
    }

    // PATCH /api/admin/master/obat/{id}/toggle
    public function toggle($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->update(['is_active' => !$obat->is_active]);
        return response()->json([
            'message'   => 'Status obat diperbarui.',
            'is_active' => $obat->is_active,
        ]);
    }

    // GET /api/admin/master/obat/kategori-list
    public function kategoriList()
    {
        $list = Obat::distinct()->pluck('kategori')->filter()->values();
        return response()->json(['data' => $list]);
    }
}
