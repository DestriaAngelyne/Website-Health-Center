<template>
  <div class="reset-page">
    <div class="reset-wrapper">

      <!-- LEFT PANEL -->
      <div class="panel-left">
        <div class="left-content">
          <div class="brand">
            <span class="brand-icon">🏥</span>
            <div>
              <div class="brand-name">Graha Medika</div>
              <div class="brand-sub">Sistem Informasi Rumah Sakit</div>
            </div>
          </div>
          <h1 class="left-title">Buat Password Baru</h1>
          <p class="left-desc">Pastikan password baru kamu kuat dan mudah diingat. Jangan bagikan ke siapapun.</p>
          <div class="left-tips">
            <div class="tip-item">✅ Minimal 8 karakter</div>
            <div class="tip-item">✅ Kombinasi huruf & angka</div>
            <div class="tip-item">✅ Hindari info pribadi</div>
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL -->
      <div class="panel-right">
        <div class="form-card">

          <!-- SUCCESS STATE -->
          <div v-if="state === 'success'" class="success-state">
            <div class="success-icon">✅</div>
            <h2>Password Berhasil Diubah!</h2>
            <p>Kamu sekarang bisa login menggunakan password baru kamu.</p>
            <button class="btn-primary" @click="goToLogin">Ke Halaman Login</button>
          </div>

          <!-- INVALID TOKEN STATE -->
          <div v-else-if="state === 'invalid'" class="error-state">
            <div class="error-icon">⚠️</div>
            <h2>Link Tidak Valid</h2>
            <p>Link reset password ini sudah kadaluarsa atau tidak valid. Silakan minta link baru.</p>
            <button class="btn-outline" @click="goToForgot">Minta Link Baru</button>
          </div>

          <!-- FORM STATE -->
          <div v-else>
            <div class="form-header">
              <div class="form-icon">🔑</div>
              <h2>Buat Password Baru</h2>
              <p>Masukkan password baru kamu di bawah ini.</p>
            </div>

            <div class="alert-error" v-if="errorMsg">{{ errorMsg }}</div>

            <div class="form-group">
              <label>Password Baru</label>
              <div class="input-wrap">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  v-model="form.password"
                  placeholder="Minimal 8 karakter"
                  :class="{ 'input-error': errors.password }"
                  @input="errors.password = ''"
                />
                <button class="toggle-pw" @click="showPassword = !showPassword" type="button">
                  {{ showPassword ? '🙈' : '👁️' }}
                </button>
              </div>
              <span class="field-error" v-if="errors.password">{{ errors.password }}</span>
            </div>

            <div class="form-group">
              <label>Konfirmasi Password</label>
              <div class="input-wrap">
                <input
                  :type="showConfirm ? 'text' : 'password'"
                  v-model="form.password_confirmation"
                  placeholder="Ulangi password baru"
                  :class="{ 'input-error': errors.password_confirmation }"
                  @input="errors.password_confirmation = ''"
                />
                <button class="toggle-pw" @click="showConfirm = !showConfirm" type="button">
                  {{ showConfirm ? '🙈' : '👁️' }}
                </button>
              </div>
              <span class="field-error" v-if="errors.password_confirmation">{{ errors.password_confirmation }}</span>
            </div>

            <!-- Strength indicator -->
            <div class="strength-bar" v-if="form.password">
              <div class="strength-track">
                <div class="strength-fill" :style="{ width: strengthPercent + '%', background: strengthColor }"></div>
              </div>
              <span :style="{ color: strengthColor }">{{ strengthLabel }}</span>
            </div>

            <button class="btn-primary" :disabled="loading" @click="handleSubmit">
              <span v-if="loading">⏳ Menyimpan...</span>
              <span v-else>Simpan Password Baru</span>
            </button>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route  = useRoute()

const state   = ref('form') // 'form' | 'success' | 'invalid'
const loading = ref(false)
const errorMsg = ref('')
const showPassword = ref(false)
const showConfirm  = ref(false)

const form = ref({
  password: '',
  password_confirmation: '',
})

const errors = ref({
  password: '',
  password_confirmation: '',
})

// Ambil token & email dari URL query param
const token = ref('')
const email = ref('')

