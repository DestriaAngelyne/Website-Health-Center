<template>
  <div class="register-page">
    <!-- STATE: FORM -->
    <transition name="fade-slide">
      <div v-if="state === 'form'" class="register-wrapper">
        <!-- LEFT PANEL -->
        <div class="panel-left">
          <!-- Foto dokter sebagai background -->
          <div class="left-photo-bg"></div>
          <!-- Overlay gradient gelap -->
          <div class="left-overlay"></div>

          <!-- Konten di atas foto -->
          <div class="left-content">

            <!-- TOP: Logo + Nama (tetap) -->
            <div class="left-top">
              <div class="brand-box">
                <img :src="logoGrahaMedika" alt="Graha Medika" class="brand-logo" />
                <span class="brand-name">Graha Medika</span>
              </div>


            </div>

            <!-- BOTTOM: Quote gaya Nucleus -->
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


            <!-- STEP 1: Data Akun -->
            <transition name="step-slide">
              <div v-if="currentStep === 1" key="step1">
                <h1 class="form-title">Buat Akun</h1>
                <p class="form-subtitle">Isi data akun untuk login ke sistem</p>

                <div class="input-group" :class="{ focused: focusedField === 'name', error: errors.name }">
                  <label>Nama Lengkap</label>
                  <div class="input-wrap">
                    <input v-model="form.name" type="text" placeholder="John Doe"
                      @focus="focusedField = 'name'" @blur="focusedField = ''" @input="errors.name = ''" />
                    <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                      <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.582-7 8-7s8 3 8 7"/>
                    </svg>
                  </div>
                  <span class="err-msg" v-if="errors.name">{{ errors.name }}</span>
                </div>

                <div class="input-group" :class="{ focused: focusedField === 'email', error: errors.email }">
                  <label>Email</label>
                  <div class="input-wrap">
                    <input v-model="form.email" type="email" placeholder="nama@grahamedika.com"
                      @focus="focusedField = 'email'" @blur="focusedField = ''" @input="errors.email = ''" />
                    <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                      <rect x="2" y="4" width="20" height="16" rx="3"/><path d="m2 7 10 7 10-7"/>
                    </svg>
                  </div>
                  <span class="err-msg" v-if="errors.email">{{ errors.email }}</span>
                </div>

                <div class="input-group" :class="{ focused: focusedField === 'password', error: errors.password }">
                  <label>Password</label>
                  <div class="input-wrap">
                    <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Min. 8 karakter"
                      @focus="focusedField = 'password'" @blur="focusedField = ''" @input="errors.password = ''" />
                    <button type="button" class="toggle-pw" @click="showPassword = !showPassword">
                      <svg v-if="!showPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                      </svg>
                      <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>
                      </svg>
                    </button>
                  </div>
                  <div class="password-strength" v-if="form.password">
                    <div class="strength-bars">
                      <span class="sbar" :class="strengthLevel >= 1 ? 'active ' + strengthColor : ''"></span>
                      <span class="sbar" :class="strengthLevel >= 2 ? 'active ' + strengthColor : ''"></span>
                      <span class="sbar" :class="strengthLevel >= 3 ? 'active ' + strengthColor : ''"></span>
                      <span class="sbar" :class="strengthLevel >= 4 ? 'active ' + strengthColor : ''"></span>
                    </div>
                    <span class="strength-label" :class="strengthColor">{{ strengthLabel }}</span>
                  </div>
                  <span class="err-msg" v-if="errors.password">{{ errors.password }}</span>
                </div>

                <div class="input-group" :class="{ focused: focusedField === 'confirm', error: errors.confirmPassword }">
                  <label>Konfirmasi Password</label>
                  <div class="input-wrap">
                    <input v-model="form.confirmPassword" :type="showConfirm ? 'text' : 'password'" placeholder="Ulangi password"
                      @focus="focusedField = 'confirm'" @blur="focusedField = ''" @input="errors.confirmPassword = ''" />
                    <button type="button" class="toggle-pw" @click="showConfirm = !showConfirm">
                      <svg v-if="!showConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                      </svg>
                      <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>
                      </svg>
                    </button>
                  </div>
                  <span class="err-msg" v-if="errors.confirmPassword">{{ errors.confirmPassword }}</span>
                </div>

                <button class="btn-primary" @click="goToStep2">
                  Lanjutkan
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                  </svg>
                </button>

                <p class="signin-text">Sudah punya akun? <router-link to="/login">Masuk di sini</router-link></p>
              </div>
            </transition>

            <!-- STEP 2: Identitas -->
            <transition name="step-slide">
              <div v-if="currentStep === 2" key="step2">
                <h1 class="form-title">Identitas Diri</h1>
                <p class="form-subtitle">Lengkapi data diri kamu</p>

                <div class="input-row">
                  <div class="input-group" :class="{ focused: focusedField === 'nik', error: errors.nik }">
                    <label>NIK</label>
                    <div class="input-wrap">
                      <input v-model="form.nik" type="text" placeholder="16 digit NIK"
                        maxlength="16"
                        @focus="focusedField = 'nik'" @blur="focusedField = ''" @input="errors.nik = ''" />
                      <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 10h.01M16 14h.01M6 10h6M6 14h4"/>
                      </svg>
                    </div>
                    <span class="err-msg" v-if="errors.nik">{{ errors.nik }}</span>
                  </div>

                  <div class="input-group" :class="{ focused: focusedField === 'phone', error: errors.phone }">
                    <label>No. Telepon</label>
                    <div class="input-wrap">
                      <input v-model="form.phone" type="tel" placeholder="08xxxxxxxxxx"
                        @focus="focusedField = 'phone'" @blur="focusedField = ''" @input="errors.phone = ''" />
                      <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.81 19.79 19.79 0 010 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                      </svg>
                    </div>
                    <span class="err-msg" v-if="errors.phone">{{ errors.phone }}</span>
                  </div>
                </div>

                <div class="input-group" :class="{ focused: focusedField === 'role', error: errors.role }">
                  <label>Jabatan / Role</label>
                  <div class="input-wrap select-wrap">
                    <select v-model="form.role"
                      @focus="focusedField = 'role'" @blur="focusedField = ''" @change="errors.role = ''">
                      <option value="" disabled>Pilih jabatan</option>
                      <option value="dokter">Dokter</option>
                      <option value="perawat">Perawat</option>
                      <option value="admin">Admin</option>
                      <option value="farmasi">Farmasi</option>
                      <option value="laboratorium">Laboratorium</option>
                      <option value="kasir">Kasir</option>
                    </select>
                    <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M6 9l6 6 6-6"/>
                    </svg>
                  </div>
                  <span class="err-msg" v-if="errors.role">{{ errors.role }}</span>
                </div>

                <div class="input-group" :class="{ focused: focusedField === 'address', error: errors.address }">
                  <label>Alamat</label>
                  <div class="input-wrap">
                    <textarea v-model="form.address" placeholder="Jl. Contoh No. 1, Kota..."
                      rows="3"
                      @focus="focusedField = 'address'" @blur="focusedField = ''" @input="errors.address = ''">
                    </textarea>
                  </div>
                  <span class="err-msg" v-if="errors.address">{{ errors.address }}</span>
                </div>

                <div class="terms-box">
                  <label class="terms-check">
                    <input type="checkbox" v-model="form.agreeTerms" />
                    <span class="checkmark"></span>
                    <span>Saya menyetujui <a href="#">Syarat & Ketentuan</a> yang berlaku</span>
                  </label>
                  <span class="err-msg" v-if="errors.agreeTerms">{{ errors.agreeTerms }}</span>
                </div>

                <div class="btn-row">
                  <button class="btn-back" @click="currentStep = 1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                  </button>
                  <button class="btn-primary flex-1" @click="handleRegister" :disabled="isLoading">
                    <span>Daftar Sekarang</span>
                  </button>
                </div>
              </div>
            </transition>
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
          <p class="state-text">Akun Berhasil Dibuat!</p>
          <p class="state-sub">Mengalihkan ke halaman login...</p>
        </div>
      </div>
    </transition>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, computed } from "vue";
