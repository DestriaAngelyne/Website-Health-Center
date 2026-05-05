import type { MenuItem } from "@/layouts/default-layout/config/types";
const DokterMenuConfig: Array<MenuItem> = [
  { heading: "Utama", pages: [{ heading: "Dashboard", route: "/dokter/dashboard", keenthemesIcon: "element-11", bootstrapIcon: "bi-house" }] },
  { heading: "Pasien", pages: [{ heading: "Pasien Hari Ini", route: "/dokter/antrian", keenthemesIcon: "people", bootstrapIcon: "bi-people" }, { heading: "Riwayat Pasien", route: "/dokter/riwayat", keenthemesIcon: "document", bootstrapIcon: "bi-clock-history" }] },
];
export default DokterMenuConfig;
