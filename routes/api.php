<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;

// Admin
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\ObatController;
use App\Http\Controllers\Api\Admin\MonitorController;
use App\Http\Controllers\Api\Admin\LaporanController;

// Master
use App\Http\Controllers\Api\Master\PoliController;
use App\Http\Controllers\Api\Master\DokterController;
use App\Http\Controllers\Api\Master\JadwalController;

// Loket
use App\Http\Controllers\Api\Loket\AntrianController as LoketAntrianController;

// Dokter
use App\Http\Controllers\Api\Dokter\AntrianController as DokterAntrianController;
use App\Http\Controllers\Api\Dokter\KunjunganController as DokterKunjunganController;

// Perawat
use App\Http\Controllers\Api\Perawat\DashboardController as PerawatDashboardController;
use App\Http\Controllers\Api\Perawat\AntrianController as PerawatAntrianController;

// Pasien
use App\Http\Controllers\Api\Pasien\ProfilController;
use App\Http\Controllers\Api\Pasien\PendaftaranController;
use App\Http\Controllers\Api\Pasien\DaftarAntrianController;
use App\Http\Controllers\Api\Pasien\AntrianController as PasienAntrianController;
use App\Http\Controllers\Api\Pasien\RiwayatController;

// Publik
use App\Http\Controllers\Api\Publik\AntrianStatusController;

// ── Public Routes ─────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',        [LoginController::class,          'login']);
    Route::post('register',     [RegisterController::class,       'register']);
    Route::post('send-otp',     [ForgotPasswordController::class, 'sendOtp']);     // Step 1: kirim OTP ke email
    Route::post('verify-otp',   [ForgotPasswordController::class, 'verifyOtp']);   // Step 2: verifikasi OTP
    Route::post('new-password', [ForgotPasswordController::class, 'newPassword']); // Step 3: simpan password baru
});

// Master Data (public)
Route::get('poli',                         [PendaftaranController::class, 'getPoli']);
Route::get('dokter/{poli_id}',             [PendaftaranController::class, 'getDokterByPoli']);
Route::get('jadwal/{dokter_id}',           [PendaftaranController::class, 'getJadwalByDokter']);
Route::get('tanggal-tersedia/{dokter_id}', [PendaftaranController::class, 'getTanggalTersedia']);

// Status Antrian Publik (untuk scan QR, tanpa login)
Route::get('antrian/status', [AntrianStatusController::class, 'show']);

