import type { MenuItem } from "@/layouts/default-layout/config/types";
const PerawatMenuConfig: Array<MenuItem> = [
  { heading: "Utama", pages: [{ heading: "Dashboard", route: "/perawat/dashboard", keenthemesIcon: "element-11", bootstrapIcon: "bi-house" }] },
  { heading: "Antrian", pages: [{ heading: "Antrian Poli", route: "/perawat/antrian", keenthemesIcon: "abstract-26", bootstrapIcon: "bi-list-ol" }, { heading: "Panggil Pasien", route: "/perawat/antrian/panggil", keenthemesIcon: "notification-on", bootstrapIcon: "bi-megaphone" }] },
];
export default PerawatMenuConfig;
