<template>
  <div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-7">
      <div>
        <h1 class="fw-bold mb-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'}; font-size: 22px;`">
          Edit Profile
        </h1>
        <div class="d-flex align-items-center gap-2">
          <router-link to="/user/dashboard" class="text-gray-500 fs-7 text-hover-primary text-decoration-none">
            Dashboard
          </router-link>
          <span class="text-gray-500 fs-7">/</span>
          <span class="fs-7" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">Edit Profile</span>
        </div>
      </div>
      <button
        @click="handleSave"
        :disabled="isSaving"
        class="btn btn-primary fw-bold d-flex align-items-center gap-2"
        style="border-radius: 10px; padding: 10px 24px;"
      >
        <span v-if="isSaving" class="spinner-border spinner-border-sm" role="status"></span>
        <i v-else class="ki-duotone ki-check fs-4">
          <span class="path1"></span><span class="path2"></span>
        </i>
        {{ isSaving ? 'Saving...' : 'Save Changes' }}
      </button>
    </div>

    <!-- Alert Success -->
    <transition name="fade-slide">
      <div v-if="showSuccess"
        class="alert d-flex align-items-center gap-3 mb-6"
        style="border-radius: 12px; border: none; background: rgba(80,205,137,0.15); padding: 14px 20px;">
        <i class="ki-duotone ki-shield-tick fs-2 text-success">
          <span class="path1"></span><span class="path2"></span>
        </i>
        <span class="fw-semibold fs-7" style="color: #50cd89;">Profile updated successfully!</span>
      </div>
    </transition>

    <div class="row g-6">

      <!-- LEFT: Avatar & Status Card -->
      <div class="col-12 col-lg-4">

        <!-- Avatar Card -->
        <div class="card mb-6" :style="cardStyle">
          <div class="card-body d-flex flex-column align-items-center py-10 px-6">

            <!-- Avatar Upload -->
            <div class="position-relative mb-6" style="cursor: pointer;" @click="triggerAvatarUpload">
              <div class="avatar-ring" :style="`border-color: ${isDark ? '#2d2d3f' : '#eff2f5'};`">
                <div v-if="!avatarPreview" class="user-avatar-xl">
                  {{ form.name?.charAt(0).toUpperCase() }}
                </div>
                <img v-else :src="avatarPreview" class="user-avatar-xl-img" alt="avatar" />
              </div>
              <div class="avatar-edit-btn">
                <i class="ki-duotone ki-pencil fs-7 text-white">
                  <span class="path1"></span><span class="path2"></span>
                </i>
              </div>
            </div>
            <input ref="avatarInput" type="file" accept="image/*" style="display:none;" @change="onAvatarChange" />

            <h4 class="fw-bold mb-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">
              {{ form.name || 'Your Name' }}
            </h4>
            <span class="fs-7 fw-semibold mb-1 text-capitalize" style="color: #009ef7;">
              {{ form.role || 'User' }}
            </span>
            <span class="fs-8 mb-5" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
              {{ form.email }}
            </span>

            <!-- Status Badge -->
            <div class="d-flex align-items-center gap-2 px-5 py-2 rounded-pill"
              :style="`background: rgba(80,205,137,0.12); border: 1px solid rgba(80,205,137,0.25);`">
              <span style="width:8px; height:8px; border-radius:50%; background:#50cd89; flex-shrink:0; display:inline-block;"></span>
              <span class="fs-8 fw-bold" style="color: #50cd89; letter-spacing: 0.5px;">ACTIVE ACCOUNT</span>
            </div>

          </div>
        </div>

        <!-- Account Details Card -->
        <div class="card" :style="cardStyle">
          <div class="card-body p-6">
            <h6 class="fw-bold mb-4" :style="`color: ${isDark ? '#ffffff' : '#181c32'}; font-size: 13px; letter-spacing: 0.5px; text-transform: uppercase;`">
              Account Details
            </h6>
            <div class="d-flex flex-column gap-4">
              <div class="detail-row">
                <div class="detail-icon" :style="`background: rgba(0,158,247,0.1);`">
                  <i class="ki-duotone ki-sms fs-5 text-primary">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                </div>
                <div>
                  <div class="fs-8 mb-1" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">Email</div>
                  <div class="fw-semibold fs-7" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">
                    {{ form.email || '-' }}
                  </div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon" :style="`background: rgba(80,205,137,0.1);`">
                  <i class="ki-duotone ki-phone fs-5 text-success">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                </div>
                <div>
                  <div class="fs-8 mb-1" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">Phone</div>
                  <div class="fw-semibold fs-7" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">
                    {{ form.phone || '-' }}
                  </div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon" :style="`background: rgba(255,199,0,0.1);`">
                  <i class="ki-duotone ki-geolocation fs-5 text-warning">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                </div>
                <div>
                  <div class="fs-8 mb-1" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">Location</div>
                  <div class="fw-semibold fs-7" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">
                    {{ form.city && form.country ? `${form.city}, ${form.country}` : '-' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END LEFT -->

      <!-- RIGHT: Form Cards -->
      <div class="col-12 col-lg-8">

        <!-- Personal Information -->
        <div class="card mb-6" :style="cardStyle">
          <div class="card-header border-0 pt-6 pb-0 px-7">
            <div class="d-flex align-items-center gap-3">
              <div class="section-icon" style="background: rgba(0,158,247,0.1);">
                <i class="ki-duotone ki-profile-user fs-4 text-primary">
                  <span class="path1"></span><span class="path2"></span>
                  <span class="path3"></span><span class="path4"></span>
                </i>
              </div>
              <div>
                <h5 class="fw-bold mb-0" :style="`color: ${isDark ? '#ffffff' : '#181c32'}; font-size: 15px;`">
                  Personal Information
                </h5>
                <span class="fs-8" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
                  Update your personal details
                </span>
              </div>
            </div>
          </div>
          <div class="card-body px-7 pt-5 pb-7">
            <div class="row g-5">

              <!-- Full Name -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" :style="labelStyle">Full Name</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                  placeholder="Enter full name"
                />
              </div>

              <!-- Username -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" :style="labelStyle">Username</label>
                <div class="position-relative">
                  <span class="input-prefix" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">@</span>
                  <input
                    v-model="form.username"
                    type="text"
                    class="form-control-custom form-control-prefix"
                    :class="isDark ? 'form-control-dark' : 'form-control-light'"
                    placeholder="username"
                  />
                </div>
              </div>

              <!-- Email -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" :style="labelStyle">Email Address</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                  placeholder="Enter email"
                />
              </div>

              <!-- Phone -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" :style="labelStyle">Phone Number</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                  placeholder="+62 xxx-xxxx-xxxx"
                />
              </div>

              <!-- Date of Birth -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" :style="labelStyle">Date of Birth</label>
                <input
                  v-model="form.birthdate"
                  type="date"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                />
              </div>

              <!-- Gender -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" :style="labelStyle">Gender</label>
                <select
                  v-model="form.gender"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                >
                  <option value="" disabled>Select gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Prefer not to say</option>
                </select>
              </div>

              <!-- Bio -->
              <div class="col-12">
                <label class="form-label-custom" :style="labelStyle">Bio</label>
                <textarea
                  v-model="form.bio"
                  rows="3"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                  placeholder="Tell us about yourself..."
                  style="resize: none;"
                ></textarea>
                <div class="d-flex justify-content-end mt-1">
                  <span class="fs-8" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
                    {{ (form.bio || '').length }}/200 characters
                  </span>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Address Information -->
        <div class="card mb-6" :style="cardStyle">
          <div class="card-header border-0 pt-6 pb-0 px-7">
            <div class="d-flex align-items-center gap-3">
              <div class="section-icon" style="background: rgba(255,199,0,0.1);">
                <i class="ki-duotone ki-geolocation fs-4 text-warning">
                  <span class="path1"></span><span class="path2"></span>
                </i>
              </div>
              <div>
                <h5 class="fw-bold mb-0" :style="`color: ${isDark ? '#ffffff' : '#181c32'}; font-size: 15px;`">
                  Address Information
                </h5>
                <span class="fs-8" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
                  Your location and address details
                </span>
              </div>
            </div>
          </div>
          <div class="card-body px-7 pt-5 pb-7">
            <div class="row g-5">

              <!-- Address -->
              <div class="col-12">
                <label class="form-label-custom" :style="labelStyle">Street Address</label>
                <input
                  v-model="form.address"
                  type="text"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                  placeholder="Enter your street address"
                />
              </div>

              <!-- City -->
              <div class="col-12 col-md-4">
                <label class="form-label-custom" :style="labelStyle">City</label>
                <input
                  v-model="form.city"
                  type="text"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                  placeholder="City"
                />
              </div>

              <!-- State/Province -->
              <div class="col-12 col-md-4">
                <label class="form-label-custom" :style="labelStyle">State / Province</label>
                <input
                  v-model="form.state"
                  type="text"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                  placeholder="State / Province"
                />
              </div>

              <!-- Postal Code -->
              <div class="col-12 col-md-4">
                <label class="form-label-custom" :style="labelStyle">Postal Code</label>
                <input
                  v-model="form.postal"
                  type="text"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                  placeholder="00000"
                />
              </div>

              <!-- Country -->
              <div class="col-12">
                <label class="form-label-custom" :style="labelStyle">Country</label>
                <select
                  v-model="form.country"
                  class="form-control-custom"
                  :class="isDark ? 'form-control-dark' : 'form-control-light'"
                >
                  <option value="" disabled>Select country</option>
                  <option value="Indonesia">Indonesia</option>
                  <option value="Malaysia">Malaysia</option>
                  <option value="Singapore">Singapore</option>
                  <option value="United States">United States</option>
                  <option value="United Kingdom">United Kingdom</option>
                  <option value="Australia">Australia</option>
                  <option value="Other">Other</option>
                </select>
              </div>

            </div>
          </div>
        </div>

        <!-- Change Password -->
        <div class="card" :style="cardStyle">
          <div class="card-header border-0 pt-6 pb-0 px-7">
            <div class="d-flex align-items-center gap-3">
              <div class="section-icon" style="background: rgba(246,78,96,0.1);">
                <i class="ki-duotone ki-lock-2 fs-4 text-danger">
                  <span class="path1"></span><span class="path2"></span>
                  <span class="path3"></span>
                </i>
              </div>
              <div>
                <h5 class="fw-bold mb-0" :style="`color: ${isDark ? '#ffffff' : '#181c32'}; font-size: 15px;`">
                  Change Password
                </h5>
                <span class="fs-8" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
                  Leave blank if you don't want to change
                </span>
              </div>
            </div>
          </div>
          <div class="card-body px-7 pt-5 pb-7">
            <div class="row g-5">

              <!-- Current Password -->
              <div class="col-12">
                <label class="form-label-custom" :style="labelStyle">Current Password</label>
                <div class="position-relative">
                  <input
                    v-model="passwords.current"
                    :type="showPass.current ? 'text' : 'password'"
                    class="form-control-custom"
                    :class="isDark ? 'form-control-dark' : 'form-control-light'"
                    placeholder="Enter current password"
                    style="padding-right: 46px;"
                  />
                  <button type="button" class="pass-toggle-btn" @click="showPass.current = !showPass.current">
                    <i :class="`ki-duotone ki-${showPass.current ? 'eye-slash' : 'eye'} fs-5`"
                      :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
                      <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                  </button>
                </div>
              </div>

              <!-- New Password -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" :style="labelStyle">New Password</label>
                <div class="position-relative">
                  <input
                    v-model="passwords.new"
                    :type="showPass.new ? 'text' : 'password'"
                    class="form-control-custom"
                    :class="isDark ? 'form-control-dark' : 'form-control-light'"
                    placeholder="Enter new password"
                    style="padding-right: 46px;"
                  />
                  <button type="button" class="pass-toggle-btn" @click="showPass.new = !showPass.new">
                    <i :class="`ki-duotone ki-${showPass.new ? 'eye-slash' : 'eye'} fs-5`"
                      :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
                      <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                  </button>
                </div>
                <!-- Password Strength -->
                <div v-if="passwords.new.length > 0" class="mt-2">
                  <div class="d-flex gap-1 mb-1">
                    <div v-for="i in 4" :key="i" class="strength-bar"
                      :style="`background: ${i <= passwordStrength.score ? passwordStrength.color : (isDark ? '#2d2d3f' : '#eff2f5')};`">
                    </div>
                  </div>
                  <span class="fs-9 fw-semibold" :style="`color: ${passwordStrength.color};`">
                    {{ passwordStrength.label }}
                  </span>
                </div>
              </div>

              <!-- Confirm Password -->
              <div class="col-12 col-md-6">
                <label class="form-label-custom" :style="labelStyle">Confirm New Password</label>
                <div class="position-relative">
                  <input
                    v-model="passwords.confirm"
                    :type="showPass.confirm ? 'text' : 'password'"
                    class="form-control-custom"
                    :class="isDark ? 'form-control-dark' : 'form-control-light'"
                    placeholder="Confirm new password"
                    style="padding-right: 46px;"
                    :style="passwords.confirm.length > 0 && passwords.new !== passwords.confirm ? 'border-color: #f64e60 !important;' : ''"
                  />
                  <button type="button" class="pass-toggle-btn" @click="showPass.confirm = !showPass.confirm">
                    <i :class="`ki-duotone ki-${showPass.confirm ? 'eye-slash' : 'eye'} fs-5`"
                      :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
                      <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                  </button>
                </div>
                <div v-if="passwords.confirm.length > 0 && passwords.new !== passwords.confirm"
                  class="fs-8 mt-1" style="color: #f64e60;">
                  Passwords do not match
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>
      <!-- END RIGHT -->

    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, inject, computed, reactive } from "vue";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "EditProfile",
  setup() {
    const userStore = useAuthStore();
    const isDark = inject<any>("isDark");
    const avatarInput = ref<HTMLInputElement | null>(null);
    const avatarPreview = ref<string | null>(null);
    const isSaving = ref(false);
    const showSuccess = ref(false);

    const form = reactive({
      name:      userStore.user?.name     || "",
      username:  userStore.user?.username || "",
      email:     userStore.user?.email    || "",
      phone:     userStore.user?.phone    || "",
      role:      userStore.user?.role     || "user",
      bio:       "",
      birthdate: "",
      gender:    "",
      address:   "",
      city:      "",
      state:     "",
      postal:    "",
      country:   "Indonesia",
    });

    const passwords = reactive({
      current: "",
      new: "",
      confirm: "",
    });

    const showPass = reactive({
      current: false,
      new: false,
      confirm: false,
    });

    const cardStyle = computed(() => `
      border-radius: 14px;
      border: 1px solid ${isDark.value ? '#2d2d3f' : '#eff2f5'};
      background: ${isDark.value ? '#1e1e2d' : '#ffffff'};
      box-shadow: 0 4px 24px rgba(0,0,0,${isDark.value ? '0.15' : '0.04'});
      transition: background 0.3s, border-color 0.3s;
    `);

    const labelStyle = computed(() => `
      color: ${isDark.value ? '#a1a5b7' : '#5e6278'};
      font-size: 12.5px;
      font-weight: 600;
      margin-bottom: 6px;
      display: block;
      letter-spacing: 0.3px;
    `);

    const passwordStrength = computed(() => {
      const p = passwords.new;
      let score = 0;
      if (p.length >= 8)                         score++;
      if (/[A-Z]/.test(p))                       score++;
      if (/[0-9]/.test(p))                       score++;
      if (/[^A-Za-z0-9]/.test(p))               score++;
      const labels = ["", "Weak", "Fair", "Good", "Strong"];
      const colors = ["", "#f64e60", "#ffc700", "#009ef7", "#50cd89"];
      return { score, label: labels[score] || "Weak", color: colors[score] || "#f64e60" };
    });

    const triggerAvatarUpload = () => { avatarInput.value?.click(); };

    const onAvatarChange = (e: Event) => {
      const file = (e.target as HTMLInputElement).files?.[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => { avatarPreview.value = ev.target?.result as string; };
      reader.readAsDataURL(file);
    };

    const handleSave = async () => {
      isSaving.value = true;
      // Simulate API call
      await new Promise((r) => setTimeout(r, 1500));
      isSaving.value = false;
      showSuccess.value = true;
      setTimeout(() => { showSuccess.value = false; }, 4000);
    };

    return {
      isDark, userStore,
      form, passwords, showPass,
      avatarInput, avatarPreview,
      isSaving, showSuccess,
      cardStyle, labelStyle,
      passwordStrength,
      triggerAvatarUpload, onAvatarChange,
      handleSave,
    };
  },
});
</script>

