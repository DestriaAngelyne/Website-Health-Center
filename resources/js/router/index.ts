import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";
import { useAuthStore } from "@/stores/auth.store";


const routes: Array<RouteRecordRaw> = [

    // â”€â”€ Landing Page â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    {
        path: "/",
        name: "landing",
        component: () => import("@/pages/LandingPage.vue"),
        meta: { pageTitle: "Selamat Datang" },
    },

    // â”€â”€ Pendaftaran Poli Online (Public) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    {
        path: "/pendaftaran-poli",
        name: "pendaftaran-poli",
        component: () => import("@/pages/PendaftaranPoli.vue"),
        meta: { pageTitle: "Daftar Poli Online" },
    },

    // â”€â”€ Status Antrian Publik (bisa diakses siapa saja, termasuk scan QR) â”€
    {
        path: "/antrian/status",
        name: "antrian-status-publik",
        component: () => import("@/pages/AntrianPublik.vue"),
        meta: { pageTitle: "Status Antrian" },
    },

    // â”€â”€ Auth Routes (tanpa layout) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    {
        path: "/login",
        name: "login",
        component: () => import("@/pages/auth/Login.vue"),
        meta: { guest: true, pageTitle: "Login" },
    },
    {
        path: "/register",
        name: "register",
        component: () => import("@/pages/auth/Register.vue"),
        meta: { guest: true, pageTitle: "Daftar Akun" },
    },
    {
        path: "/forgot-password",
        name: "forgot-password",
        component: () => import("@/pages/auth/ForgotPassword.vue"),
        meta: { guest: true, pageTitle: "Lupa Password" },
    },
    {
       path: "/reset-password",
        name: "reset-password",
        component: () => import("@/pages/auth/ResetPassword.vue"),
        meta: { guest: true, pageTitle: "Reset Password" },
    },

    // â”€â”€ Superadmin Routes (mengurus sistem) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    {
        path: "/superadmin",
        redirect: "/superadmin/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: { requiresAuth: true, role: "superadmin" },
        children: [
            { path: "dashboard",                name: "superadmin-dashboard",        component: () => import("@/pages/superadmin/Dashboard.vue"),                     meta: { pageTitle: "Dashboard Superadmin" } },
            { path: "users",                    name: "superadmin-users",            component: () => import("@/pages/superadmin/users/Index.vue"),                   meta: { pageTitle: "Kelola User" } },
            { path: "users/create",             name: "superadmin-users-create",     component: () => import("@/pages/superadmin/users/Create.vue"),                  meta: { pageTitle: "Tambah User" } },
            { path: "users/:id/edit",           name: "superadmin-users-edit",       component: () => import("@/pages/superadmin/users/Edit.vue"),                    meta: { pageTitle: "Edit User" } },
            { path: "master/poli",              name: "superadmin-poli",             component: () => import("@/pages/superadmin/master/poli/Index.vue"),             meta: { pageTitle: "Master Poli" } },
            { path: "master/poli/create",       name: "superadmin-poli-create",      component: () => import("@/pages/superadmin/master/poli/Create.vue"),            meta: { pageTitle: "Tambah Poli" } },
            { path: "master/poli/:id/edit",     name: "superadmin-poli-edit",        component: () => import("@/pages/superadmin/master/poli/Edit.vue"),              meta: { pageTitle: "Edit Poli" } },
            { path: "master/dokter",            name: "superadmin-dokter",           component: () => import("@/pages/superadmin/master/dokter/Index.vue"),           meta: { pageTitle: "Master Dokter" } },
            { path: "master/dokter/create",     name: "superadmin-dokter-create",    component: () => import("@/pages/superadmin/master/dokter/Create.vue"),          meta: { pageTitle: "Tambah Dokter" } },
            { path: "master/dokter/:id/edit",   name: "superadmin-dokter-edit",      component: () => import("@/pages/superadmin/master/dokter/Edit.vue"),            meta: { pageTitle: "Edit Dokter" } },
            { path: "master/jadwal",            name: "superadmin-jadwal",           component: () => import("@/pages/superadmin/master/jadwal/Index.vue"),           meta: { pageTitle: "Jadwal Dokter" } },
            { path: "master/jadwal/create",     name: "superadmin-jadwal-create",    component: () => import("@/pages/superadmin/master/jadwal/Create.vue"),          meta: { pageTitle: "Tambah Jadwal" } },
            { path: "master/jadwal/:id/edit",   name: "superadmin-jadwal-edit",      component: () => import("@/pages/superadmin/master/jadwal/Edit.vue"),            meta: { pageTitle: "Edit Jadwal" } },
            { path: "antrian/monitor",          name: "superadmin-monitor",          component: () => import("@/pages/superadmin/antrian/Monitor.vue"),               meta: { pageTitle: "Monitor Antrian" } },
            { path: "laporan/harian",           name: "superadmin-laporan-harian",   component: () => import("@/pages/superadmin/laporan/Harian.vue"),                meta: { pageTitle: "Laporan Harian" } },
            { path: "laporan/bulanan",          name: "superadmin-laporan-bulanan",  component: () => import("@/pages/superadmin/laporan/Bulanan.vue"),               meta: { pageTitle: "Laporan Bulanan" } },
            { path: "profil/edit",              name: "superadmin-profil-edit",      component: () => import("@/pages/superadmin/profil/Edit.vue"),                   meta: { pageTitle: "Edit Profil" } },
        ],
    },

    // â”€â”€ Admin Routes (mengurus operasional: loket + perawat gabungan) â”€â”€â”€â”€â”€â”€
    {
        path: "/admin",
        redirect: "/admin/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: { requiresAuth: true, role: "admin" },
        children: [
            { path: "dashboard",                name: "admin-dashboard",        component: () => import("@/pages/admin/Dashboard.vue"),                     meta: { pageTitle: "Dashboard Admin Operasional" } },
            { path: "pendaftaran",              name: "admin-pendaftaran",     component: () => import("@/pages/admin/pendaftaran/Index.vue"),              meta: { pageTitle: "Pendaftaran" } },
            { path: "pendaftaran/baru",         name: "admin-pasien-baru",     component: () => import("@/pages/admin/pendaftaran/PasienBaru.vue"),        meta: { pageTitle: "Pasien Baru" } },
            { path: "pendaftaran/lama",         name: "admin-pasien-lama",     component: () => import("@/pages/admin/pendaftaran/PasienLama.vue"),        meta: { pageTitle: "Pasien Lama" } },
            { path: "antrian",                  name: "admin-antrian",         component: () => import("@/pages/admin/antrian/Index.vue"),                  meta: { pageTitle: "Antrian Hari Ini" } },
            { path: "antrian/tiket",            name: "admin-tiket",           component: () => import("@/pages/admin/antrian/Tiket.vue"),                  meta: { pageTitle: "Cetak Tiket" } },
            { path: "antrian/poli",             name: "admin-antrian-poli",    component: () => import("@/pages/admin/antrian-poli/Index.vue"),                meta: { pageTitle: "Antrian Poli" } },
            { path: "antrian/panggil/:id?",     name: "admin-panggil",         component: () => import("@/pages/admin/antrian-poli/Panggil.vue"),                meta: { pageTitle: "Panggil Pasien" } },
            { path: "pasien",                   name: "admin-pasien",          component: () => import("@/pages/admin/pasien/Index.vue"),                  meta: { pageTitle: "Data Pasien" } },
            { path: "profil/edit",              name: "admin-profil-edit",     component: () => import("@/pages/admin/profil/Edit.vue"),                    meta: { pageTitle: "Edit Profil" } },
        ],
    },

    // â”€â”€ Pasien Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    {
        path: "/pasien",
        redirect: "/pasien/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: { requiresAuth: true, role: "pasien" },
        children: [
            { path: "dashboard",      name: "pasien-dashboard",      component: () => import("@/pages/pasien/Dashboard.vue"),          meta: { pageTitle: "Dashboard" } },
            { path: "pendaftaran",    name: "pasien-pendaftaran",    component: () => import("@/pages/PendaftaranPoli.vue"),           meta: { pageTitle: "Daftar Antrian" } },
            { path: "antrian/status", name: "pasien-antrian-status", component: () => import("@/pages/pasien/antrian/Status.vue"),     meta: { pageTitle: "Status Antrian" } },
            { path: "riwayat",        name: "pasien-riwayat",        component: () => import("@/pages/pasien/riwayat/Index.vue"),      meta: { pageTitle: "Riwayat Kunjungan" } },
            { path: "riwayat/:id",    name: "pasien-riwayat-detail", component: () => import("@/pages/pasien/riwayat/Detail.vue"),     meta: { pageTitle: "Detail Kunjungan" } },
            { path: "profil",         name: "pasien-profil",         component: () => import("@/pages/pasien/profil/Index.vue"),       meta: { pageTitle: "Profil Saya" } },
            { path: "profil/edit",    name: "pasien-profil-edit",    component: () => import("@/pages/pasien/profil/Edit.vue"),        meta: { pageTitle: "Edit Profil" } },
        ],
    },

    // â”€â”€ Display Publik â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    {
        path: "/display",
        name: "display",
        component: () => import("@/pages/display/AntreanDisplay.vue"),
        meta: { pageTitle: "Display Antrian" },
    },

    // â”€â”€ Error Pages â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    {
        path: "/404",
        name: "404",
        component: () => import("@/views/crafted/authentication/Error404.vue"),
        meta: { pageTitle: "Halaman Tidak Ditemukan" },
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/404",
    },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
    scrollBehavior(to) {
        if (to.hash) return { el: to.hash, top: 80, behavior: "smooth" };
        return { top: 0, left: 0, behavior: "smooth" };
    },
});

// â”€â”€ Navigation Guard â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    document.title = `${to.meta.pageTitle ?? 'Puskesmas'} - SI Antrian`;

    const roleRedirect: Record<string, string> = {
        superadmin: '/superadmin/dashboard',
        admin   : '/admin/dashboard',
        dokter  : '/dokter/dashboard',
        pasien  : '/pasien/dashboard',
    };

    if (to.meta.requiresAuth) {
        if (!authStore.isLoggedIn) {
            return next({ name: 'login' });
        }

        if (!authStore.user) {
            try {
                await authStore.fetchUser();
            } catch {
                return next({ name: 'login' });
            }
        }

        if (!authStore.user) {
            return next({ name: 'login' });
        }

        if (to.meta.role && authStore.user.role !== to.meta.role) {
            return next(roleRedirect[authStore.user.role] ?? '/login');
        }

        return next();
    }

    if (to.meta.guest && authStore.isLoggedIn) {
        if (!authStore.user) {
            try {
                await authStore.fetchUser();
            } catch {
                return next();
            }
        }

        if (authStore.user) {
            return next(roleRedirect[authStore.user.role] ?? '/');
        }
    }

    next();
});

export default router;

