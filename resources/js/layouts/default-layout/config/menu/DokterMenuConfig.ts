import type { MenuItem } from "@/layouts/default-layout/config/types";

// Dokter: melihat antrean dan memeriksa pasien miliknya
const DokterMenuConfig: Array<MenuItem> = [
  {
    heading: "Utama",
    pages: [
      {
        heading        : "Dashboard",
        route          : "/dokter/dashboard",
        keenthemesIcon : "element-11",
        bootstrapIcon  : "bi-house",
      },
      {
        heading        : "Pemeriksaan",
        route          : "/dokter/pemeriksaan",
        keenthemesIcon : "notepad-edit",
        bootstrapIcon  : "bi-clipboard2-pulse",
      },
    ],
  },
];

export default DokterMenuConfig;