<style scoped>
/* ===== AVATAR ===== */
.avatar-ring {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  border: 3px solid;
  padding: 3px;
  position: relative;
}
.user-avatar-xl {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: linear-gradient(135deg, #009ef7, #f64e60);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  font-weight: 700;
  color: white;
}
.user-avatar-xl-img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}
.avatar-edit-btn {
  position: absolute;
  bottom: 4px;
  right: 4px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #009ef7;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0,158,247,0.5);
  transition: transform 0.2s;
}
.avatar-edit-btn:hover { transform: scale(1.1); }

/* ===== DETAIL ROW ===== */
.detail-row {
  display: flex;
  align-items: center;
  gap: 12px;
}
.detail-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ===== SECTION ICON ===== */
.section-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ===== FORM CONTROLS ===== */
.form-label-custom {
  font-size: 12.5px;
  font-weight: 600;
  margin-bottom: 6px;
  display: block;
  letter-spacing: 0.3px;
}
.form-control-custom {
  width: 100%;
  padding: 11px 14px;
  border-radius: 10px;
  border: 1.5px solid transparent;
  font-size: 14px;
  font-weight: 500;
  outline: none;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.form-control-dark {
  background: #151521;
  color: #ffffff;
  border-color: #2d2d3f;
}
.form-control-dark::placeholder { color: #565674; }
.form-control-dark:focus {
  border-color: #009ef7;
  box-shadow: 0 0 0 3px rgba(0,158,247,0.12);
}
.form-control-light {
  background: #f9f9f9;
  color: #181c32;
  border-color: #eff2f5;
}
.form-control-light::placeholder { color: #b5b5c3; }
.form-control-light:focus {
  border-color: #009ef7;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(0,158,247,0.08);
}
.form-control-dark option { background: #1e1e2d; }

/* ===== PREFIX INPUT ===== */
.input-prefix {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  font-weight: 600;
  z-index: 1;
  pointer-events: none;
}
.form-control-prefix { padding-left: 28px; }

/* ===== PASSWORD TOGGLE ===== */
.pass-toggle-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1;
}

/* ===== STRENGTH BAR ===== */
.strength-bar {
  height: 4px;
  flex: 1;
  border-radius: 2px;
  transition: background 0.3s;
}

/* ===== TRANSITION ===== */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.35s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* ===== CARD HOVER ===== */
.card {
  transition: box-shadow 0.25s;
}
.card:hover {
  box-shadow: 0 8px 32px rgba(0,0,0,0.12) !important;
}
</style>
