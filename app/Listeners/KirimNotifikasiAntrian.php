<?php

namespace App\Listeners;

use App\Events\AntrianDilewati;
use App\Events\AntrianDipanggil;
use App\Events\AntrianSelesai;
use App\Services\NotifikasiService;

class KirimNotifikasiAntrian
{
    public function __construct(protected NotifikasiService $notifikasi)
    {
    }

    public function handleDipanggil(AntrianDipanggil $event): void
    {
        $antrian = $event->antrian;
        $user    = $antrian->pasien?->user;

        if (!$user) {
            return;
        }

        $this->notifikasi->kirim(
            user : $user,
            judul: 'Nomor Antrian Dipanggil',
            pesan: "Nomor antrian {$antrian->kode_antrian}-" . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT) . " silakan menuju {$antrian->poli?->nama}.",
            tipe : 'antrian',
            icon : 'notification-status',
            url  : '/pasien/antrian/status',
            data : ['antrian_id' => $antrian->id, 'status' => 'dipanggil'],
        );
    }

    public function handleSelesai(AntrianSelesai $event): void
    {
        $antrian = $event->antrian;
        $user    = $antrian->pasien?->user;

        if (!$user) {
            return;
        }

        $this->notifikasi->kirim(
            user : $user,
            judul: 'Pemeriksaan Selesai',
            pesan: "Antrian {$antrian->kode_antrian}-" . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT) . " di {$antrian->poli?->nama} telah selesai. Terima kasih atas kunjungan Anda.",
            tipe : 'antrian',
            icon : 'notification-status',
            url  : '/pasien/riwayat',
            data : ['antrian_id' => $antrian->id, 'status' => 'selesai'],
        );
    }

    public function handleDilewati(AntrianDilewati $event): void
    {
        $antrian = $event->antrian;
        $user    = $antrian->pasien?->user;

        if (!$user) {
            return;
        }

        $this->notifikasi->kirim(
            user : $user,
            judul: 'Nomor Antrian Dilewati',
            pesan: "Nomor antrian {$antrian->kode_antrian}-" . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT) . " dilewati karena tidak hadir saat dipanggil. Silakan hubungi petugas.",
            tipe : 'antrian',
            icon : 'notification-status',
            url  : '/pasien/antrian/status',
            data : ['antrian_id' => $antrian->id, 'status' => 'dilewati'],
        );
    }
}
