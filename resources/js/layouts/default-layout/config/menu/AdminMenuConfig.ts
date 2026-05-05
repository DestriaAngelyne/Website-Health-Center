import type { MenuItem } from "@/layouts/default-layout/config/types";

const AdminMenuConfig: Array<MenuItem> = [
  {
    heading: "Utama",
    pages: [
      {
        heading        : "Dashboard",
        route          : "/admin/dashboard",
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
        route          : "/admin/users",
        keenthemesIcon : "people",
        bootstrapIcon  : "bi-people",
        sub: [
          { heading: "Daftar User",  route: "/admin/users" },
          { heading: "Tambah User",  route: "/admin/users/create" },
        ],
      },
    ],
  },
  {
    heading: "Master Data",
    pages: [
      {
        sectionTitle   : "Poli",
        route          : "/admin/master/poli",
        keenthemesIcon : "heart",          // ← icon valid keenthemes
        bootstrapIcon  : "bi-hospital",
        sub: [
          { heading: "Daftar Poli",  route: "/admin/master/poli" },
          { heading: "Tambah Poli",  route: "/admin/master/poli/create" },
        ],
      },
      {
        sectionTitle   : "Dokter",
        route          : "/admin/master/dokter",
        keenthemesIcon : "profile-user",
        bootstrapIcon  : "bi-person-badge",
        sub: [
          { heading: "Daftar Dokter",  route: "/admin/master/dokter" },
          { heading: "Tambah Dokter",  route: "/admin/master/dokter/create" },
        ],
      },
      {
        sectionTitle   : "Jadwal",
        route          : "/admin/master/jadwal",
        keenthemesIcon : "calendar",
        bootstrapIcon  : "bi-calendar3",
        sub: [
          { heading: "Jadwal Dokter",  route: "/admin/master/jadwal" },
          { heading: "Tambah Jadwal",  route: "/admin/master/jadwal/create" },
        ],
      },
      {
        sectionTitle   : "Obat",
        route          : "/admin/master/obat",
        keenthemesIcon : "capsule",        // ← icon valid keenthemes
        bootstrapIcon  : "bi-capsule",
        sub: [
          { heading: "Daftar Obat",  route: "/admin/master/obat" },
          { heading: "Tambah Obat",  route: "/admin/master/obat/create" },
        ],
      },
    ],
  },
  {
    heading: "Antrian",
    pages: [
      {
        heading        : "Monitor Antrian",
        route          : "/admin/antrian/monitor",
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
        route          : "/admin/laporan/harian",
        keenthemesIcon : "chart-simple",
        bootstrapIcon  : "bi-bar-chart",
      },
      {
        heading        : "Laporan Bulanan",
        route          : "/admin/laporan/bulanan",
        keenthemesIcon : "chart-line-up",
        bootstrapIcon  : "bi-graph-up",
      },
    ],
  },
];

export default AdminMenuConfig;
