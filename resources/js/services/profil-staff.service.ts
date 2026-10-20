// resources/js/services/profil-staff.service.ts

import api from "@/services/api";

export type StaffRole = "superadmin" | "admin";

export const ProfilStaffService = {

  getProfil(role: StaffRole) {
    return api.get(`/${role}/profil`);
  },

  updateProfil(role: StaffRole, payload: {
    nama_lengkap : string;
    email        : string;
    no_telepon?  : string;
  }) {
    return api.put(`/${role}/profil`, payload);
  },

  gantiPassword(role: StaffRole, payload: {
    password_lama       : string;
    password_baru       : string;
    konfirmasi_password : string;
  }) {
    return api.put(`/${role}/profil/password`, payload);
  },

};