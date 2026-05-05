<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->role) {
            $query->where('role', $request->role);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name',  'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'username'   => $u->username,
                'role'       => $u->role,
                'is_active'  => $u->is_active,
                'no_hp'      => $u->no_hp,
                'created_at' => $u->created_at->format('d/m/Y'),
            ]);

        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'username'  => 'nullable|string|unique:users,username',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:admin,loket,perawat,dokter,pasien',
            'no_hp'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'username'  => $request->username ?? strtolower(str_replace(' ', '.', $request->name)) . rand(100, 999),
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'no_hp'     => $request->no_hp,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json(['message' => 'User berhasil ditambahkan.', 'data' => $user], 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['data' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $id,
            'role'      => 'required|in:admin,loket,perawat,dokter,pasien',
            'no_hp'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'no_hp'     => $request->no_hp,
            'is_active' => $request->is_active ?? $user->is_active,
        ]);

        return response()->json(['message' => 'User berhasil diperbarui.', 'data' => $user]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri.'], 422);
        }

        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate(['password' => 'required|string|min:6']);
        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Password berhasil direset.']);
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak dapat menonaktifkan akun sendiri.'], 422);
        }
        $user->update(['is_active' => !$user->is_active]);
        return response()->json(['message' => 'Status user berhasil diubah.', 'is_active' => $user->is_active]);
    }
}
