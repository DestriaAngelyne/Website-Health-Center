<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'message' => 'Akun anda tidak aktif. Hubungi administrator.',
            ], 403);
        }

        // Hapus token lama
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'username' => $user->username,
                'role'     => $user->role,
                'foto'     => $user->foto,
                'no_hp'    => $user->no_hp,
            ],
            'redirect' => $this->getRedirectByRole($user->role),
        ]);
    }

    private function getRedirectByRole(string $role): string
    {
        return match($role) {
            'admin'   => '/admin/dashboard',
            'loket'   => '/loket/dashboard',
            'perawat' => '/perawat/dashboard',
            'dokter'  => '/dokter/dashboard',
            'pasien'  => '/pasien/dashboard',
            default   => '/',
        };
    }
}
