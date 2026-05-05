import type { MenuItem } from "@/layouts/default-layout/config/types";

const PasienMenuConfig: Array<MenuItem> = [
    { heading: "Utama", pages: [
        { heading: "Dashboard", route: "/pasien/dashboard", keenthemesIcon: "element-11", bootstrapIcon: "bi-house" }
    ]},
    { heading: "Antrian", pages: [
        { heading: "Status Antrian", route: "/pasien/antrian/status", keenthemesIcon: "abstract-26", bootstrapIcon: "bi-list-ol" },
    ]},
    { heading: "Riwayat", pages: [
        { heading: "Riwayat Kunjungan", route: "/pasien/riwayat", keenthemesIcon: "document", bootstrapIcon: "bi-clock-history" }
    ]},
];

export default PasienMenuConfig;
