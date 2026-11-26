<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DokterController extends Controller
{
    public function index()
    {
        $dokter = Dokter::with(['poli', 'user:id,username,email'])
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
                'punya_akun' => (bool) $d->user_id,
                'email'      => $d->user?->email,
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
            'username'   => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username'],
            'email'      => 'required|email|max:255|unique:users,email',
            'password'   => 'required|string|min:8',
        ], $this->pesanAkun());

        $aktif = $request->is_active ?? true;

        $dokter = DB::transaction(function () use ($request, $aktif) {
            $user = $this->buatAkun($request, $aktif);

            return Dokter::create([
                'user_id'    => $user->id,
                'poli_id'    => $request->poli_id,
                'nip'        => $request->nip,
                'nama'       => $request->nama,
                'spesialis'  => $request->spesialis,
                'str_number' => $request->str_number,
                'no_hp'      => $request->no_hp,
                'is_active'  => $aktif,
            ]);
        });

        return response()->json(['message' => 'Dokter berhasil ditambahkan.', 'data' => $dokter], 201);
    }

    public function show($id)
    {
        $dokter = Dokter::with(['poli', 'jadwalDokter', 'user:id,username,email'])->findOrFail($id);
        return response()->json(['data' => $dokter]);
    }

    public function update(Request $request, $id)
    {
        $dokter = Dokter::findOrFail($id);

        // Akun login dokter (null kalau dokter ini belum punya akun)
        $akun       = $dokter->user_id ? User::find($dokter->user_id) : null;
        $adaIsiAkun = $request->filled('username') || $request->filled('email') || $request->filled('password');
        $wajibAkun  = $akun !== null || $adaIsiAkun;

        $request->validate([
            'nip'        => 'nullable|string|max:20|unique:dokter,nip,' . $id,
            'nama'       => 'required|string|max:100',
            'spesialis'  => 'nullable|string|max:100',
            'poli_id'    => 'required|exists:poli,id',
            'no_hp'      => 'nullable|string|max:20',
            'str_number' => 'nullable|string|max:50',
            'is_active'  => 'boolean',
            'username'   => [Rule::requiredIf($wajibAkun), 'nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('users', 'username')->ignore($akun?->id)],
            'email'      => [Rule::requiredIf($wajibAkun), 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($akun?->id)],
            'password'   => [Rule::requiredIf($akun === null && $adaIsiAkun), 'nullable', 'string', 'min:8'],
        ], $this->pesanAkun());

        DB::transaction(function () use ($request, $dokter, $akun, $adaIsiAkun) {
            $dokter->update([
                'poli_id'    => $request->poli_id,
                'nip'        => $request->nip,
                'nama'       => $request->nama,
                'spesialis'  => $request->spesialis,
                'str_number' => $request->str_number,
                'no_hp'      => $request->no_hp,
                'is_active'  => $request->is_active ?? $dokter->is_active,
            ]);

            if ($akun) {
                $data = [
                    'name'      => $request->nama,
                    'email'     => $request->email,
                    'username'  => $request->username,
                    'no_hp'     => $request->no_hp,
                    'is_active' => $dokter->is_active,
                ];
                if ($request->filled('password')) {
                    $data['password'] = Hash::make($request->password);
                }
                $akun->update($data);
            } elseif ($adaIsiAkun) {
                $user = $this->buatAkun($request, $dokter->is_active);
                $dokter->update(['user_id' => $user->id]);
            }
        });

        return response()->json(['message' => 'Dokter berhasil diperbarui.', 'data' => $dokter->fresh()]);
    }

    public function destroy($id)
    {
        $dokter = Dokter::findOrFail($id);

        DB::transaction(function () use ($dokter) {
            if ($dokter->user_id) {
                User::where('id', $dokter->user_id)->delete();
            }
            $dokter->delete();
        });

        return response()->json(['message' => 'Dokter berhasil dihapus.']);
    }

    public function toggleActive($id)
    {
        $dokter = Dokter::findOrFail($id);

        DB::transaction(function () use ($dokter) {
            $dokter->update(['is_active' => !$dokter->is_active]);

            if ($dokter->user_id) {
                User::where('id', $dokter->user_id)->update(['is_active' => $dokter->is_active]);
            }
        });

        return response()->json([
            'message'   => 'Status dokter berhasil diubah.',
            'is_active' => $dokter->is_active,
        ]);
    }

    private function buatAkun(Request $request, bool $aktif): User
    {
        return User::create([
            'name'      => $request->nama,
            'email'     => $request->email,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'role'      => 'dokter',
            'no_hp'     => $request->no_hp,
            'is_active' => $aktif,
        ]);
    }

    private function pesanAkun(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.regex'    => 'Username hanya boleh berisi huruf, angka, titik, strip, dan garis bawah.',
            'username.unique'   => 'Username sudah digunakan.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
        ];
    }
}