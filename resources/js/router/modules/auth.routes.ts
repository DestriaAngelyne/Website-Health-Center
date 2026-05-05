const authRoutes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('../../pages/auth/Login.vue'),
        meta: { guest: true }, // hanya bisa diakses kalau belum login
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('../../pages/auth/ForgotPassword.vue'),
        meta: { guest: true },
    },
]

export default authRoutes
