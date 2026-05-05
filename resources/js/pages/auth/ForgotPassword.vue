<template>
  <div class="forgot-page">

    <!-- STATE: FORM (input email) -->
    <transition name="fade-slide">
      <div v-if="state === 'form'" class="forgot-wrapper">

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
                <div class="mockup-row"><span class="mock-label">Keamanan Akun</span></div>
                <div class="mockup-row"><span class="mock-label">Reset Password</span><span class="mock-value">✓</span></div>
                <div class="mockup-bar"><div class="bar-fill" style="width: 85%"></div></div>
                <div class="mockup-row small"><span class="mock-label">Proteksi</span><span class="mock-value">85%</span></div>
              </div>
            </div>
            <div class="left-text">
              <h2>Lupa Password?<br/>Tenang Saja!</h2>
              <p>Kami akan kirimkan kode OTP ke email kamu dalam hitungan detik.</p>
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
                <path d="M12 16v-4a6 6 0 1112 0v4M8 16h20v14H8z" stroke="#3B6BF5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="18" cy="23" r="2" fill="#3B6BF5"/>
              </svg>
            </div>

            <h1 class="form-title">Reset Password</h1>
            <p class="form-subtitle">Masukkan email akunmu, kami akan kirim kode OTP untuk reset password.</p>

            <div class="input-group" :class="{ focused: isFocused, error: errorMsg }">
              <label>Email</label>
              <div class="input-wrap">
                <input
                  v-model="email"
                  type="email"
                  placeholder="nama@grahmedika.com"
                  @focus="isFocused = true"
                  @blur="isFocused = false"
                  @input="errorMsg = ''"
                  @keyup.enter="handleSendOtp"
                />
                <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="2" y="4" width="20" height="16" rx="3"/><path d="m2 7 10 7 10-7"/>
                </svg>
              </div>
              <span class="err-msg" v-if="errorMsg">{{ errorMsg }}</span>
            </div>

            <button class="btn-submit" @click="handleSendOtp" :disabled="isLoading">
              <svg v-if="isLoading" class="spin-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12a9 9 0 11-6.219-8.56"/>
              </svg>
              <span>{{ isLoading ? 'Mengirim...' : 'Kirim Kode OTP' }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- STATE: OTP (input kode OTP) -->
    <transition name="fade-slide">
      <div v-if="state === 'otp'" class="forgot-wrapper">

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
            <div class="left-text" style="margin-top: 40px;">
              <h2>Cek Email<br/>Kamu!</h2>
              <p>Kode OTP 6 digit sudah dikirim ke email kamu. Berlaku selama 10 menit.</p>
            </div>
          </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="panel-right">
          <div class="form-container">
            <button class="back-btn" @click="state = 'form'">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
              </svg>
              Ganti Email
            </button>

            <div class="form-icon">
              <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                <path d="M18 4C10.268 4 4 10.268 4 18s6.268 14 14 14 14-6.268 14-14S25.732 4 18 4z" fill="#3B6BF5" opacity="0.12"/>
                <rect x="8" y="10" width="20" height="16" rx="2" stroke="#3B6BF5" stroke-width="2"/>
                <path d="M8 14l10 6 10-6" stroke="#3B6BF5" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>

            <h1 class="form-title">Masukkan Kode OTP</h1>
            <p class="form-subtitle">Kode OTP dikirim ke <strong>{{ email }}</strong></p>

            <!-- 6 Kotak OTP -->
            <div class="otp-inputs">
              <input
                v-for="(_, i) in otpDigits"
                :key="i"
                :ref="el => { if (el) otpRefs[i] = el as HTMLInputElement }"
                v-model="otpDigits[i]"
                type="text"
                maxlength="1"
                inputmode="numeric"
                class="otp-box"
                :class="{ error: otpError }"
                @input="handleOtpInput(i)"
                @keydown="handleOtpKeydown($event, i)"
                @paste="handleOtpPaste"
              />
            </div>

            <span class="err-msg" v-if="otpError" style="display:block; margin-bottom: 12px;">{{ otpError }}</span>

            <button class="btn-submit" @click="handleVerifyOtp" :disabled="isLoading || otpDigits.join('').length < 6">
              <svg v-if="isLoading" class="spin-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12a9 9 0 11-6.219-8.56"/>
              </svg>
              <span>{{ isLoading ? 'Memverifikasi...' : 'Verifikasi OTP' }}</span>
            </button>

            <div class="resend-wrap">
              <span class="resend-text">Tidak menerima kode?</span>
              <button class="resend-btn" @click="handleResendOtp" :disabled="resendCooldown > 0 || isLoading">
                {{ resendCooldown > 0 ? `Kirim ulang (${resendCooldown}s)` : 'Kirim ulang' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- STATE: RESET (input password baru) -->
    <transition name="fade-slide">
      <div v-if="state === 'reset'" class="forgot-wrapper">

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
            <div class="left-text" style="margin-top: 40px;">
              <h2>Buat Password<br/>Baru!</h2>
              <p>Pastikan password baru kamu kuat dan mudah diingat.</p>
            </div>
          </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="panel-right">
          <div class="form-container">
            <button class="back-btn" @click="state = 'otp'">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
              </svg>
              Kembali
            </button>

            <div class="form-icon">
              <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                <path d="M18 4C10.268 4 4 10.268 4 18s6.268 14 14 14 14-6.268 14-14S25.732 4 18 4z" fill="#3B6BF5" opacity="0.12"/>
                <path d="M12 16v-4a6 6 0 1112 0v4M8 16h20v14H8z" stroke="#3B6BF5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="18" cy="23" r="2" fill="#3B6BF5"/>
              </svg>
            </div>

            <h1 class="form-title">Password Baru</h1>
            <p class="form-subtitle">Masukkan password baru untuk akun kamu.</p>

            <div class="input-group" :class="{ error: resetError }">
              <label>Password Baru</label>
              <div class="input-wrap">
                <input
                  v-model="newPassword"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Minimal 8 karakter"
                  @input="resetError = ''"
                  @keyup.enter="handleNewPassword"
                />
                <button type="button" class="toggle-pw" @click="showPassword = !showPassword">
                  <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="input-group" :class="{ error: resetError }">
              <label>Konfirmasi Password</label>
              <div class="input-wrap">
                <input
                  v-model="confirmPassword"
                  :type="showConfirm ? 'text' : 'password'"
                  placeholder="Ulangi password baru"
                  @input="resetError = ''"
                  @keyup.enter="handleNewPassword"
                />
                <button type="button" class="toggle-pw" @click="showConfirm = !showConfirm">
                  <svg v-if="!showConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
              <span class="err-msg" v-if="resetError">{{ resetError }}</span>
            </div>

            <button class="btn-submit" @click="handleNewPassword" :disabled="isLoading">
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
          <p class="state-text">Silakan login menggunakan password baru kamu.</p>
          <button class="btn-back-login" @click="$router.push('/login')">Kembali ke Login</button>
        </div>
      </div>
    </transition>

  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onUnmounted } from 'vue'
import axios from 'axios'

export default defineComponent({
  name: 'ForgotPasswordPage',
  setup() {
    const state = ref<'form' | 'otp' | 'reset' | 'success'>('form')
    const email = ref('')
    const errorMsg = ref('')
    const isFocused = ref(false)
    const isLoading = ref(false)
    const logoGrahaMedika = '/media/logos/custom-1.png'

    // OTP state
    const otpDigits = ref<string[]>(['', '', '', '', '', ''])
    const otpRefs = ref<HTMLInputElement[]>([])
    const otpError = ref('')
    const resendCooldown = ref(0)
    let cooldownTimer: ReturnType<typeof setInterval> | null = null

    // Reset password state
    const newPassword = ref('')
    const confirmPassword = ref('')
    const resetError = ref('')
    const showPassword = ref(false)
    const showConfirm = ref(false)

    const api = axios.create({
      baseURL: window.location.origin,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    // ── STEP 1: Kirim OTP ─────────────────────────────────────────
    async function handleSendOtp() {
      if (!email.value) { errorMsg.value = 'Email wajib diisi'; return }
      if (!/\S+@\S+\.\S+/.test(email.value)) { errorMsg.value = 'Format email tidak valid'; return }

      isLoading.value = true
      try {
        await api.post('/api/auth/send-otp', { email: email.value })
        otpDigits.value = ['', '', '', '', '', '']
        otpError.value = ''
        state.value = 'otp'
        startCooldown()
      } catch (err: any) {
        errorMsg.value = err?.response?.data?.message || 'Email tidak ditemukan.'
      } finally {
        isLoading.value = false
      }
    }

    // ── STEP 2: Verifikasi OTP ────────────────────────────────────
    async function handleVerifyOtp() {
      const otp = otpDigits.value.join('')
      if (otp.length < 6) { otpError.value = 'Masukkan 6 digit kode OTP'; return }

      isLoading.value = true
      try {
        await api.post('/api/auth/verify-otp', { email: email.value, otp })
        otpError.value = ''
        state.value = 'reset'
      } catch (err: any) {
        otpError.value = err?.response?.data?.message || 'Kode OTP salah atau sudah kadaluarsa.'
      } finally {
        isLoading.value = false
      }
    }

    // ── Kirim ulang OTP ───────────────────────────────────────────
    async function handleResendOtp() {
      isLoading.value = true
      try {
        await api.post('/api/auth/send-otp', { email: email.value })
        otpDigits.value = ['', '', '', '', '', '']
        otpError.value = ''
        startCooldown()
      } catch (err: any) {
        otpError.value = err?.response?.data?.message || 'Gagal mengirim ulang OTP.'
      } finally {
        isLoading.value = false
      }
    }

    // ── STEP 3: Simpan password baru ─────────────────────────────
    async function handleNewPassword() {
      if (!newPassword.value || newPassword.value.length < 8) {
        resetError.value = 'Password minimal 8 karakter'
        return
      }
      if (newPassword.value !== confirmPassword.value) {
        resetError.value = 'Konfirmasi password tidak cocok'
        return
      }

      isLoading.value = true
      try {
        await api.post('/api/auth/new-password', {
          email: email.value,
          otp: otpDigits.value.join(''),
          password: newPassword.value,
          password_confirmation: confirmPassword.value,
        })
        state.value = 'success'
      } catch (err: any) {
        resetError.value = err?.response?.data?.message || 'Gagal menyimpan password. Silakan ulangi dari awal.'
      } finally {
        isLoading.value = false
      }
    }

    // ── OTP input handlers ────────────────────────────────────────
    function handleOtpInput(index: number) {
      const val = otpDigits.value[index]
      // Hanya izinkan angka
      otpDigits.value[index] = val.replace(/\D/g, '').slice(-1)
      otpError.value = ''
      // Auto focus ke kotak berikutnya
      if (otpDigits.value[index] && index < 5) {
        otpRefs.value[index + 1]?.focus()
      }
    }

    function handleOtpKeydown(e: KeyboardEvent, index: number) {
      if (e.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
        otpRefs.value[index - 1]?.focus()
      }
    }

    function handleOtpPaste(e: ClipboardEvent) {
      e.preventDefault()
      const text = e.clipboardData?.getData('text').replace(/\D/g, '').slice(0, 6) || ''
      text.split('').forEach((char, i) => {
        otpDigits.value[i] = char
      })
      const nextEmpty = otpDigits.value.findIndex(d => !d)
      const focusIdx = nextEmpty === -1 ? 5 : nextEmpty
      otpRefs.value[focusIdx]?.focus()
    }

    // ── Cooldown timer untuk kirim ulang ─────────────────────────
    function startCooldown() {
      resendCooldown.value = 60
      if (cooldownTimer) clearInterval(cooldownTimer)
      cooldownTimer = setInterval(() => {
        resendCooldown.value--
        if (resendCooldown.value <= 0) {
          clearInterval(cooldownTimer!)
          cooldownTimer = null
        }
      }, 1000)
    }

    onUnmounted(() => {
      if (cooldownTimer) clearInterval(cooldownTimer)
    })

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
      state, email, errorMsg, isFocused, isLoading, logoGrahaMedika,
      otpDigits, otpRefs, otpError, resendCooldown,
      newPassword, confirmPassword, resetError, showPassword, showConfirm,
      handleSendOtp, handleVerifyOtp, handleResendOtp, handleNewPassword,
      handleOtpInput, handleOtpKeydown, handleOtpPaste,
      getConfettiStyle,
    }
  }
})
</script>

<style scoped>
.forgot-page {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f0f4ff;
  font-family: 'Plus Jakarta Sans', 'Nunito', sans-serif;
  overflow: hidden;
}
.forgot-wrapper { display: flex; width: 100%; min-height: 100vh; }

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
.form-subtitle { font-size: 0.875rem; color: #64748b; margin: 0 0 28px; line-height: 1.6; }

.input-group { margin-bottom: 22px; }
.input-group label { display: block; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
.input-wrap { position: relative; }
.input-wrap input { width: 100%; padding: 12px 42px 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; color: #0f172a; background: #f8fafc; outline: none; transition: all 0.2s; box-sizing: border-box; font-family: inherit; }
.input-group.focused .input-wrap input,
.input-wrap input:focus { border-color: #3b6bf5; background: white; box-shadow: 0 0 0 3px rgba(59,107,245,0.1); }
.input-group.error .input-wrap input { border-color: #ef4444; }
.input-icon { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
.toggle-pw { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0; display: flex; align-items: center; }
.err-msg { font-size: 0.75rem; color: #ef4444; margin-top: 4px; display: block; }

/* OTP Inputs */
.otp-inputs { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; }
.otp-box {
  width: 48px; height: 56px;
  text-align: center; font-size: 1.4rem; font-weight: 700;
  border: 1.5px solid #e2e8f0; border-radius: 10px;
  background: #f8fafc; color: #0f172a; outline: none;
  transition: all 0.2s; font-family: inherit;
}
.otp-box:focus { border-color: #3b6bf5; background: white; box-shadow: 0 0 0 3px rgba(59,107,245,0.1); }
.otp-box.error { border-color: #ef4444; }

/* Resend */
.resend-wrap { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 16px; }
.resend-text { font-size: 0.82rem; color: #64748b; }
.resend-btn { background: none; border: none; color: #3b6bf5; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: inherit; padding: 0; transition: opacity 0.2s; }
.resend-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-submit {
  width: 100%; padding: 13px;
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

.fullscreen-state { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; z-index: 50; }
.blue-bg { background: linear-gradient(145deg, #2563eb 0%, #1d4ed8 60%, #1e3a8a 100%); }
.state-content { display: flex; flex-direction: column; align-items: center; gap: 16px; text-align: center; padding: 0 24px; }
.state-title { color: white; font-size: 1.4rem; font-weight: 800; margin: 0; }
.state-text { color: rgba(255,255,255,0.75); font-size: 0.9rem; margin: 0; max-width: 280px; line-height: 1.6; }
.check-circle { width: 88px; height: 88px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.check-circle.pop-in { animation: pop-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards, pulse-ring 1s 0.5s ease-out forwards; box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
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
  .otp-box { width: 42px; height: 50px; font-size: 1.2rem; }
}
</style>
