<template>
  <div class="login-page">
    <!-- STATE: FORM -->
    <transition name="fade-slide">
      <div v-if="state === 'form'" class="login-wrapper">
        <!-- LEFT PANEL -->
        <div class="panel-left">
          <!-- Foto dokter sebagai background -->
          <div class="left-photo-bg"></div>
          <!-- Overlay gradient gelap -->
          <div class="left-overlay"></div>

          <!-- Konten di atas foto -->
          <div class="left-content">

            <!-- TOP: Logo + Nama -->
            <div class="left-top">
              <div class="brand-box">
                <img
                  :src="logoGrahaMedika"
                  alt="Graha Medika"
                  style="width:42px !important;height:42px !important;object-fit:contain !important;border-radius:0 !important;flex-shrink:0;display:block !important;max-width:42px !important;"
                />
                <span class="brand-name">Graha Medika</span>
              </div>
            </div>

            <!-- BOTTOM: Quote -->
            <div class="left-bottom">
              <blockquote class="left-quote">
                "Sistem yang memudahkan kami melayani<br/>pasien dengan lebih cepat dan tepat."
              </blockquote>
              <div class="left-author">
                <div class="author-avatar">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" stroke="white" stroke-width="1.8"/>
                    <path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </div>
                <div>
                  <div class="author-name">dr. Sari Dewi</div>
                  <div class="author-role">Kepala Puskesmas Sehat Bersama</div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="panel-right">
          <div class="form-container">

            <!-- Icon -->
            <div class="form-icon">
              <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                <path d="M18 4C10.268 4 4 10.268 4 18s6.268 14 14 14 14-6.268 14-14S25.732 4 18 4z" fill="#3B6BF5" opacity="0.12"/>
                <path d="M18 10a5 5 0 100 10 5 5 0 000-10zm-9 16c0-4.418 4.03-8 9-8s9 3.582 9 8" stroke="#3B6BF5" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>

            <h1 class="form-title">Selamat Datang!</h1>
            <p class="form-subtitle">Masuk ke sistem manajemen rumah sakit</p>

            <!-- EMAIL -->
            <div class="input-group" :class="{ focused: focusedField === 'email', error: errors.email }">
              <label>Email</label>
              <div class="input-wrap">
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="nama@grahamedika.com"
                  @focus="focusedField = 'email'"
                  @blur="focusedField = ''"
                  @input="errors.email = ''"
                />
                <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="2" y="4" width="20" height="16" rx="3"/><path d="m2 7 10 7 10-7"/>
                </svg>
              </div>
              <span class="err-msg" v-if="errors.email">{{ errors.email }}</span>
            </div>

            <!-- PASSWORD -->
            <div class="input-group" :class="{ focused: focusedField === 'password', error: errors.password }">
              <label>Password</label>
              <div class="input-wrap">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Masukkan password"
                  @focus="focusedField = 'password'"
                  @blur="focusedField = ''"
                  @input="errors.password = ''"
                />
                <button type="button" class="toggle-pw" @click="showPassword = !showPassword">
                  <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>
                  </svg>
                </button>
              </div>
              <span class="err-msg" v-if="errors.password">{{ errors.password }}</span>
            </div>

            <!-- OPTIONS -->
            <div class="form-options">
              <label class="remember-me">
                <input type="checkbox" v-model="form.remember" />
                <span class="checkmark"></span>
                Ingat saya
              </label>
              <router-link to="/forgot-password" class="forgot-link">Lupa Password?</router-link>
            </div>

            <button class="btn-primary" @click="handleLogin" :disabled="isLoading">
              <span>Masuk</span>
            </button>

            <p class="signup-text">Belum punya akun? <router-link to="/register">Daftar Sekarang</router-link></p>
          </div>
        </div>
      </div>
    </transition>

    <!-- STATE: PROCESSING -->
    <transition name="fade">
      <div v-if="state === 'processing'" class="fullscreen-state blue-bg">
        <div class="floating-dots">
          <span v-for="i in 8" :key="i" class="fdot" :style="{ '--i': i }"></span>
        </div>
        <div class="state-content">
          <div class="spinner-ring">
            <svg class="spinner-svg" viewBox="0 0 80 80">
              <circle class="spinner-track" cx="40" cy="40" r="34" fill="none" stroke-width="5"/>
              <circle class="spinner-progress" cx="40" cy="40" r="34" fill="none" stroke-width="5"/>
            </svg>
            <div class="spinner-icon">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="8" r="4" stroke="white" stroke-width="1.8"/>
                <path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </div>
          </div>
          <p class="state-text">Processing ...</p>
        </div>
      </div>
    </transition>

    <!-- STATE: CONFIRMED -->
    <transition name="fade">
      <div v-if="state === 'confirmed'" class="fullscreen-state blue-bg">
        <div class="confetti-container">
          <span v-for="i in 20" :key="i" class="confetti-piece" :style="getConfettiStyle(i)"></span>
        </div>
        <div class="state-content">
          <div class="check-circle" :class="{ 'pop-in': state === 'confirmed' }">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
              <polyline points="20,6 9,17 4,12" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="check-stroke"/>
            </svg>
          </div>
          <p class="state-text">Confirmed!</p>
        </div>
      </div>
    </transition>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "LoginPage",
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();

    const state = ref<"form" | "processing" | "confirmed">("form");
    const focusedField = ref("");
    const showPassword = ref(false);
    const isLoading = ref(false);

    const logoGrahaMedika = "/images/logo-graha-medika.png";

    const form = reactive({ email: "", password: "", remember: false });
    const errors = reactive({ email: "", password: "" });

    function validate() {
      let valid = true;
      if (!form.email) {
        errors.email = "Email wajib diisi";
        valid = false;
      } else if (!/\S+@\S+\.\S+/.test(form.email)) {
        errors.email = "Format email tidak valid";
        valid = false;
      }
      if (!form.password) {
        errors.password = "Password wajib diisi";
        valid = false;
      }
      return valid;
    }

    async function handleLogin() {
      if (!validate()) return;
      state.value = "processing";
      isLoading.value = true;
      try {
        await authStore.login(form.email, form.password);
        state.value = "confirmed";
        setTimeout(() => router.push({ name: "dashboard" }), 1800);
      } catch (err: any) {
        state.value = "form";
        isLoading.value = false;
        const msg = err?.response?.data?.message || "Login gagal. Periksa kembali email dan password.";
        errors.email = msg;
      }
    }

    function getConfettiStyle(i: number) {
      const colors = ["#f59e0b", "#ef4444", "#22c55e", "#8b5cf6", "#3b82f6", "#ec4899", "#f97316"];
      return {
        left: `${Math.random() * 100}%`,
        animationDelay: `${(i * 0.08).toFixed(2)}s`,
        width: `${6 + Math.floor(Math.random() * 8)}px`,
        height: `${6 + Math.floor(Math.random() * 8)}px`,
        background: colors[i % colors.length],
        borderRadius: Math.random() > 0.5 ? "50%" : "2px",
        transform: `rotate(${Math.random() * 360}deg)`,
      };
    }

    return {
      state, form, errors, focusedField,
      showPassword, isLoading, logoGrahaMedika,
      handleLogin, getConfettiStyle,
    };
  },
});
</script>

