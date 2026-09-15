<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class ForgotPasswordController extends Controller
{
    // ── STEP 1: Kirim OTP ke email ────────────────────────────────
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan.',
            ], 404);
        }

        // Hapus OTP lama kalau ada
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Generate OTP 6 digit
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan OTP (di-hash)
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => bcrypt($otp),
            'created_at' => Carbon::now(),
        ]);

        // Kirim email setelah response (non-blocking)
        defer(function () use ($otp, $user) {
            Mail::send(
                'emails.reset-password',
                ['otp' => $otp, 'user' => $user],
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Kode OTP Reset Password - Graha Medika');
                }
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email kamu. Berlaku selama 10 menit.',
        ]);
    }

    // ── STEP 2: Verifikasi OTP ────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak ditemukan. Silakan minta kode baru.',
            ], 400);
        }

        // Cek apakah OTP sudah kadaluarsa (10 menit)
        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.',
            ], 400);
        }

        // Cek apakah OTP cocok
        if (!Hash::check($request->otp, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah. Silakan coba lagi.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP valid.',
        ]);
    }

    // ── STEP 3: Simpan password baru ─────────────────────────────
    public function newPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp'      => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid. Silakan ulangi dari awal.',
            ], 400);
        }

        // Cek kadaluarsa
        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'Sesi kadaluarsa. Silakan minta kode OTP baru.',
            ], 400);
        }

        // Cek OTP
        if (!Hash::check($request->otp, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak valid. Silakan ulangi dari awal.',
            ], 400);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus OTP
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah. Silakan login.',
        ]);
    }
}
