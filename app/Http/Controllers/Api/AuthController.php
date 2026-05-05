<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => ['login' => 'Email atau password salah']
            ], 401);
        }
        $user->api_token = Str::random(60);
        $user->save();
        return response()->json([
            'data' => [
                'token' => $user->api_token,
                'user'  => $user,
            ]
        ]);
    }

    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => ['login' => 'Email atau password salah']
            ], 401);
        }
        if ($user->role === 'admin') {
            return response()->json([
                'errors' => ['login' => 'Akun admin tidak bisa login disini. Gunakan halaman Admin Login.']
            ], 403);
        }
        $user->api_token = Str::random(60);
        $user->save();
        return response()->json([
            'data' => [
                'token' => $user->api_token,
                'user'  => $user,
            ]
        ]);
    }

    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => ['login' => 'Email atau password salah']
            ], 401);
        }
        if ($user->role !== 'admin') {
            return response()->json([
                'errors' => ['login' => 'Akses ditolak. Anda bukan admin.']
            ], 403);
        }
        $user->api_token = Str::random(60);
        $user->save();
        return response()->json([
            'data' => [
                'token' => $user->api_token,
                'user'  => $user,
            ]
        ]);
    }

    public function verifyToken(Request $request)
    {
        $request->validate(['api_token' => 'required']);
        $user = User::where('api_token', $request->api_token)->first();
        if (!$user) {
            return response()->json([
                'errors' => ['token' => 'Token tidak valid']
            ], 401);
        }
        return response()->json([
            'data' => [
                'token' => $user->api_token,
                'user'  => $user,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $header = $request->header('Authorization');
        if ($header) {
            $token = str_replace('Token ', '', $header);
            $user = User::where('api_token', $token)->first();
            if ($user) {
                $user->api_token = null;
                $user->save();
            }
        }
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:8|confirmed',
        ]);
        $user = User::create([
            'name'      => $request->first_name . ' ' . $request->last_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'api_token' => Str::random(60),
            'role'      => 'pasien',
        ]);
        return response()->json([
            'data' => [
                'token' => $user->api_token,
                'user'  => $user,
            ]
        ]);
    }
}
