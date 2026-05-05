<template>
  <div class="reset-page">

    <!-- STATE: FORM -->
    <transition name="fade-slide">
      <div v-if="state === 'form'" class="reset-wrapper">

        <!-- LEFT PANEL -->
        <div class="panel-left">
          <div class="left-bg-circles">
            <span class="circle c1"></span>
            <span class="circle c2"></span>
            <span class="circle c3"></span>
          </div>
          <div class="left-content">
            <div class="brand-box">
              <img :src="logoGrahaMedika" alt="Graha Medika" class="brand-logo" />
              <span class="brand-name">Graha Medika</span>
            </div>
            <div class="illustration-card">
              <div class="mockup-card">
                <div class="mockup-header">
                  <span class="dot red"></span>
                  <span class="dot yellow"></span>
                  <span class="dot green"></span>
                </div>
                <div class="mockup-row"><span class="mock-label">Password Baru</span></div>
                <div class="mockup-row"><span class="mock-label">Keamanan</span><span class="mock-value">✓</span></div>
                <div class="mockup-bar"><div class="bar-fill" style="width: 92%"></div></div>
                <div class="mockup-row small"><span class="mock-label">Proteksi</span><span class="mock-value">92%</span></div>
              </div>
            </div>
            <div class="left-text">
              <h2>Buat Password<br/>Baru Kamu!</h2>
              <p>Pastikan password baru kamu kuat dan tidak pernah digunakan sebelumnya.</p>
            </div>
          </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="panel-right">
          <div class="form-container">

            <button class="back-btn" @click="$router.push('/login')">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
              </svg>
              Kembali ke Login
            </button>

            <div class="form-icon">
              <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                <path d="M18 4C10.268 4 4 10.268 4 18s6.268 14 14 14 14-6.268 14-14S25.732 4 18 4z" fill="#3B6BF5" opacity="0.12"/>
                <path d="M13 17V13a5 5 0 0110 0v4M9 17h18v12H9z" stroke="#3B6BF5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="18" cy="23" r="2" fill="#3B6BF5"/>
              </svg>
            </div>

            <h1 class="form-title">Buat Password Baru</h1>
            <p class="form-subtitle">Masukkan password baru kamu di bawah ini. Minimal 8 karakter.</p>

            <div class="alert-error" v-if="errorMsg">{{ errorMsg }}</div>

            <!-- Password Baru -->
            <div class="input-group" :class="{ focused: focusedField === 'password', error: errors.password }">
              <label>Password Baru</label>
              <div class="input-wrap">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Minimal 8 karakter"
                  @focus="focusedField = 'password'"
                  @blur="focusedField = ''"
                  @input="errors.password = ''"
                />
                <button class="toggle-pw" @click="showPassword = !showPassword" type="button">
                  <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
              <span class="err-msg" v-if="errors.password">{{ errors.password }}</span>
            </div>

            <!-- Strength indicator -->
            <div class="strength-wrap" v-if="form.password">
              <div class="strength-track">
                <div class="strength-fill" :style="{ width: strengthPercent + '%', background: strengthColor }"></div>
              </div>
              <span class="strength-label" :style="{ color: strengthColor }">{{ strengthLabel }}</span>
            </div>

            <!-- Konfirmasi Password -->
            <div class="input-group" :class="{ focused: focusedField === 'confirm', error: errors.password_confirmation }">
              <label>Konfirmasi Password</label>
              <div class="input-wrap">
                <input
                  v-model="form.password_confirmation"
                  :type="showConfirm ? 'text' : 'password'"
                  placeholder="Ulangi password baru"
                  @focus="focusedField = 'confirm'"
                  @blur="focusedField = ''"
                  @input="errors.password_confirmation = ''"
                  @keyup.enter="handleSubmit"
                />
                <button class="toggle-pw" @click="showConfirm = !showConfirm" type="button">
                  <svg v-if="!showConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
              <span class="err-msg" v-if="errors.password_confirmation">{{ errors.password_confirmation }}</span>
            </div>

            <button class="btn-submit" @click="handleSubmit" :disabled="isLoading">
              <svg v-if="isLoading" class="spin-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12a9 9 0 11-6.219-8.56"/>
              </svg>
              <span>{{ isLoading ? 'Menyimpan...' : 'Simpan Password Baru' }}</span>
            </button>

          </div>
        </div>
      </div>
    </transition>

    <!-- STATE: SUCCESS -->
    <transition name="fade">
      <div v-if="state === 'success'" class="fullscreen-state blue-bg">
        <div class="confetti-container">
          <span v-for="i in 20" :key="i" class="confetti-piece" :style="getConfettiStyle(i)"></span>
        </div>
        <div class="state-content">
          <div class="check-circle pop-in">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
              <polyline points="20,6 9,17 4,12" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="check-stroke"/>
            </svg>
          </div>
          <p class="state-title">Password Berhasil Diubah!</p>
          <p class="state-text">Kamu sekarang bisa login menggunakan password baru kamu.</p>
          <button class="btn-back-login" @click="$router.push('/login')">Ke Halaman Login</button>
        </div>
      </div>
    </transition>

    <!-- STATE: INVALID TOKEN -->
    <transition name="fade">
      <div v-if="state === 'invalid'" class="fullscreen-state dark-bg">
        <div class="state-content">
          <div class="warning-circle pop-in">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
              <path d="M12 9v4M12 17h.01" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round"/>
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="#f59e0b" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </div>
          <p class="state-title">Link Tidak Valid</p>
          <p class="state-text">Link reset password ini sudah kadaluarsa atau tidak valid. Silakan minta link baru.</p>
          <button class="btn-back-login" @click="$router.push('/forgot-password')">Minta Link Baru</button>
        </div>
      </div>
    </transition>

  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

