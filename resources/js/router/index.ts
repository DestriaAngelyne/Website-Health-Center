import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";
import { useAuthStore } from "@/stores/auth.store";


const routes: Array<RouteRecordRaw> = [

    // ── Landing Page ──────────────────────────────────────────────────────
    {
        path: "/",
        name: "landing",
        component: () => import("@/pages/LandingPage.vue"),
        meta: { pageTitle: "Selamat Datang" },
    },

    // ── Pendaftaran Poli Online (Public) ──────────────────────────────────
    {
        path: "/pendaftaran-poli",
        name: "pendaftaran-poli",
        component: () => import("@/pages/PendaftaranPoli.vue"),
        meta: { pageTitle: "Daftar Poli Online" },
    },

    // ── Status Antrian Publik (bisa diakses siapa saja, termasuk scan QR) ─
    {
        path: "/antrian/status",
        name: "antrian-status-publik",
        component: () => import("@/pages/AntrianPublik.vue"),
        meta: { pageTitle: "Status Antrian" },
    },

    // ── Auth Routes (tanpa layout) ────────────────────────────────────────
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

    // ── Admin Routes ──────────────────────────────────────────────────────
    {
        path: "/admin",
        redirect: "/admin/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: { requiresAuth: true, role: "admin" },
        children: [
            { path: "dashboard",                name: "admin-dashboard",        component: () => import("@/pages/admin/Dashboard.vue"),                     meta: { pageTitle: "Dashboard Admin" } },
            { path: "users",                    name: "admin-users",            component: () => import("@/pages/admin/users/Index.vue"),                   meta: { pageTitle: "Kelola User" } },
            { path: "users/create",             name: "admin-users-create",     component: () => import("@/pages/admin/users/Create.vue"),                  meta: { pageTitle: "Tambah User" } },
            { path: "users/:id/edit",           name: "admin-users-edit",       component: () => import("@/pages/admin/users/Edit.vue"),                    meta: { pageTitle: "Edit User" } },
            { path: "master/poli",              name: "admin-poli",             component: () => import("@/pages/admin/master/poli/Index.vue"),             meta: { pageTitle: "Master Poli" } },
            { path: "master/poli/create",       name: "admin-poli-create",      component: () => import("@/pages/admin/master/poli/Create.vue"),            meta: { pageTitle: "Tambah Poli" } },
            { path: "master/poli/:id/edit",     name: "admin-poli-edit",        component: () => import("@/pages/admin/master/poli/Edit.vue"),              meta: { pageTitle: "Edit Poli" } },
            { path: "master/dokter",            name: "admin-dokter",           component: () => import("@/pages/admin/master/dokter/Index.vue"),           meta: { pageTitle: "Master Dokter" } },
            { path: "master/dokter/create",     name: "admin-dokter-create",    component: () => import("@/pages/admin/master/dokter/Create.vue"),          meta: { pageTitle: "Tambah Dokter" } },
            { path: "master/dokter/:id/edit",   name: "admin-dokter-edit",      component: () => import("@/pages/admin/master/dokter/Edit.vue"),            meta: { pageTitle: "Edit Dokter" } },
            { path: "master/jadwal",            name: "admin-jadwal",           component: () => import("@/pages/admin/master/jadwal/Index.vue"),           meta: { pageTitle: "Jadwal Dokter" } },
            { path: "master/jadwal/create",     name: "admin-jadwal-create",    component: () => import("@/pages/admin/master/jadwal/Create.vue"),          meta: { pageTitle: "Tambah Jadwal" } },
            { path: "master/jadwal/:id/edit",   name: "admin-jadwal-edit",      component: () => import("@/pages/admin/master/jadwal/Edit.vue"),            meta: { pageTitle: "Edit Jadwal" } },
            { path: "master/obat",              name: "admin-obat",             component: () => import("@/pages/admin/master/obat/Index.vue"),             meta: { pageTitle: "Master Obat" } },
            { path: "master/obat/create",       name: "admin-obat-create",      component: () => import("@/pages/admin/master/obat/Create.vue"),            meta: { pageTitle: "Tambah Obat" } },
            { path: "master/obat/:id/edit",     name: "admin-obat-edit",        component: () => import("@/pages/admin/master/obat/Edit.vue"),              meta: { pageTitle: "Edit Obat" } },
            { path: "antrian/monitor",          name: "admin-monitor",          component: () => import("@/pages/admin/antrian/Monitor.vue"),               meta: { pageTitle: "Monitor Antrian" } },
            { path: "laporan/harian",           name: "admin-laporan-harian",   component: () => import("@/pages/admin/laporan/Harian.vue"),                meta: { pageTitle: "Laporan Harian" } },
            { path: "laporan/bulanan",          name: "admin-laporan-bulanan",  component: () => import("@/pages/admin/laporan/Bulanan.vue"),               meta: { pageTitle: "Laporan Bulanan" } },
            { path: "profil/edit",              name: "admin-profil-edit",      component: () => import("@/pages/admin/profil/Edit.vue"),                   meta: { pageTitle: "Edit Profil" } },
        ],
    },

    // ── Loket Routes ──────────────────────────────────────────────────────
    {
        path: "/loket",
        redirect: "/loket/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: { requiresAuth: true, role: "loket" },
        children: [
            { path: "dashboard",        name: "loket-dashboard",   component: () => import("@/pages/loket/Dashboard.vue"),              meta: { pageTitle: "Dashboard Loket" } },
            { path: "pendaftaran",      name: "loket-pendaftaran", component: () => import("@/pages/loket/pendaftaran/Index.vue"),      meta: { pageTitle: "Pendaftaran" } },
            { path: "pendaftaran/baru", name: "loket-pasien-baru", component: () => import("@/pages/loket/pendaftaran/PasienBaru.vue"), meta: { pageTitle: "Pasien Baru" } },
            { path: "pendaftaran/lama", name: "loket-pasien-lama", component: () => import("@/pages/loket/pendaftaran/PasienLama.vue"), meta: { pageTitle: "Pasien Lama" } },
            { path: "antrian",          name: "loket-antrian",     component: () => import("@/pages/loket/antrian/Index.vue"),          meta: { pageTitle: "Antrian Hari Ini" } },
            { path: "antrian/tiket",    name: "loket-tiket",       component: () => import("@/pages/loket/antrian/Tiket.vue"),          meta: { pageTitle: "Cetak Tiket" } },
            { path: "pasien",           name: "loket-pasien",      component: () => import("@/pages/loket/pasien/Index.vue"),           meta: { pageTitle: "Data Pasien" } },
            { path: "profil/edit",      name: "loket-profil-edit", component: () => import("@/pages/loket/profil/Edit.vue"),            meta: { pageTitle: "Edit Profil" } },
        ],
    },

    // ── Perawat Routes ────────────────────────────────────────────────────
    {
        path: "/perawat",
        redirect: "/perawat/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: { requiresAuth: true, role: "perawat" },
        children: [
            { path: "dashboard",       name: "perawat-dashboard",   component: () => import("@/pages/perawat/Dashboard.vue"),           meta: { pageTitle: "Dashboard Perawat" } },
            { path: "antrian",         name: "perawat-antrian",     component: () => import("@/pages/perawat/antrian/Index.vue"),       meta: { pageTitle: "Antrian Poli" } },
            { path: "antrian/panggil/:id?", name: "perawat-panggil",     component: () => import("@/pages/perawat/antrian/Panggil.vue"),     meta: { pageTitle: "Panggil Pasien" } },
            { path: "skrining/:id",    name: "perawat-skrining",    component: () => import("@/pages/perawat/skrining/InputVital.vue"), meta: { pageTitle: "Input Tanda Vital" } },
            { path: "profil/edit",     name: "perawat-profil-edit", component: () => import("@/pages/perawat/profil/Edit.vue"),         meta: { pageTitle: "Edit Profil" } },
        ],
    },

    // ── Dokter Routes ─────────────────────────────────────────────────────
    {
        path: "/dokter",
        redirect: "/dokter/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: { requiresAuth: true, role: "dokter" },
        children: [
            { path: "dashboard",               name: "dokter-dashboard",   component: () => import("@/pages/dokter/Dashboard.vue"),           meta: { pageTitle: "Dashboard Dokter" } },
            { path: "antrian",                 name: "dokter-antrian",     component: () => import("@/pages/dokter/antrian/Index.vue"),       meta: { pageTitle: "Pasien Hari Ini" } },
            { path: "rekam-medis/:id",         name: "dokter-rekam",       component: () => import("@/pages/dokter/rekam-medis/Input.vue"),   meta: { pageTitle: "Rekam Medis" } },
            { path: "rekam-medis/:id/resep",   name: "dokter-resep",       component: () => import("@/pages/dokter/rekam-medis/Resep.vue"),   meta: { pageTitle: "Tulis Resep" } },
            { path: "rekam-medis/:id/rujukan", name: "dokter-rujukan",     component: () => import("@/pages/dokter/rekam-medis/Rujukan.vue"), meta: { pageTitle: "Buat Rujukan" } },
            { path: "riwayat",                 name: "dokter-riwayat",     component: () => import("@/pages/dokter/riwayat/Index.vue"),       meta: { pageTitle: "Riwayat Pasien" } },
            { path: "profil/edit",             name: "dokter-profil-edit", component: () => import("@/pages/dokter/profil/Edit.vue"),         meta: { pageTitle: "Edit Profil" } },
        ],
    },

    // ── Pasien Routes ─────────────────────────────────────────────────────
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

    // ── Display Publik ────────────────────────────────────────────────────
    {
        path: "/display",
        name: "display",
        component: () => import("@/pages/display/AntreanDisplay.vue"),
        meta: { pageTitle: "Display Antrian" },
    },

    // ── Error Pages ───────────────────────────────────────────────────────
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

// ── Navigation Guard ──────────────────────────────────────────────────────
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    document.title = `${to.meta.pageTitle ?? 'Puskesmas'} - SI Antrian`;

    const roleRedirect: Record<string, string> = {
        admin   : '/admin/dashboard',
        loket   : '/loket/dashboard',
        perawat : '/perawat/dashboard',
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