// ── Protected Routes ──────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout', [LogoutController::class, 'logout']);
        Route::get('me',      [LogoutController::class, 'me']);
    });

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Dashboard
        Route::get('dashboard', [AdminDashboardController::class, 'index']);

        // Users
        Route::get   ('users',                     [UserController::class, 'index']);
        Route::post  ('users',                     [UserController::class, 'store']);
        Route::get   ('users/{id}',                [UserController::class, 'show']);
        Route::put   ('users/{id}',                [UserController::class, 'update']);
        Route::delete('users/{id}',                [UserController::class, 'destroy']);
        Route::patch ('users/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::patch ('users/{id}/toggle-active',  [UserController::class, 'toggleActive']);

        // Poli
        Route::get   ('poli',                      [PoliController::class, 'index']);
        Route::post  ('poli',                      [PoliController::class, 'store']);
        Route::get   ('poli/{id}',                 [PoliController::class, 'show']);
        Route::put   ('poli/{id}',                 [PoliController::class, 'update']);
        Route::delete('poli/{id}',                 [PoliController::class, 'destroy']);
        Route::patch ('poli/{id}/toggle-active',   [PoliController::class, 'toggleActive']);

        // Dokter
        Route::get   ('dokter',                    [DokterController::class, 'index']);
        Route::post  ('dokter',                    [DokterController::class, 'store']);
        Route::get   ('dokter/{id}',               [DokterController::class, 'show']);
        Route::put   ('dokter/{id}',               [DokterController::class, 'update']);
        Route::delete('dokter/{id}',               [DokterController::class, 'destroy']);
        Route::patch ('dokter/{id}/toggle-active', [DokterController::class, 'toggleActive']);

        // Jadwal
        Route::get   ('jadwal',                    [JadwalController::class, 'index']);
        Route::post  ('jadwal',                    [JadwalController::class, 'store']);
        Route::get   ('jadwal/{id}',               [JadwalController::class, 'show']);
        Route::put   ('jadwal/{id}',               [JadwalController::class, 'update']);
        Route::delete('jadwal/{id}',               [JadwalController::class, 'destroy']);

        // Obat
        Route::get   ('master/obat/kategori-list', [ObatController::class, 'kategoriList']);
        Route::get   ('master/obat',               [ObatController::class, 'index']);
        Route::post  ('master/obat',               [ObatController::class, 'store']);
        Route::get   ('master/obat/{id}',          [ObatController::class, 'show']);
        Route::put   ('master/obat/{id}',          [ObatController::class, 'update']);
        Route::delete('master/obat/{id}',          [ObatController::class, 'destroy']);
        Route::patch ('master/obat/{id}/toggle',   [ObatController::class, 'toggle']);

        // Monitor Antrian
        Route::get   ('antrian/monitor',           [MonitorController::class, 'index']);
        Route::patch ('antrian/{id}/status',       [MonitorController::class, 'updateStatus']);

        // Laporan
        Route::get   ('laporan/harian',            [LaporanController::class, 'harian']);
        Route::get   ('laporan/bulanan',           [LaporanController::class, 'bulanan']);
    });

    // ── Loket ─────────────────────────────────────────────────────────────────
    Route::middleware('role:loket')->prefix('loket')->group(function () {
        Route::get  ('dashboard',             [LoketAntrianController::class, 'dashboard']);
        Route::get  ('sesi',                  [LoketAntrianController::class, 'getSesi']);
        Route::post ('sesi/buka',             [LoketAntrianController::class, 'bukaSesi']);
        Route::post ('sesi/buka-semua',       [LoketAntrianController::class, 'bukaSesiOtomatis']);
        Route::patch('sesi/{id}/tutup',       [LoketAntrianController::class, 'tutupSesi']);
        Route::get  ('antrian',               [LoketAntrianController::class, 'getAntrian']);
        Route::post ('antrian/daftar',        [LoketAntrianController::class, 'daftarManual']);
        Route::patch('antrian/{id}/batalkan', [LoketAntrianController::class, 'batalkan']);
        Route::get  ('pasien/cari',           [LoketAntrianController::class, 'cariPasien']);
    });

    // ── Perawat ───────────────────────────────────────────────────────────────
    Route::middleware('role:perawat')->prefix('perawat')->group(function () {
        Route::get  ('dashboard',              [PerawatDashboardController::class, 'index']);
        Route::get  ('antrian',                [PerawatAntrianController::class,   'index']);
        Route::get  ('antrian/panggil/{id?}',  [PerawatAntrianController::class,   'panggilDetail']);
        Route::patch('antrian/{id}/panggil',   [PerawatAntrianController::class,   'panggil']);
        Route::post ('antrian/mulai/{id}',     [PerawatAntrianController::class,   'mulai']);
        Route::patch('antrian/{id}/selesai',   [PerawatAntrianController::class,   'selesai']);
        Route::post ('antrian/tutup-sesi',     [PerawatAntrianController::class,   'tutupSesi']);
    });

    // ── Dokter ────────────────────────────────────────────────────────────────
    Route::middleware('role:dokter')->prefix('dokter')->group(function () {
        Route::get  ('dashboard',                        [DokterAntrianController::class,   'dashboard']);
        Route::get  ('antrian',                          [DokterAntrianController::class,   'index']);
        Route::patch('antrian/{id}/panggil',             [DokterAntrianController::class,   'panggil']);
        Route::patch('antrian/{id}/mulai',               [DokterAntrianController::class,   'mulai']);
        Route::patch('antrian/{id}/lewati',              [DokterAntrianController::class,   'lewati']);

        Route::get  ('kunjungan/{antrian_id}',           [DokterKunjunganController::class, 'show']);
        Route::post ('kunjungan/{antrian_id}/simpan',    [DokterKunjunganController::class, 'simpan']);
        Route::post ('kunjungan/{antrian_id}/selesai',   [DokterKunjunganController::class, 'selesai']);
        Route::post ('kunjungan/{kunjungan_id}/resep',   [DokterKunjunganController::class, 'simpanResep']);
        Route::post ('kunjungan/{kunjungan_id}/rujukan', [DokterKunjunganController::class, 'simpanRujukan']);
        Route::get  ('riwayat',                          [DokterKunjunganController::class, 'riwayat']);

        Route::get  ('obat',                             [ObatController::class,            'index']);
    });

    // ── Pasien ────────────────────────────────────────────────────────────────
    Route::middleware('role:pasien')->prefix('pasien')->group(function () {

        // Profil
        Route::get('profil',           [ProfilController::class, 'show']);
        Route::put('profil',           [ProfilController::class, 'update']);
        Route::put('profil/password',  [ProfilController::class, 'gantiPassword']);
        Route::get('antrian/hari-ini', [PasienAntrianController::class, 'hariIni']);

        // Pendaftaran Antrian
        Route::prefix('pendaftaran')->group(function () {
            Route::get  ('poli',                 [DaftarAntrianController::class, 'getPoli']);
            Route::get  ('dokter/{poliId}',      [DaftarAntrianController::class, 'getDokterByPoli']);
            Route::get  ('sesi',                 [DaftarAntrianController::class, 'getSesi']);
            Route::post ('daftar',               [DaftarAntrianController::class, 'daftar']);
            Route::get  ('cek-antrian-aktif',    [DaftarAntrianController::class, 'cekAntrianAktif']);
            Route::patch('{antrianId}/batalkan', [DaftarAntrianController::class, 'batalkan']);
        });

        // Riwayat Kunjungan
        Route::get('riwayat',      [RiwayatController::class, 'index']);
        Route::get('riwayat/{id}', [RiwayatController::class, 'show']);

        Route::get('antrian/riwayat', [PendaftaranController::class, 'riwayatAntrian']);
        Route::get('antrian/{id}',    [PendaftaranController::class, 'detailAntrian']);
    });
});