export default defineComponent({
  name: 'ResetPasswordPage',
  setup() {
    const router = useRouter()
    const route  = useRoute()

    const state        = ref<'form' | 'success' | 'invalid'>('form')
    const isLoading    = ref(false)
    const errorMsg     = ref('')
    const focusedField = ref('')
    const showPassword = ref(false)
    const showConfirm  = ref(false)
    const logoGrahaMedika = '/media/logos/custom-1.png'

    const form = ref({ password: '', password_confirmation: '' })
    const errors = ref({ password: '', password_confirmation: '' })

    const token = ref('')
    const email = ref('')

    onMounted(() => {
      token.value = (route.query.token as string) || ''
      email.value = (route.query.email as string) || ''
      if (!token.value || !email.value) {
        state.value = 'invalid'
      }
    })

    // Password strength
    const strengthPercent = computed(() => {
      const pw = form.value.password
      if (!pw) return 0
      let score = 0
      if (pw.length >= 8)  score += 25
      if (pw.length >= 12) score += 25
      if (/[A-Z]/.test(pw)) score += 25
      if (/[0-9!@#$%^&*]/.test(pw)) score += 25
      return score
    })

    const strengthColor = computed(() => {
      if (strengthPercent.value <= 25) return '#ef4444'
      if (strengthPercent.value <= 50) return '#f97316'
      if (strengthPercent.value <= 75) return '#eab308'
      return '#22c55e'
    })

    const strengthLabel = computed(() => {
      if (strengthPercent.value <= 25) return 'Lemah'
      if (strengthPercent.value <= 50) return 'Sedang'
      if (strengthPercent.value <= 75) return 'Kuat'
      return 'Sangat Kuat'
    })

    function validate() {
      let valid = true
      errors.value = { password: '', password_confirmation: '' }

      if (!form.value.password) {
        errors.value.password = 'Password wajib diisi.'
        valid = false
      } else if (form.value.password.length < 8) {
        errors.value.password = 'Password minimal 8 karakter.'
        valid = false
      }

      if (!form.value.password_confirmation) {
        errors.value.password_confirmation = 'Konfirmasi password wajib diisi.'
        valid = false
      } else if (form.value.password !== form.value.password_confirmation) {
        errors.value.password_confirmation = 'Password tidak cocok.'
        valid = false
      }

      return valid
    }

    const api = axios.create({
      baseURL: window.location.origin,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    })

    async function handleSubmit() {
      if (!validate()) return

      isLoading.value = true
      errorMsg.value  = ''

      try {
        await api.post('/api/auth/new-password', {
          email:                 email.value,
          token:                 token.value,
          password:              form.value.password,
          password_confirmation: form.value.password_confirmation,
        })
        state.value = 'success'
      } catch (err: any) {
        const msg = err?.response?.data?.message || 'Terjadi kesalahan.'
        if (msg.toLowerCase().includes('token') || msg.toLowerCase().includes('kadaluarsa')) {
          state.value = 'invalid'
        } else {
          errorMsg.value = msg
        }
      } finally {
        isLoading.value = false
      }
    }

    function getConfettiStyle(i: number) {
      const colors = ['#f59e0b', '#ef4444', '#22c55e', '#8b5cf6', '#3b82f6', '#ec4899']
      const size = 6 + Math.floor(Math.random() * 8)
      return {
        left: `${Math.random() * 100}%`,
        animationDelay: `${(i * 0.08).toFixed(2)}s`,
        width: `${size}px`,
        height: `${size}px`,
        background: colors[i % colors.length],
        borderRadius: Math.random() > 0.5 ? '50%' : '2px',
        transform: `rotate(${Math.random() * 360}deg)`,
      }
    }

    return {
      state, isLoading, errorMsg, focusedField,
      showPassword, showConfirm, logoGrahaMedika,
      form, errors, strengthPercent, strengthColor, strengthLabel,
      handleSubmit, getConfettiStyle,
    }
  },
})
</script>

<style scoped>
.reset-page {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f4ff;
  font-family: 'Plus Jakarta Sans', 'Nunito', sans-serif;
  overflow: hidden;
}
.reset-wrapper { display: flex; width: 100%; min-height: 100vh; }

/* LEFT PANEL — identik dengan ForgotPassword */
.panel-left {
  flex: 1;
  background: linear-gradient(145deg, #2563eb 0%, #1d4ed8 60%, #1e3a8a 100%);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  min-height: 100vh;
}
.left-bg-circles .circle { position: absolute; border-radius: 50%; opacity: 0.08; }
.c1 { width: 400px; height: 400px; background: white; top: -100px; left: -100px; }
.c2 { width: 250px; height: 250px; background: white; bottom: 40px; right: -80px; }
.c3 { width: 150px; height: 150px; background: #93c5fd; top: 55%; left: 60%; }
.left-content { position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; padding: 48px 40px; gap: 28px; }
.brand-box { display: flex; align-items: center; gap: 12px; }
.brand-logo { width: 40px; height: 40px; object-fit: contain; }
.brand-name { color: white; font-size: 1.1rem; font-weight: 700; }
.illustration-card { animation: float 3.5s ease-in-out infinite; }
@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
.mockup-card { background: rgba(255,255,255,0.15); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.25); border-radius: 16px; padding: 20px 24px; width: 260px; color: white; }
.mockup-header { display: flex; gap: 6px; margin-bottom: 14px; }
.dot { width: 10px; height: 10px; border-radius: 50%; }
.dot.red { background: #f87171; } .dot.yellow { background: #fbbf24; } .dot.green { background: #4ade80; }
.mockup-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 0.85rem; }
.mockup-row.small { font-size: 0.75rem; opacity: 0.75; }
.mock-label { opacity: 0.8; } .mock-value { font-weight: 700; }
.mockup-bar { background: rgba(255,255,255,0.2); border-radius: 99px; height: 6px; margin-bottom: 6px; overflow: hidden; }
.bar-fill { background: #4ade80; height: 100%; border-radius: 99px; animation: grow 1.5s ease forwards; }
@keyframes grow { from { width: 0% } }
.left-text { text-align: center; color: white; }
.left-text h2 { font-size: 1.5rem; font-weight: 800; line-height: 1.3; margin-bottom: 8px; }
.left-text p { font-size: 0.875rem; opacity: 0.75; line-height: 1.6; max-width: 280px; }

/* RIGHT PANEL */
.panel-right { flex: 1; display: flex; align-items: center; justify-content: center; background: white; padding: 48px 40px; }
.form-container { width: 100%; max-width: 400px; }

.back-btn {
  display: inline-flex; align-items: center; gap: 6px;
  background: none; border: none; color: #64748b;
  font-size: 0.82rem; font-weight: 600; cursor: pointer;
  padding: 0; margin-bottom: 28px; font-family: inherit; transition: color 0.2s;
}
.back-btn:hover { color: #3b6bf5; }

.form-icon { width: 60px; height: 60px; background: #eff6ff; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
.form-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
.form-subtitle { font-size: 0.875rem; color: #64748b; margin: 0 0 24px; line-height: 1.6; }

.alert-error {
  background: #fef2f2; border: 1px solid #fecaca;
  color: #dc2626; padding: 10px 14px; border-radius: 8px;
  font-size: 0.85rem; margin-bottom: 16px;
}

.input-group { margin-bottom: 16px; }
.input-group label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
.input-wrap { position: relative; }
.input-wrap input {
  width: 100%; padding: 12px 42px 12px 14px;
  border: 1.5px solid #e2e8f0; border-radius: 10px;
  font-size: 0.9rem; color: #0f172a; background: #f8fafc;
  outline: none; transition: all 0.2s; box-sizing: border-box; font-family: inherit;
}
.input-group.focused .input-wrap input { border-color: #3b6bf5; background: white; box-shadow: 0 0 0 3px rgba(59,107,245,0.1); }
.input-group.error .input-wrap input { border-color: #ef4444; }
.toggle-pw {
  position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; color: #94a3b8;
  display: flex; align-items: center; padding: 2px; transition: color 0.2s;
}
.toggle-pw:hover { color: #3b6bf5; }
.err-msg { font-size: 0.75rem; color: #ef4444; margin-top: 4px; display: block; }

/* Strength bar */
.strength-wrap { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; margin-top: -6px; }
.strength-track { flex: 1; height: 5px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
.strength-fill { height: 100%; border-radius: 99px; transition: all 0.3s; }
.strength-label { font-size: 0.75rem; font-weight: 700; min-width: 70px; text-align: right; }

.btn-submit {
  width: 100%; padding: 13px; margin-top: 8px;
  background: linear-gradient(135deg, #3b6bf5, #2563eb);
  color: white; border: none; border-radius: 10px;
  font-size: 0.95rem; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  box-shadow: 0 4px 14px rgba(59,107,245,0.35); transition: all 0.2s; font-family: inherit;
}
.btn-submit:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(59,107,245,0.45); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }
.spin-icon { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Fullscreen states */
.fullscreen-state { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; z-index: 50; }
.blue-bg { background: linear-gradient(145deg, #2563eb 0%, #1d4ed8 60%, #1e3a8a 100%); }
.dark-bg { background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%); }
.state-content { display: flex; flex-direction: column; align-items: center; gap: 16px; text-align: center; padding: 0 24px; }
.state-title { color: white; font-size: 1.4rem; font-weight: 800; margin: 0; }
.state-text { color: rgba(255,255,255,0.75); font-size: 0.9rem; margin: 0; max-width: 280px; line-height: 1.6; }

.check-circle, .warning-circle { width: 88px; height: 88px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.check-circle.pop-in, .warning-circle.pop-in { animation: pop-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards, pulse-ring 1s 0.5s ease-out forwards; box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
@keyframes pop-in { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
@keyframes pulse-ring { 0% { box-shadow: 0 0 0 0 rgba(255,255,255,0.5); } 100% { box-shadow: 0 0 0 30px rgba(255,255,255,0); } }
.check-stroke { stroke-dasharray: 40; stroke-dashoffset: 40; animation: draw-check 0.4s 0.3s ease forwards; }
@keyframes draw-check { to { stroke-dashoffset: 0; } }

.btn-back-login { margin-top: 8px; padding: 12px 28px; background: white; color: #2563eb; border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: all 0.2s; }
.btn-back-login:hover { transform: translateY(-1px); }

.confetti-container { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
.confetti-piece { position: absolute; top: -20px; animation: fall 1.5s ease-in forwards; }
@keyframes fall { 0% { top: -20px; opacity: 1; } 100% { top: 110%; opacity: 0; transform: rotate(540deg); } }

.fade-slide-enter-active { animation: slideInRight 0.5s ease; }
.fade-slide-leave-active { animation: slideOutLeft 0.4s ease; }
@keyframes slideInRight { from { opacity: 0; transform: translateX(30px); } }
@keyframes slideOutLeft { to { opacity: 0; transform: translateX(-30px); } }
.fade-enter-active { animation: fadeIn 0.4s ease; }
.fade-leave-active { animation: fadeOut 0.3s ease; }
@keyframes fadeIn { from { opacity: 0; } }
@keyframes fadeOut { to { opacity: 0; } }

@media (max-width: 768px) {
  .panel-left { display: none; }
  .panel-right { padding: 32px 20px; }
}
</style>
