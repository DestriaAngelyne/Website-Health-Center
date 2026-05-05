import { RouteRecordRaw } from "vue-router";
import DefaultLayout from "@/layouts/default-layout/DefaultLayout.vue";

const pasienRoutes: RouteRecordRaw[] = [
  {
    path: "/pasien",
    component: DefaultLayout,
    meta: { requiresAuth: true, role: "pasien" },
    children: [
      {
        path: "dashboard",
        name: "pasien.dashboard",
        component: () => import("@/pages/pasien/Dashboard.vue"),
      },
      {
        path: "antrian/status",
        name: "pasien.antrian.status",
        component: () => import("@/pages/pasien/antrian/Status.vue"),
      },
      {
        path: "riwayat",
        name: "pasien.riwayat",
        component: () => import("@/pages/pasien/riwayat/Index.vue"),
      },
      {
        path: "riwayat/:id",
        name: "pasien.riwayat.detail",
        component: () => import("@/pages/pasien/riwayat/Detail.vue"),
      },
      {
        path: "profil",
        name: "pasien.profil",
        component: () => import("@/pages/pasien/profil/Index.vue"),
      },
      {
        path: "profil/edit",
        name: "pasien.profil.edit",
        component: () => import("@/pages/pasien/profil/Edit.vue"),
      },
    ],
  },
];

export default pasienRoutes;
