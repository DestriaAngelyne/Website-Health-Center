import type { MenuItem } from "@/layouts/default-layout/config/types";

// Dokter: melihat antrean pasien miliknya
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
    ],
  },
];

export default DokterMenuConfig;