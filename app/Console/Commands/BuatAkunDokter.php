<?php

namespace App\Console\Commands;

use App\Models\Dokter;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BuatAkunDokter extends Command
{
    protected $signature = 'dokter:buat-akun {--password=dokter123 : Password awal untuk semua akun} {--domain=puskesmas.com : Domain email} {--dry-run : Hanya tampilkan rencana tanpa menyimpan}';

    protected $description = 'Buat akun login (role dokter) untuk dokter yang belum punya akun';

    public function handle(): int
    {
        $password = (string) $this->option('password');
        $domain   = ltrim((string) $this->option('domain'), '@');
        $dryRun   = (bool) $this->option('dry-run');

        if (strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');
            return self::FAILURE;
        }

        $dokterList = Dokter::with('poli')->whereNull('user_id')->orderBy('id')->get();

        if ($dokterList->isEmpty()) {
            $this->info('Semua dokter sudah punya akun.');
            return self::SUCCESS;
        }

        $dipakai = [];
        $baris   = [];

        foreach ($dokterList as $dokter) {
            $dasar    = $this->usernameDasar($dokter->nama);
            $username = $dasar;
            $n        = 2;
            while ($this->sudahDipakai($username, $domain, $dipakai)) {
                $username = $dasar . $n;
                $n++;
            }
            $dipakai[] = $username;
            $email     = $username . '@' . $domain;

            if (! $dryRun) {
                DB::transaction(function () use ($dokter, $username, $email, $password) {
                    $user = User::create([
                        'name'      => $dokter->nama,
                        'email'     => $email,
                        'username'  => $username,
                        'password'  => Hash::make($password),
                        'role'      => 'dokter',
                        'no_hp'     => $dokter->no_hp,
                        'is_active' => $dokter->is_active,
                    ]);
                    $dokter->update(['user_id' => $user->id]);
                });
            }

            $baris[] = [$dokter->id, $dokter->nama, $dokter->poli?->nama, $username, $email];
        }

        $this->table(['ID', 'Dokter', 'Poli', 'Username', 'Email'], $baris);
        $this->info(($dryRun ? '[DRY RUN] Akan dibuat: ' : 'Akun dibuat: ') . count($baris) . ' | Password awal: ' . $password);

        return self::SUCCESS;
    }

    private function usernameDasar(string $nama): string
    {
        $nama = preg_replace('/^\s*(drg|dr)\.?\s+/i', '', $nama);
        $nama = preg_replace('/,.*$/', '', $nama);
        $slug = Str::slug(trim($nama), '.');

        return $slug !== '' ? $slug : 'dokter';
    }

    private function sudahDipakai(string $username, string $domain, array $dipakai): bool
    {
        return in_array($username, $dipakai, true)
            || User::withTrashed()->where('username', $username)->exists()
            || User::withTrashed()->where('email', $username . '@' . $domain)->exists();
    }
}