onMounted(() => {
  token.value = route.query.token || ''
  email.value = route.query.email || ''

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

// Validasi
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

async function handleSubmit() {
  if (!validate()) return

  loading.value  = true
  errorMsg.value = ''

  try {
    await axios.post('/api/auth/new-password', {
      email:                 email.value,
      token:                 token.value,
      password:              form.value.password,
      password_confirmation: form.value.password_confirmation,
    })

    state.value = 'success'
  } catch (err) {
    const msg = err.response?.data?.message || 'Terjadi kesalahan.'
    if (msg.toLowerCase().includes('token') || msg.toLowerCase().includes('kadaluarsa')) {
      state.value = 'invalid'
    } else {
      errorMsg.value = msg
    }
  } finally {
    loading.value = false
  }
}

function goToLogin()  { router.push('/login') }
function goToForgot() { router.push('/forgot-password') }
</script>

<style scoped>
.reset-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #e0e7ff 0%, #f0f9ff 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 16px;
}

.reset-wrapper {
  display: flex;
  width: 100%;
  max-width: 960px;
  min-height: 560px;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.12);
}

/* LEFT */
.panel-left {
  flex: 1;
  background: linear-gradient(160deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
  padding: 48px 40px;
  display: flex;
  align-items: center;
  color: white;
}
.left-content { width: 100%; }
.brand { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; }
.brand-icon { font-size: 2rem; }
.brand-name { font-size: 1.2rem; font-weight: 800; }
.brand-sub  { font-size: 0.75rem; opacity: 0.7; margin-top: 2px; }
.left-title { font-size: 1.8rem; font-weight: 800; line-height: 1.3; margin-bottom: 16px; }
.left-desc  { font-size: 0.9rem; opacity: 0.8; line-height: 1.7; margin-bottom: 32px; }
.left-tips  { display: flex; flex-direction: column; gap: 10px; }
.tip-item   { font-size: 0.85rem; opacity: 0.9; }

/* RIGHT */
.panel-right {
  flex: 1;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 40px;
}
.form-card { width: 100%; max-width: 380px; }

/* Header */
.form-header { text-align: center; margin-bottom: 28px; }
.form-icon   { font-size: 2.5rem; margin-bottom: 12px; }
.form-header h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
.form-header p  { font-size: 0.875rem; color: #64748b; }

/* Form */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 8px; }

.input-wrap { position: relative; }
.input-wrap input {
  width: 100%; padding: 12px 44px 12px 14px;
  border: 1.5px solid #e2e8f0; border-radius: 10px;
  font-size: 0.95rem; color: #0f172a;
  outline: none; transition: border 0.2s;
  box-sizing: border-box;
}
.input-wrap input:focus  { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
.input-wrap input.input-error { border-color: #ef4444; }
.toggle-pw {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; font-size: 1rem;
}

.field-error { font-size: 0.78rem; color: #ef4444; margin-top: 5px; display: block; }
.alert-error {
  background: #fef2f2; border: 1px solid #fecaca;
  color: #dc2626; padding: 10px 14px; border-radius: 8px;
  font-size: 0.85rem; margin-bottom: 16px;
}

/* Strength */
.strength-bar  { margin-bottom: 20px; }
.strength-track { height: 6px; background: #e2e8f0; border-radius: 99px; margin-bottom: 6px; }
.strength-fill  { height: 100%; border-radius: 99px; transition: all 0.3s; }
.strength-bar span { font-size: 0.78rem; font-weight: 600; }

/* Buttons */
.btn-primary {
  width: 100%; padding: 13px;
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: white; border: none; border-radius: 10px;
  font-size: 0.95rem; font-weight: 700; cursor: pointer;
  transition: opacity 0.2s; margin-top: 4px;
}
.btn-primary:hover    { opacity: 0.92; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-outline {
  width: 100%; padding: 13px;
  background: white; color: #2563eb;
  border: 2px solid #2563eb; border-radius: 10px;
  font-size: 0.95rem; font-weight: 700; cursor: pointer;
  transition: all 0.2s;
}
.btn-outline:hover { background: #eff6ff; }

/* Success / Error state */
.success-state, .error-state { text-align: center; padding: 20px 0; }
.success-icon, .error-icon   { font-size: 3rem; margin-bottom: 16px; }
.success-state h2, .error-state h2 { font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
.success-state p, .error-state p   { font-size: 0.875rem; color: #64748b; margin-bottom: 24px; line-height: 1.7; }

@media (max-width: 640px) {
  .reset-wrapper { flex-direction: column; }
  .panel-left    { padding: 32px 24px; }
  .panel-right   { padding: 32px 24px; }
}
</style>
