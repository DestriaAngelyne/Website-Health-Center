<template>
  <div class="container-xxl">
    <!--begin::Loading-->
    <div v-if="loadingData" class="d-flex justify-content-center py-20">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <!--end::Loading-->

    <div v-else>
      <div class="d-flex align-items-center mb-7">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 my-0">
          Edit Profil Saya
        </h1>
      </div>

      <div class="row g-5 g-xl-10">
        <!--begin::Col Left - Foto Profil-->
        <div class="col-xl-4">
          <div class="card mb-5">
            <div class="card-body d-flex flex-column align-items-center py-10">
              <div class="symbol symbol-100px symbol-circle mb-5 position-relative">
                <img
                  v-if="authStore.fotoUrl"
                  :src="authStore.fotoUrl"
                  alt="Foto Profil"
                  class="w-100 h-100 rounded-circle object-fit-cover"
                />
                <div
                  v-else
                  class="symbol-label fs-1 fw-bold text-white"
                  :style="{ background: '#1B84FF' }"
                >
                  {{ form.nama_lengkap?.charAt(0)?.toUpperCase() || '?' }}
                </div>
                <label
                  for="upload_photo"
                  class="btn btn-icon btn-circle btn-color-white w-25px h-25px bg-primary position-absolute bottom-0 end-0"
                  style="cursor: pointer"
                  title="Ganti Foto"
                >
                  <i class="ki-duotone ki-pencil fs-7">
                    <span class="path1"></span>
                    <span class="path2"></span>
                  </i>
                </label>
                <input
                  id="upload_photo"
                  type="file"
                  accept="image/*"
                  class="d-none"
                  @change="onPhotoChange"
                />
              </div>

              <div class="fw-bold fs-4 text-gray-900">{{ form.nama_lengkap }}</div>
              <div class="text-muted fs-6 mb-3">{{ form.email }}</div>
              <span class="badge badge-light-primary fw-semibold px-4 py-2">{{ roleLabel }}</span>

              <div class="separator separator-dashed my-6 w-100"></div>

              <div class="w-100">
                <div class="d-flex justify-content-between mb-3">
                  <span class="text-muted fs-7">Username</span>
                  <span class="fw-bold fs-7 text-gray-800">{{ form.username || '-' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted fs-7">No. Telepon</span>
                  <span class="fw-bold fs-7 text-gray-800">{{ form.no_telepon || '-' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--end::Col Left-->

        <!--begin::Col Right - Form-->
        <div class="col-xl-8">

          <div
            v-if="alertMsg.text"
            class="alert d-flex align-items-center mb-6"
            :class="alertMsg.type === 'success' ? 'alert-success' : 'alert-danger'"
          >
            <span class="fw-semibold">{{ alertMsg.text }}</span>
          </div>

          <!--begin::Card Data Diri-->
          <div class="card mb-5">
            <div class="card-header">
              <div class="card-title">
                <h3 class="fw-bold m-0">Data Diri</h3>
              </div>
            </div>
            <div class="card-body">
              <div class="row mb-5">
                <div class="col-md-6">
                  <label class="form-label required fw-semibold">Nama Lengkap</label>
                  <input
                    v-model="form.nama_lengkap"
                    type="text"
                    class="form-control form-control-solid"
                    placeholder="Masukkan nama lengkap"
                    :class="{ 'is-invalid': errors.nama_lengkap }"
                  />
                  <div v-if="errors.nama_lengkap" class="invalid-feedback">{{ errors.nama_lengkap }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label required fw-semibold">Email</label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="form-control form-control-solid"
                    placeholder="email@example.com"
                    :class="{ 'is-invalid': errors.email }"
                  />
                  <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
                </div>
              </div>

              <div class="row mb-5">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">No. Telepon</label>
                  <input
                    v-model="form.no_telepon"
                    type="text"
                    class="form-control form-control-solid"
                    placeholder="08xxxxxxxxxx"
                    :class="{ 'is-invalid': errors.no_telepon }"
                  />
                  <div v-if="errors.no_telepon" class="invalid-feedback">{{ errors.no_telepon }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Username</label>
                  <input
                    :value="form.username"
                    type="text"
                    class="form-control form-control-solid"
                    disabled
                  />
                  <div class="text-muted fs-8 mt-1">Username tidak dapat diubah.</div>
                </div>
              </div>

              <div class="d-flex justify-content-end">
                <button
                  type="button"
                  class="btn btn-primary"
                  @click="simpanProfil"
                  :disabled="loadingSimpan"
                >
                  <span v-if="!loadingSimpan">Simpan Perubahan</span>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Menyimpan...
                  </span>
                </button>
              </div>
            </div>
          </div>
          <!--end::Card Data Diri-->

          <!--begin::Card Ganti Password-->
          <div class="card">
            <div class="card-header">
              <div class="card-title">
                <h3 class="fw-bold m-0">Ganti Password</h3>
              </div>
            </div>
            <div class="card-body">
              <div class="mb-5">
                <label class="form-label required fw-semibold">Password Lama</label>
                <div class="position-relative">
                  <input
                    v-model="passwordForm.password_lama"
                    :type="showPasswordLama ? 'text' : 'password'"
                    class="form-control form-control-solid pe-12"
                    placeholder="Masukkan password lama"
                    :class="{ 'is-invalid': passwordErrors.password_lama }"
                  />
                  <button
                    type="button"
                    class="btn btn-icon position-absolute end-0 top-0 h-100"
                    @click="showPasswordLama = !showPasswordLama"
                  >
                    <i :class="showPasswordLama ? 'ki-duotone ki-eye-slash' : 'ki-duotone ki-eye'" class="fs-4">
                      <span class="path1"></span><span class="path2"></span><span class="path3" v-if="!showPasswordLama"></span>
                    </i>
                  </button>
                </div>
                <div v-if="passwordErrors.password_lama" class="text-danger fs-7 mt-1">{{ passwordErrors.password_lama }}</div>
              </div>

              <div class="mb-5">
                <label class="form-label required fw-semibold">Password Baru</label>
                <div class="position-relative">
                  <input
                    v-model="passwordForm.password_baru"
                    :type="showPasswordBaru ? 'text' : 'password'"
                    class="form-control form-control-solid pe-12"
                    placeholder="Minimal 8 karakter"
                    :class="{ 'is-invalid': passwordErrors.password_baru }"
                  />
                  <button
                    type="button"
                    class="btn btn-icon position-absolute end-0 top-0 h-100"
                    @click="showPasswordBaru = !showPasswordBaru"
                  >
                    <i :class="showPasswordBaru ? 'ki-duotone ki-eye-slash' : 'ki-duotone ki-eye'" class="fs-4">
                      <span class="path1"></span><span class="path2"></span><span class="path3" v-if="!showPasswordBaru"></span>
                    </i>
                  </button>
                </div>
                <div v-if="passwordErrors.password_baru" class="text-danger fs-7 mt-1">{{ passwordErrors.password_baru }}</div>
              </div>

              <div class="mb-7">
                <label class="form-label required fw-semibold">Konfirmasi Password Baru</label>
                <div class="position-relative">
                  <input
                    v-model="passwordForm.konfirmasi_password"
                    :type="showKonfirmasi ? 'text' : 'password'"
                    class="form-control form-control-solid pe-12"
                    placeholder="Ulangi password baru"
                    :class="{ 'is-invalid': passwordErrors.konfirmasi_password }"
                  />
                  <button
                    type="button"
                    class="btn btn-icon position-absolute end-0 top-0 h-100"
                    @click="showKonfirmasi = !showKonfirmasi"
                  >
                    <i :class="showKonfirmasi ? 'ki-duotone ki-eye-slash' : 'ki-duotone ki-eye'" class="fs-4">
                      <span class="path1"></span><span class="path2"></span><span class="path3" v-if="!showKonfirmasi"></span>
                    </i>
                  </button>
                </div>
                <div v-if="passwordErrors.konfirmasi_password" class="text-danger fs-7 mt-1">{{ passwordErrors.konfirmasi_password }}</div>
              </div>

              <div class="d-flex justify-content-end">
                <button
                  type="button"
                  class="btn btn-danger"
                  @click="gantiPassword"
                  :disabled="loadingPassword"
                >
                  <span v-if="!loadingPassword">Ganti Password</span>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Memproses...
                  </span>
                </button>
              </div>
            </div>
          </div>
          <!--end::Card Ganti Password-->

        </div>
        <!--end::Col Right-->
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth.store";
import { ProfilStaffService } from "@/services/profil-staff.service";

const ROLE = "admin" as const;

export default defineComponent({
  name: "admin-edit-profil",
  setup() {
    const authStore = useAuthStore();
    const roleLabel = "Administrator";

    const form = reactive({
      nama_lengkap: "",
      username    : "",
      email       : "",
      no_telepon  : "",
    });

    const errors        = reactive<Record<string, string>>({});
    const alertMsg      = reactive({ text: "", type: "success" });
    const loadingData   = ref(true);
    const loadingSimpan = ref(false);

    const passwordForm = reactive({
      password_lama      : "",
      password_baru      : "",
      konfirmasi_password: "",
    });
    const passwordErrors   = reactive<Record<string, string>>({});
    const loadingPassword  = ref(false);
    const showPasswordLama = ref(false);
    const showPasswordBaru = ref(false);
    const showKonfirmasi   = ref(false);

    const showAlert = (text: string, type: "success" | "danger" = "success") => {
      alertMsg.text = text;
      alertMsg.type = type;
      setTimeout(() => { alertMsg.text = ""; }, 4000);
    };

    // Muat data profil dari API
    onMounted(async () => {
      try {
        const res = await ProfilStaffService.getProfil(ROLE);
        const d = res.data.data;
        form.nama_lengkap = d.nama_lengkap ?? "";
        form.username     = d.username ?? "";
        form.email        = d.email ?? "";
        form.no_telepon   = d.no_telepon ?? "";
      } catch (e) {
        console.error("Gagal memuat profil", e);
        showAlert("Gagal memuat profil", "danger");
      } finally {
        loadingData.value = false;
      }
    });

    // Foto: dipotong persegi dan disimpan di localStorage per-user,
    // sama seperti halaman profil pasien (dibaca lewat authStore.fotoUrl)
    const onPhotoChange = (e: Event) => {
      const input = e.target as HTMLInputElement;
      const file  = input.files?.[0];
      if (!file) return;
      if (!file.type.startsWith("image/")) {
        showAlert("File harus berupa gambar", "danger");
        input.value = "";
        return;
      }
      const reader = new FileReader();
      reader.onload = (ev) => {
        const img = new Image();
        img.onload = () => {
          const SIZE = 256;
          const side = Math.min(img.width, img.height);
          const canvas = document.createElement("canvas");
          canvas.width  = SIZE;
          canvas.height = SIZE;
          const ctx = canvas.getContext("2d");
          if (!ctx) return;
          ctx.drawImage(img, (img.width - side) / 2, (img.height - side) / 2, side, side, 0, 0, SIZE, SIZE);
          const dataUrl = canvas.toDataURL("image/jpeg", 0.85);
          try {
            localStorage.setItem(authStore.fotoKey, dataUrl);
          } catch (err) {
            showAlert("Foto gagal disimpan", "danger");
            return;
          }
          if (authStore.user) authStore.user.foto_url = dataUrl;
          showAlert("Foto profil diperbarui!");
        };
        img.src = ev.target?.result as string;
      };
      reader.readAsDataURL(file);
      input.value = "";
    };

    // Simpan profil
    const simpanProfil = async () => {
      Object.keys(errors).forEach((k) => delete errors[k]);
      if (!form.nama_lengkap.trim()) errors.nama_lengkap = "Nama lengkap wajib diisi";
      if (!form.email.trim())        errors.email        = "Email wajib diisi";
      if (Object.keys(errors).length) return;

      loadingSimpan.value = true;
      try {
        await ProfilStaffService.updateProfil(ROLE, {
          nama_lengkap: form.nama_lengkap,
          email       : form.email,
          no_telepon  : form.no_telepon,
        });
        await authStore.fetchUser();
        showAlert("Profil berhasil diperbarui!");
      } catch (err: any) {
        if (err.response?.data?.errors) {
          const serverErrors = err.response.data.errors;
          Object.keys(serverErrors).forEach((key) => {
            errors[key] = Array.isArray(serverErrors[key]) ? serverErrors[key][0] : serverErrors[key];
          });
        } else {
          showAlert(err.response?.data?.message ?? "Gagal menyimpan profil", "danger");
        }
      } finally {
        loadingSimpan.value = false;
      }
    };

    // Ganti password
    const gantiPassword = async () => {
      Object.keys(passwordErrors).forEach((k) => delete passwordErrors[k]);
      if (!passwordForm.password_lama) passwordErrors.password_lama = "Password lama wajib diisi";
      if (!passwordForm.password_baru || passwordForm.password_baru.length < 8) passwordErrors.password_baru = "Password baru minimal 8 karakter";
      if (passwordForm.password_baru !== passwordForm.konfirmasi_password) passwordErrors.konfirmasi_password = "Konfirmasi password tidak cocok";
      if (Object.keys(passwordErrors).length) return;

      loadingPassword.value = true;
      try {
        await ProfilStaffService.gantiPassword(ROLE, passwordForm);
        passwordForm.password_lama       = "";
        passwordForm.password_baru       = "";
        passwordForm.konfirmasi_password = "";
        showAlert("Password berhasil diubah!");
      } catch (err: any) {
        if (err.response?.data?.errors) {
          const serverErrors = err.response.data.errors;
          Object.keys(serverErrors).forEach((key) => {
            passwordErrors[key] = Array.isArray(serverErrors[key]) ? serverErrors[key][0] : serverErrors[key];
          });
        } else {
          showAlert(err.response?.data?.message ?? "Gagal mengganti password", "danger");
        }
      } finally {
        loadingPassword.value = false;
      }
    };

    return {
      authStore, roleLabel,
      form, errors, alertMsg, loadingData, loadingSimpan,
      onPhotoChange, simpanProfil,
      passwordForm, passwordErrors, loadingPassword,
      showPasswordLama, showPasswordBaru, showKonfirmasi, gantiPassword,
    };
  },
});
</script>