<style scoped>
/* ===== BASE ===== */
.login-page {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: stretch;
  justify-content: center;
  background: #f0f4ff;
  font-family: 'Inter', 'Nunito', sans-serif;
  font-size: 14px;
  overflow: visible;
}

.login-wrapper {
  display: flex;
  width: 100%;
  min-height: 100vh;
  align-items: stretch;
}

/* ===== LEFT PANEL ===== */
.panel-left {
  flex: 0 0 47%;
  position: relative;
  display: flex;
  align-items: stretch;
  overflow: hidden;
  min-height: 100vh;
}

/* Foto dokter background */
.left-photo-bg {
  position: absolute;
  inset: 0;
  background-image: url('https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=900&q=80&auto=format&fit=crop');
  background-size: cover;
  background-position: center top;
  z-index: 0;
}

/* Overlay gradien gelap */
.left-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(15, 30, 80, 0.72) 0%,
    rgba(15, 30, 80, 0.45) 40%,
    rgba(15, 30, 80, 0.75) 75%,
    rgba(10, 20, 60, 0.92) 100%
  );
  z-index: 1;
}

/* Konten di atas foto */
.left-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 100%;
  padding: 36px 40px 44px;
}

.left-top {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

/* Brand */
.brand-box {
  display: flex;
  align-items: center;
  gap: 12px;
}
.brand-logo {
  width: 42px !important;
  height: 42px !important;
  min-width: 42px !important;
  min-height: 42px !important;
  background-size: contain !important;
  background-repeat: no-repeat !important;
  background-position: center !important;
  flex-shrink: 0 !important;
  display: block !important;
}
.brand-name {
  color: white;
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: 0.01em;
}

/* Quote di bawah */
.left-bottom {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.left-quote {
  margin: 0;
  padding: 0;
  font-size: 1.22rem;
  font-weight: 700;
  color: white;
  line-height: 1.55;
  letter-spacing: -0.01em;
  border: none;
}

.left-author {
  display: flex;
  align-items: center;
  gap: 12px;
}

.author-avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  border: 2px solid rgba(255,255,255,0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.author-name {
  font-size: 0.85rem;
  font-weight: 700;
  color: white;
}
.author-role {
  font-size: 0.73rem;
  color: rgba(255,255,255,0.6);
  margin-top: 2px;
}

/* ===== RIGHT PANEL ===== */
.panel-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  padding: 48px 40px;
  overflow-y: auto;
  min-height: 100vh;
  box-sizing: border-box;
}

.form-container {
  width: 100%;
  max-width: 400px;
}

/* Form icon */
.form-icon {
  width: 64px; height: 64px;
  background: #eff6ff;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

/* Titles */
.form-title {
  font-size: 1.75rem;
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 6px;
  letter-spacing: -0.02em;
}
.form-subtitle {
  font-size: 0.875rem;
  color: #64748b;
  margin: 0 0 28px;
  line-height: 1.5;
}

/* ===== INPUTS ===== */
.input-group {
  margin-bottom: 18px;
  width: 100%;
}
.input-group label {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 7px;
  letter-spacing: 0.01em;
}

.input-wrap {
  position: relative;
  width: 100%;
}

.input-wrap input {
  width: 100%;
  padding: 12px 44px 12px 14px;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.875rem;
  color: #0f172a;
  background: #f8fafc;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  box-sizing: border-box;
  font-family: inherit;
  line-height: 1.5;
}

/* Focus state */
.input-group.focused .input-wrap input {
  border-color: #3b6bf5;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(59, 107, 245, 0.1);
}
/* Error state */
.input-group.error .input-wrap input {
  border-color: #ef4444;
  box-shadow: none;
}

.input-icon {
  position: absolute;
  right: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

/* Password toggle */
.toggle-pw {
  position: absolute;
  right: 13px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  padding: 0;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}
.toggle-pw:hover { color: #3b6bf5; }

/* Error message */
.err-msg {
  font-size: 0.75rem;
  color: #ef4444;
  margin-top: 5px;
  display: block;
}

/* OPTIONS ROW */
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 22px;
}

/* Remember me checkbox */
.remember-me {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.82rem;
  font-weight: 500;
  color: #475569;
  cursor: pointer;
}
.remember-me input { display: none; }
.checkmark {
  width: 18px; height: 18px;
  border: 1.5px solid #cbd5e1;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
  background: white;
}
.remember-me input:checked + .checkmark {
  background: #3b6bf5;
  border-color: #3b6bf5;
}
.remember-me input:checked + .checkmark::after {
  content: '';
  width: 4px; height: 8px;
  border: 2px solid white;
  border-top: none;
  border-left: none;
  transform: rotate(45deg) translate(-1px, -1px);
  display: block;
}

.forgot-link {
  font-size: 0.82rem;
  color: #3b6bf5;
  font-weight: 600;
  text-decoration: none;
}
.forgot-link:hover { text-decoration: underline; }

/* ===== BUTTON ===== */
.btn-primary {
  width: 100%;
  padding: 13px 20px;
  background: linear-gradient(135deg, #3b6bf5, #2563eb);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 4px 14px rgba(59, 107, 245, 0.35);
  font-family: inherit;
  letter-spacing: 0.01em;
}
.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(59, 107, 245, 0.45);
}
.btn-primary:active:not(:disabled) { transform: translateY(0); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

/* Sign up link */
.signup-text {
  text-align: center;
  margin-top: 22px;
  font-size: 0.85rem;
  color: #64748b;
}
.signup-text a {
  color: #3b6bf5;
  font-weight: 700;
  text-decoration: none;
}
.signup-text a:hover { text-decoration: underline; }

/* ===== FULLSCREEN STATES ===== */
.fullscreen-state {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  z-index: 50;
}
.blue-bg {
  background: linear-gradient(145deg, #2563eb 0%, #1d4ed8 60%, #1e3a8a 100%);
}
.state-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}
.state-text {
  color: rgba(255,255,255,0.9);
  font-size: 1.05rem;
  font-weight: 600;
  letter-spacing: 0.03em;
}

/* Spinner */
.spinner-ring {
  position: relative;
  width: 88px; height: 88px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.spinner-svg {
  position: absolute;
  inset: 0;
  width: 88px; height: 88px;
  animation: rotate 1.4s linear infinite;
}
@keyframes rotate { to { transform: rotate(360deg); } }
.spinner-track { stroke: rgba(255,255,255,0.15); }
.spinner-progress {
  stroke: #fbbf24;
  stroke-dasharray: 213;
  stroke-dashoffset: 60;
  stroke-linecap: round;
}
.spinner-icon {
  width: 56px; height: 56px;
  background: rgba(255,255,255,0.15);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Confirmed check */
.check-circle {
  width: 88px; height: 88px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.check-circle.pop-in {
  animation: pop-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards,
             pulse-ring 1s 0.5s ease-out forwards;
}
@keyframes pop-in {
  0%   { transform: scale(0); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
@keyframes pulse-ring {
  0%   { box-shadow: 0 0 0 0 rgba(255,255,255,0.5); }
  100% { box-shadow: 0 0 0 30px rgba(255,255,255,0); }
}
.check-stroke {
  stroke-dasharray: 40;
  stroke-dashoffset: 40;
  animation: draw-check 0.4s 0.3s ease forwards;
}
@keyframes draw-check { to { stroke-dashoffset: 0; } }

/* Confetti */
.confetti-container {
  position: absolute;
  inset: 0;
  overflow: hidden;
  pointer-events: none;
}
.confetti-piece {
  position: absolute;
  top: -20px;
  animation: fall 1.5s ease-in forwards;
}
@keyframes fall {
  0%   { top: -20px; opacity: 1; transform: rotate(0deg); }
  100% { top: 110%; opacity: 0; transform: rotate(540deg); }
}

/* Floating dots */
.floating-dots { position: absolute; inset: 0; pointer-events: none; }
.fdot {
  position: absolute;
  width: 8px; height: 8px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  animation: fdrift calc(3s + var(--i) * 0.4s) ease-in-out infinite alternate;
  left: calc(var(--i) * 12%);
  top: calc(var(--i) * 10% + 5%);
}
@keyframes fdrift { to { transform: translateY(-30px) translateX(10px); opacity: 0.5; } }

/* ===== TRANSITIONS ===== */
.fade-slide-enter-active { animation: slideInRight 0.5s ease; }
.fade-slide-leave-active { animation: slideOutLeft 0.4s ease; }
@keyframes slideInRight { from { opacity: 0; transform: translateX(30px); } }
@keyframes slideOutLeft { to   { opacity: 0; transform: translateX(-30px); } }
.fade-enter-active { animation: fadeIn 0.4s ease; }
.fade-leave-active { animation: fadeOut 0.3s ease; }
@keyframes fadeIn  { from { opacity: 0; } }
@keyframes fadeOut { to   { opacity: 0; } }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .panel-left { display: none; }
  .panel-right { padding: 32px 20px; }
}
</style>
