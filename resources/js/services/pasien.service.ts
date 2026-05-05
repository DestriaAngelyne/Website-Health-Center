// resources/js/services/pasien.service.ts

import api from "@/services/api";

export const PasienService = {

  // ── Profil ───────────────────────────────────────────────────────────────────

  getProfil() {
    return api.get("/pasien/profil");
  },

  updateProfil(payload: {
    nama_lengkap  : string;
    nik           : string;
    tanggal_lahir : string;
    jenis_kelamin : string;
    no_telepon    : string;
    email         : string;
    alamat        : string;
    golongan_darah?: string;
    no_bpjs?      : string;
  }) {
    return api.put("/pasien/profil", payload);
  },

  gantiPassword(payload: {
    password_lama       : string;
    password_baru       : string;
    konfirmasi_password : string;
  }) {
    return api.put("/pasien/profil/password", payload);
  },

  // ── Pendaftaran Antrian ──────────────────────────────────────────────────────

  getPoli() {
    return api.get("/pasien/pendaftaran/poli");
  },

  getDokterByPoli(poliId: number) {
    return api.get(`/pasien/pendaftaran/dokter/${poliId}`);
  },

  getSesi(poliId: number, dokterId: number) {
    return api.get("/pasien/pendaftaran/sesi", {
      params: { poli_id: poliId, dokter_id: dokterId },
    });
  },

  daftarAntrian(payload: {
    poli_id         : number;
    dokter_id       : number;
    sesi_antrian_id : number;
    jenis_kunjungan?: string;
    catatan?        : string;
  }) {
    return api.post("/pasien/pendaftaran/daftar", payload);
  },

  cekAntrianAktif() {
    return api.get("/pasien/pendaftaran/cek-antrian-aktif");
  },

  batalkanAntrian(antrianId: number) {
    return api.patch(`/pasien/pendaftaran/${antrianId}/batalkan`);
  },

  // ── Status Antrian ───────────────────────────────────────────────────────────

  getAntrianHariIni() {
    return api.get("/pasien/antrian/hari-ini");
  },

  getDisplayAntrian() {
    return api.get("/pasien/antrian/display");
  },

  // ── Riwayat Kunjungan ────────────────────────────────────────────────────────

  getRiwayat(params?: {
    search? : string;
    poli?   : string;
    sort?   : string;
    page?   : number;
  }) {
    return api.get("/pasien/riwayat", { params });
  },

  getRiwayatDetail(id: number) {
    return api.get(`/pasien/riwayat/${id}`);
  },

  // ── Dashboard ────────────────────────────────────────────────────────────────

  getDashboard() {
    return api.get("/pasien/dashboard");
  },

};
