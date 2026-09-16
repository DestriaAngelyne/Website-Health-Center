<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;

class NotifikasiService
{
    /**
     * Kirim (simpan) satu notifikasi untuk seorang user.
     */
    public function kirim(
        User $user,
        string $judul,
        string $pesan,
        ?string $tipe = 'antrian',
        ?string $icon = null,
        ?string $url = null,
        ?array $data = null
    ): Notifikasi {
        return Notifikasi::create([
            'user_id' => $user->id,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'icon'    => $icon,
            'url'     => $url,
            'data'    => $data,
        ]);
    }
}
