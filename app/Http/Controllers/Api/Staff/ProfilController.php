<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    // GET /api/{superadmin|admin}/profil
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'nama_lengkap' => $user->name,
                'username'     => $user->username,
                'email'        => $user->email,
                'no_telepon'   => $user->no_hp,
                'role'         => $user->role,
            ],
        ]);
    }

    // PUT /api/{superadmin|admin}/profil
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telepon'   => 'nullable|string|max:20',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah digunakan akun lain.',
        ]);

        $user->update([
            'name'  => $request->nama_lengkap,
            'email' => $request->email,
            'no_hp' => $request->no_telepon,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
        ]);
    }

    // PUT /api/{superadmin|admin}/profil/password
    public function gantiPassword(Request $request)
    {
        $request->validate([
            'password_lama'       => 'required',
            'password_baru'       => ['required', 'min:8', Password::defaults()],
            'konfirmasi_password' => 'required|same:password_baru',
        ], [
            'password_lama.required'       => 'Password lama wajib diisi.',
            'password_baru.required'       => 'Password baru wajib diisi.',
            'password_baru.min'            => 'Password baru minimal 8 karakter.',
            'konfirmasi_password.required' => 'Konfirmasi password wajib diisi.',
            'konfirmasi_password.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai',
                'errors'  => ['password_lama' => ['Password lama tidak sesuai']],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah',
        ]);
    }
}