import { useRouter } from "vue-router";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "RegisterPage",
  setup() {
    const router = useRouter();

    const state = ref<"form" | "processing" | "confirmed">("form");
    const currentStep = ref(1);
    const focusedField = ref("");
    const showPassword = ref(false);
    const showConfirm = ref(false);
    const isLoading = ref(false);

    const logoGrahaMedika = "/images/logo-graha-medika.png";

    const form = reactive({
      name: "",
      email: "",
      password: "",
      confirmPassword: "",
      nik: "",
      phone: "",
      role: "",
      address: "",
      agreeTerms: false,
    });

    const errors = reactive({
      name: "",
      email: "",
      password: "",
      confirmPassword: "",
      nik: "",
      phone: "",
      role: "",
      address: "",
      agreeTerms: "",
    });

    const strengthLevel = computed(() => {
      const p = form.password;
      if (!p) return 0;
      let score = 0;
      if (p.length >= 8) score++;
      if (/[A-Z]/.test(p)) score++;
      if (/[0-9]/.test(p)) score++;
      if (/[^A-Za-z0-9]/.test(p)) score++;
      return score;
    });
    const strengthColor = computed(() => {
      const lvl = strengthLevel.value;
      if (lvl <= 1) return "weak";
      if (lvl === 2) return "fair";
      if (lvl === 3) return "good";
      return "strong";
    });
    const strengthLabel = computed(() => {
      const map: Record<string, string> = { weak: "Lemah", fair: "Cukup", good: "Bagus", strong: "Kuat" };
      return map[strengthColor.value] || "";
    });

    function validateStep1() {
      let valid = true;
      if (!form.name.trim()) { errors.name = "Nama wajib diisi"; valid = false; }
      if (!form.email) { errors.email = "Email wajib diisi"; valid = false; }
      else if (!/\S+@\S+\.\S+/.test(form.email)) { errors.email = "Format email tidak valid"; valid = false; }
      if (!form.password) { errors.password = "Password wajib diisi"; valid = false; }
      else if (form.password.length < 8) { errors.password = "Password minimal 8 karakter"; valid = false; }
      if (!form.confirmPassword) { errors.confirmPassword = "Konfirmasi password wajib diisi"; valid = false; }
      else if (form.password !== form.confirmPassword) { errors.confirmPassword = "Password tidak cocok"; valid = false; }
      return valid;
    }

    function validateStep2() {
      let valid = true;
      if (!form.nik) { errors.nik = "NIK wajib diisi"; valid = false; }
      else if (form.nik.length !== 16) { errors.nik = "NIK harus 16 digit"; valid = false; }
      if (!form.phone) { errors.phone = "No. telepon wajib diisi"; valid = false; }
      if (!form.role) { errors.role = "Jabatan wajib dipilih"; valid = false; }
      if (!form.address.trim()) { errors.address = "Alamat wajib diisi"; valid = false; }
      if (!form.agreeTerms) { errors.agreeTerms = "Kamu harus menyetujui syarat & ketentuan"; valid = false; }
      return valid;
    }

    function goToStep2() {
      if (validateStep1()) currentStep.value = 2;
    }

    async function handleRegister() {
      if (!validateStep2()) return;

      state.value = "processing";
      isLoading.value = true;

      try {
        await ApiService.post("register", {
          name: form.name,
          email: form.email,
          password: form.password,
          password_confirmation: form.confirmPassword,
          nik: form.nik,
          phone: form.phone,
          role: form.role,
          address: form.address,
        });

        state.value = "confirmed";
        setTimeout(() => {
          router.push({ name: "sign-in" });
        }, 2200);
      } catch (err: any) {
        state.value = "form";
        isLoading.value = false;
        const data = err?.response?.data;
        if (data?.errors) {
          Object.assign(errors, data.errors);
          if (data.errors.email || data.errors.password || data.errors.name) {
            currentStep.value = 1;
          }
        } else {
          errors.email = data?.message || "Pendaftaran gagal. Coba lagi.";
          currentStep.value = 1;
        }
      }
    }

    function getConfettiStyle(i: number) {
      const colors = ["#f59e0b", "#ef4444", "#22c55e", "#8b5cf6", "#3b82f6", "#ec4899", "#f97316"];
      const x = Math.random() * 100;
      const delay = (i * 0.07).toFixed(2);
      const size = 6 + Math.floor(Math.random() * 8);
      return {
        left: `${x}%`,
        animationDelay: `${delay}s`,
        width: `${size}px`,
        height: `${size}px`,
        background: colors[i % colors.length],
        borderRadius: Math.random() > 0.5 ? "50%" : "2px",
        transform: `rotate(${Math.random() * 360}deg)`,
      };
    }

    return {
      state, currentStep, form, errors,
      focusedField, showPassword, showConfirm, isLoading,
      logoGrahaMedika,
      strengthLevel, strengthColor, strengthLabel,
      goToStep2, handleRegister, getConfettiStyle,
    };
  },
});
</script>

