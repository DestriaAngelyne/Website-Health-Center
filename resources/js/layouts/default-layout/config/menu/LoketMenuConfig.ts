import type { MenuItem } from "@/layouts/default-layout/config/types";
const LoketMenuConfig: Array<MenuItem> = [
  { heading: "Utama", pages: [{ heading: "Dashboard", route: "/loket/dashboard", keenthemesIcon: "element-11", bootstrapIcon: "bi-house" }] },
  { heading: "Pendaftaran", pages: [{ sectionTitle: "Pendaftaran Pasien", route: "/loket/pendaftaran", keenthemesIcon: "pencil", bootstrapIcon: "bi-person-plus", sub: [{ heading: "Daftar Pendaftaran", route: "/loket/pendaftaran" }, { heading: "Pasien Baru", route: "/loket/pendaftaran/baru" }, { heading: "Pasien Lama", route: "/loket/pendaftaran/lama" }] }] },
  { heading: "Antrian", pages: [{ heading: "Antrian Hari Ini", route: "/loket/antrian", keenthemesIcon: "abstract-26", bootstrapIcon: "bi-list-ol" }, { heading: "Cetak Tiket", route: "/loket/antrian/tiket", keenthemesIcon: "printer", bootstrapIcon: "bi-printer" }] },
  { heading: "Data", pages: [{ heading: "Data Pasien", route: "/loket/pasien", keenthemesIcon: "people", bootstrapIcon: "bi-people" }] },
];
export default LoketMenuConfig;
