// resources/js/services/notifikasi.service.ts

import api from "@/services/api";

export const NotifikasiService = {
  getAll() {
    return api.get("/pasien/notifikasi");
  },

  tandaiBaca(id: number) {
    return api.patch(`/pasien/notifikasi/${id}/baca`);
  },

  tandaiSemuaBaca() {
    return api.patch("/pasien/notifikasi/baca-semua");
  },
};
