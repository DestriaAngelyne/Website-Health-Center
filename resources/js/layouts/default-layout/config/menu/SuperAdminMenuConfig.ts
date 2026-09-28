import type { MenuItem } from "@/layouts/default-layout/config/types";

const SuperAdminMenuConfig: Array<MenuItem> = [
  {
    heading: "Utama",
    pages: [
      {
        heading        : "Dashboard",
        route          : "/superadmin/dashboard",
        keenthemesIcon : "element-11",
        bootstrapIcon  : "bi-speedometer2",
      },
    ],
  },
  {
    heading: "Manajemen User",
    pages: [
      {
        sectionTitle   : "Pengguna",
        route          : "/superadmin/users",
        keenthemesIcon : "people",
        bootstrapIcon  : "bi-people",
        sub: [
          { heading: "Daftar User",  route: "/superadmin/users" },
          { heading: "Tambah User",  route: "/superadmin/users/create" },
        ],
      },
    ],
  },
  {
    heading: "Master Data",
    pages: [
      {
        sectionTitle   : "Poli",
        route          : "/superadmin/master/poli",
        keenthemesIcon : "heart",          // ← icon valid keenthemes
        bootstrapIcon  : "bi-hospital",
        sub: [
          { heading: "Daftar Poli",  route: "/superadmin/master/poli" },
          { heading: "Tambah Poli",  route: "/superadmin/master/poli/create" },
        ],
      },
      {
        sectionTitle   : "Dokter",
        route          : "/superadmin/master/dokter",
        keenthemesIcon : "profile-user",
        bootstrapIcon  : "bi-person-badge",
        sub: [
          { heading: "Daftar Dokter",  route: "/superadmin/master/dokter" },
          { heading: "Tambah Dokter",  route: "/superadmin/master/dokter/create" },
        ],
      },
      {
        sectionTitle   : "Jadwal",
        route          : "/superadmin/master/jadwal",
        keenthemesIcon : "calendar",
        bootstrapIcon  : "bi-calendar3",
        sub: [
          { heading: "Jadwal Dokter",  route: "/superadmin/master/jadwal" },
          { heading: "Tambah Jadwal",  route: "/superadmin/master/jadwal/create" },
        ],
      },
    ],
  },
  {
    heading: "Antrian",
    pages: [
      {
        heading        : "Monitor Antrian",
        route          : "/superadmin/antrian/monitor",
        keenthemesIcon : "screen",
        bootstrapIcon  : "bi-display",
      },
    ],
  },
  {
    heading: "Laporan",
    pages: [
      {
        heading        : "Laporan Harian",
        route          : "/superadmin/laporan/harian",
        keenthemesIcon : "chart-simple",
        bootstrapIcon  : "bi-bar-chart",
      },
      {
        heading        : "Laporan Bulanan",
        route          : "/superadmin/laporan/bulanan",
        keenthemesIcon : "chart-line-up",
        bootstrapIcon  : "bi-graph-up",
      },
    ],
  },
];

export default SuperAdminMenuConfig;


