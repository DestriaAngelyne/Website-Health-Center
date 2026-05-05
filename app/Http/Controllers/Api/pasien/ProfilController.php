<?php

namespace App\Http\Controllers\Api\Pasien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    // GET /api/pasien/profil
    public function show(Request $request)
    {
        $user   = $request->user();
        $pasien = $user->pasien;

        return response()->json([
            'success' => true,
            'data'    => [
                'nama_lengkap'  => $pasien?->nama   ?? $user->name,
                'email'         => $user->email,
                'nik'           => $pasien?->nik,
                'tanggal_lahir' => $pasien?->tanggal_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $pasien?->jenis_kelamin,
                'no_telepon'    => $pasien?->no_hp,
                'alamat'        => $pasien?->alamat,
                'golongan_darah'=> $pasien?->golongan_darah,
                'no_bpjs'       => $pasien?->no_bpjs,
                'no_rm'         => $pasien?->no_rm,
                'jenis_jaminan' => $pasien?->jenis_pasien,
            ]
        ]);
    }

    // PUT /api/pasien/profil
    public function update(Request $request)
    {
        $user     = $request->user();
        $pasien   = $user->pasien;
        $pasienId = $pasien?->id ?? 0;
        $userId   = $user->id;

        $request->validate([
            'nama'          => 'required|string|max:255',
            'nik'           => [
                'required',
                'digits:16',
                Rule::unique('pasien', 'nik')->ignore($pasienId),
            ],
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp'         => 'required|string|max:20',
            'email'         => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'jenis_jaminan' => 'nullable|string|in:bpjs,umum',
            'no_bpjs'       => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('pasien', 'no_bpjs')
                    ->ignore($pasienId)
                    ->whereNotNull('no_bpjs')
                    ->where(fn($q) => $q->where('no_bpjs', '!=', '')),
            ],
        ], [
            'nama.required'          => 'Nama lengkap wajib diisi.',
            'nik.required'           => 'NIK wajib diisi.',
            'nik.digits'             => 'NIK harus 16 digit.',
            'nik.unique'             => 'NIK sudah terdaftar di akun lain.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'no_hp.required'         => 'Nomor telepon wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah digunakan akun lain.',
            'no_bpjs.unique'         => 'Nomor BPJS sudah terdaftar di akun lain.',
        ]);

        // Update tabel users
        $user->update([
            'name'  => $request->nama,
            'email' => $request->email,
        ]);

        // Update tabel pasien
        if ($pasien) {
            $pasien->update([
                'nama'          => $request->nama,
                'nik'           => $request->nik,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp'         => $request->no_hp,
                'no_bpjs'       => $request->no_bpjs ?: null,
                'jenis_pasien'  => $request->jenis_jaminan ?: $pasien->jenis_pasien,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
        ]);
    }

    // PUT /api/pasien/profil/password
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
