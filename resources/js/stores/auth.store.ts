import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '../services/auth.service'
import router from '../router'

interface User {
    id: number
    name: string
    email: string
    username: string | null
    role: 'admin' | 'loket' | 'perawat' | 'dokter' | 'pasien'
    foto: string | null
    foto_url: string | null   // ← URL lengkap foto profil dari backend
    no_hp: string | null
}

export const useAuthStore = defineStore('auth', () => {
    const user  = ref<User | null>(null)
    const token = ref<string | null>(localStorage.getItem('auth_token'))

    const isLoggedIn = computed(() => !!token.value)
    const isAdmin    = computed(() => user.value?.role === 'admin')
    const isLoket    = computed(() => user.value?.role === 'loket')
    const isPerawat  = computed(() => user.value?.role === 'perawat')
    const isDokter   = computed(() => user.value?.role === 'dokter')
    const isPasien   = computed(() => user.value?.role === 'pasien')

    // Key localStorage per-user agar tidak bentrok antar akun
    const fotoKey = computed(() => `puskesmas_foto_${user.value?.id ?? 'guest'}`)

    // Prioritas: localStorage (hasil crop) → foto_url dari backend → null
    const fotoUrl = computed(() => {
        const local = localStorage.getItem(fotoKey.value)
        if (local) return local
        return user.value?.foto_url ?? null
    })

    async function login(email: string, password: string) {
        const data = await authService.login(email, password)
        token.value = data.token
        user.value  = data.user
        localStorage.setItem('auth_token', data.token)
        router.push(data.redirect)
    }

    async function logout() {
        try {
            await authService.logout()
        } catch (e) {
            // Tetap logout meski API error
        } finally {
            // Hapus foto dari localStorage saat logout
            const key = fotoKey.value
            token.value = null
            user.value  = null
            localStorage.removeItem('auth_token')
            localStorage.removeItem(key)
            router.push('/login')
        }
    }

    async function fetchUser() {
        if (!token.value) return
        try {
            const data = await authService.me()
            user.value = data.user
            // Sengaja TIDAK menimpa localStorage foto —
            // foto hasil crop tetap dipertahankan setelah refresh
        } catch (e: any) {
            if (e.response?.status === 401) {
                token.value = null
                user.value  = null
                localStorage.removeItem('auth_token')
                router.push('/login')
            }
        }
    }

    return {
        user,
        token,
        fotoUrl,
        fotoKey,
        isLoggedIn,
        isAdmin,
        isLoket,
        isPerawat,
        isDokter,
        isPasien,
        login,
        logout,
        fetchUser,
    }
})