<style scoped>
/* ===== BASE ===== */
.register-page {
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

.register-wrapper {
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

/* Foto dokter background — pakai Unsplash free photo */
.left-photo-bg {
  position: absolute;
  inset: 0;
  background-image: url('https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=900&q=80&auto=format&fit=crop');
  background-size: cover;
  background-position: center top;
  z-index: 0;
}

/* Overlay gradien gelap di atas foto */
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
  width: 40px;
  height: 40px;
  object-fit: contain;
  object-position: center;
  border-radius: 0;
  display: block;
  flex-shrink: 0;
}

.brand-name {
  color: white;
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: 0.01em;
}

/* Steps visual */
.steps-visual {
  display: flex;
  align-items: center;
  gap: 10px;
}
.step-item { display: flex; flex-direction: column; align-items: center; gap: 6px; }
.step-circle {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: rgba(255,255,255,0.15);
  border: 2px solid rgba(255,255,255,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.9rem;
  font-weight: 700;
  transition: all 0.3s;
  backdrop-filter: blur(4px);
}
.step-item.active .step-circle {
  background: white;
  color: #1d4ed8;
  border-color: white;
  box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
}
.step-item.done .step-circle {
  background: #22c55e;
  border-color: #22c55e;
}
.step-label {
  color: rgba(255,255,255,0.65);
  font-size: 0.72rem;
  font-weight: 500;
}
.step-item.active .step-label,
.step-item.done .step-label { color: white; }

.step-line {
  width: 52px; height: 2px;
  background: rgba(255,255,255,0.2);
  border-radius: 99px;
  margin-bottom: 22px;
  transition: background 0.3s;
}
.step-line.active { background: #22c55e; }

/* Quote Nucleus-style di bawah */
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
  align-items: flex-start;     /* tidak center vertikal agar tidak terpotong */
  justify-content: center;
  background: white;
  padding: 48px 40px;
  overflow-y: auto;
  min-height: 100vh;
  box-sizing: border-box;
}

.form-container {
  width: 100%;
  max-width: 440px;
  padding-top: 8px;            /* ruang agar icon tidak terpotong di atas */
}

/* Form icon — sama persis login */
.form-icon {
  width: 64px; height: 64px;
  background: #eff6ff;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  flex-shrink: 0;
  overflow: visible;
}

/* Top step indicator */
.top-steps {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 24px;
}
.top-step {
  display: flex;
  align-items: center;
  gap: 8px;
  opacity: 0.4;
  transition: opacity 0.3s;
}
.top-step.active, .top-step.done { opacity: 1; }
.ts-num {
  width: 26px; height: 26px;
  border-radius: 50%;
  background: #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  color: #64748b;
  transition: all 0.3s;
  flex-shrink: 0;
}
.top-step.active .ts-num { background: #3b6bf5; color: white; }
.top-step.done .ts-num { background: #22c55e; color: white; }
.ts-label { font-size: 0.8rem; color: #64748b; font-weight: 600; white-space: nowrap; }
.top-step.active .ts-label { color: #0f172a; }
.ts-line {
  flex: 1;
  height: 1.5px;
  background: #e2e8f0;
  border-radius: 99px;
  transition: background 0.3s;
}
.ts-line.active { background: #22c55e; }

/* Titles — sama dengan login */
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
  margin: 0 0 24px;
  line-height: 1.5;
}

/* ===== INPUTS — konsisten satu standar ===== */
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

/* Semua input, textarea, select — ukuran & padding seragam */
.input-wrap input,
.input-wrap textarea,
.input-wrap select {
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
.input-wrap textarea {
  resize: none;
  padding-right: 14px; /* textarea tidak ada icon kanan */
}
.input-wrap select {
  appearance: none;
  -webkit-appearance: none;
  cursor: pointer;
}

/* Focus state */
.input-group.focused .input-wrap input,
.input-group.focused .input-wrap select,
.input-group.focused .input-wrap textarea {
  border-color: #3b6bf5;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(59, 107, 245, 0.1);
}
/* Error state */
.input-group.error .input-wrap input,
.input-group.error .input-wrap select,
.input-group.error .input-wrap textarea {
  border-color: #ef4444;
  box-shadow: none;
}

/* Icons inside input */
.input-icon {
  position: absolute;
  right: 13px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}
.select-wrap .input-icon { pointer-events: none; }

/* Textarea icon handled by no padding-right, no icon shown */

/* Password toggle button */
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

/* Input row: 2 kolom sejajar */
.input-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.input-row .input-group { margin-bottom: 0; }

/* Password strength */
.password-strength {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 7px;
}
.strength-bars { display: flex; gap: 4px; }
.sbar {
  width: 30px; height: 4px;
  background: #e2e8f0;
  border-radius: 99px;
  transition: background 0.25s;
}
.sbar.active.weak   { background: #ef4444; }
.sbar.active.fair   { background: #f59e0b; }
.sbar.active.good   { background: #3b82f6; }
.sbar.active.strong { background: #22c55e; }
.strength-label { font-size: 0.72rem; font-weight: 600; }
.strength-label.weak   { color: #ef4444; }
.strength-label.fair   { color: #f59e0b; }
.strength-label.good   { color: #3b82f6; }
.strength-label.strong { color: #22c55e; }

/* Terms checkbox */
.terms-box { margin-bottom: 20px; }
.terms-check {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 0.82rem;
  color: #475569;
  cursor: pointer;
  line-height: 1.55;
}
.terms-check input { display: none; }
.checkmark {
  width: 18px; height: 18px;
  border: 1.5px solid #cbd5e1;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
  margin-top: 1px;
  background: white;
}
.terms-check input:checked + .checkmark {
  background: #3b6bf5;
  border-color: #3b6bf5;
}
.terms-check input:checked + .checkmark::after {
  content: '';
  width: 4px; height: 8px;
  border: 2px solid white;
  border-top: none;
  border-left: none;
  transform: rotate(45deg) translate(-1px, -1px);
  display: block;
}
.terms-check a { color: #3b6bf5; font-weight: 600; text-decoration: none; }
.terms-check a:hover { text-decoration: underline; }

/* ===== BUTTONS — sama persis dengan login ===== */
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
.btn-primary.flex-1 { flex: 1; width: auto; }

.btn-row { display: flex; gap: 10px; align-items: stretch; }

.btn-back {
  padding: 12px 18px;
  background: #f1f5f9;
  color: #475569;
  border: none;
  border-radius: 10px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background 0.2s;
  font-family: inherit;
  white-space: nowrap;
}
.btn-back:hover { background: #e2e8f0; }

/* Sign in link */
.signin-text {
  text-align: center;
  margin-top: 22px;
  font-size: 0.85rem;
  color: #64748b;
}
.signin-text a {
  color: #3b6bf5;
  font-weight: 700;
  text-decoration: none;
}
.signin-text a:hover { text-decoration: underline; }

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
.state-sub {
  color: rgba(255,255,255,0.55);
  font-size: 0.82rem;
  margin-top: -8px;
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
.step-slide-enter-active { animation: stepIn 0.35s ease; }
.step-slide-leave-active { animation: stepOut 0.25s ease; position: absolute; width: 100%; }
@keyframes stepIn  { from { opacity: 0; transform: translateX(20px); } }
@keyframes stepOut { to   { opacity: 0; transform: translateX(-20px); } }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .panel-left { display: none; }
  .panel-right { padding: 32px 20px; }
  .input-row { grid-template-columns: 1fr; }
}
</style>
