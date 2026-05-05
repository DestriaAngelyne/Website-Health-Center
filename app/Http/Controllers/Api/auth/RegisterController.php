<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => ['required', 'confirmed', Password::min(8)],
            'nik'                   => 'required|digits:16|unique:users,username',
            'no_hp'                 => 'required|string|max:20',
            'tanggal_lahir'         => 'required|date|before:today',
            'jenis_kelamin'         => 'required|in:L,P',
        ], [
            'name.required'         => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Alamat email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah terdaftar.',
            'password.required'     => 'Kata sandi wajib diisi.',
            'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'          => 'Kata sandi minimal 8 karakter.',
            'nik.required'          => 'NIK wajib diisi.',
            'nik.digits'            => 'NIK harus 16 digit.',
            'nik.unique'            => 'NIK sudah terdaftar.',
            'no_hp.required'        => 'Nomor telepon wajib diisi.',
            'tanggal_lahir.required'=> 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before'  => 'Tanggal lahir tidak valid.',
            'jenis_kelamin.required'=> 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'      => 'Jenis kelamin tidak valid.',
        ]);

        DB::beginTransaction();

        try {
            // Buat akun user dengan role pasien
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'username'  => $request->nik, // NIK disimpan sebagai username
                'no_hp'     => $request->no_hp,
                'role'      => 'pasien',
                'is_active' => true,
            ]);

            // Cek apakah NIK sudah ada di tabel pasien (didaftarkan loket)
            $pasien = Pasien::where('nik', $request->nik)->first();

            if ($pasien) {
                // Hubungkan akun ke data pasien yang sudah ada
                $pasien->update(['user_id' => $user->id]);
            } else {
                // Buat data pasien baru (belum pernah ke loket)
                Pasien::create([
                    'user_id'       => $user->id,
                    'nik'           => $request->nik,
                    'nama'          => $request->name,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'no_hp'         => $request->no_hp,
                    'no_rm'         => $this->generateNoRM(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Akun berhasil dibuat. Silakan masuk menggunakan email dan kata sandi Anda.',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.',
                'error'   => $e->getMessage(), // hapus baris ini di production
            ], 500);
        }
    }

    /**
     * Generate nomor rekam medis otomatis
     * Format: RM-YYYY-XXXXXX (contoh: RM-2024-000001)
     */
    private function generateNoRM(): string
    {
        $year    = date('Y');
        $prefix  = "RM-{$year}-";
        $last    = Pasien::where('no_rm', 'like', "{$prefix}%")
                         ->orderBy('no_rm', 'desc')
                         ->value('no_rm');

        $lastNum = $last ? (int) substr($last, -6) : 0;
        $newNum  = str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);

        return "{$prefix}{$newNum}";
    }
}
