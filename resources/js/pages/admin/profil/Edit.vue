<template>
  <div class="container-xxl">
    <!--begin::Page Title-->
    <div class="d-flex align-items-center mb-7">
      <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 my-0">
        Edit Profil Saya
      </h1>
    </div>
    <!--end::Page Title-->

    <div class="row g-5 g-xl-10">
      <!--begin::Col Left - Foto Profil-->
      <div class="col-xl-4">
        <div class="card mb-5">
          <div class="card-body d-flex flex-column align-items-center py-10">
            <!--begin::Avatar-->
            <div class="symbol symbol-100px symbol-circle mb-5 position-relative">
              <img
                v-if="previewPhoto"
                :src="previewPhoto"
                alt="Foto Profil"
                class="w-100 h-100 rounded-circle object-fit-cover"
              />
              <div
                v-else
                class="symbol-label fs-1 fw-bold text-white"
                :style="{ background: '#1B84FF' }"
              >
                {{ form.nama_lengkap.charAt(0).toUpperCase() }}
              </div>
              <!--begin::Edit button-->
              <label
                for="upload_photo"
                class="btn btn-icon btn-circle btn-color-white w-25px h-25px bg-primary position-absolute bottom-0 end-0"
                style="cursor: pointer"
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
              <!--end::Edit button-->
            </div>
            <!--end::Avatar-->

            <div class="fw-bold fs-4 text-gray-900">{{ form.nama_lengkap }}</div>
            <div class="text-muted fs-6 mb-3">{{ form.email }}</div>
            <span class="badge badge-light-primary fw-semibold px-4 py-2">Pasien</span>

            <div class="separator separator-dashed my-6 w-100"></div>

            <div class="w-100">
              <div class="d-flex justify-content-between mb-3">
                <span class="text-muted fs-7">No. RM</span>
                <span class="fw-bold fs-7 text-gray-800">{{ form.no_rm }}</span>
              </div>
              <div class="d-flex justify-content-between mb-3">
                <span class="text-muted fs-7">Jaminan</span>
                <span class="badge badge-light-success fw-semibold">{{ form.jenis_jaminan?.toUpperCase() }}</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted fs-7">No. BPJS</span>
                <span class="fw-bold fs-7 text-gray-800">{{ form.no_bpjs || '-' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--end::Col Left-->

      <!--begin::Col Right - Form-->
      <div class="col-xl-8">

        <!--begin::Card Data Diri-->
        <div class="card mb-5">
          <div class="card-header">
            <div class="card-title">
              <h3 class="fw-bold m-0">Data Diri</h3>
            </div>
          </div>
          <div class="card-body">
            <!--begin::Alert-->
            <div v-if="successMessage" class="alert alert-success d-flex align-items-center mb-6">
              <i class="ki-duotone ki-shield-tick fs-2hx text-success me-3">
                <span class="path1"></span><span class="path2"></span>
              </i>
              <div class="d-flex flex-column">
                <span class="fw-semibold">{{ successMessage }}</span>
              </div>
            </div>
            <!--end::Alert-->

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
                <label class="form-label required fw-semibold">NIK</label>
                <input
                  v-model="form.nik"
                  type="text"
                  class="form-control form-control-solid"
                  placeholder="16 digit NIK"
                  maxlength="16"
                  :class="{ 'is-invalid': errors.nik }"
                />
                <div v-if="errors.nik" class="invalid-feedback">{{ errors.nik }}</div>
              </div>
            </div>

            <div class="row mb-5">
              <div class="col-md-6">
                <label class="form-label required fw-semibold">Tanggal Lahir</label>
                <input
                  v-model="form.tanggal_lahir"
                  type="date"
                  class="form-control form-control-solid"
                  :class="{ 'is-invalid': errors.tanggal_lahir }"
                />
                <div v-if="errors.tanggal_lahir" class="invalid-feedback">{{ errors.tanggal_lahir }}</div>
              </div>
              <div class="col-md-6">
                <label class="form-label required fw-semibold">Jenis Kelamin</label>
                <div class="d-flex gap-5 mt-3">
                  <div class="form-check form-check-custom form-check-solid">
                    <input
                      v-model="form.jenis_kelamin"
                      class="form-check-input"
                      type="radio"
                      value="L"
                      id="jk_laki"
                    />
                    <label class="form-check-label fw-semibold" for="jk_laki">Laki-laki</label>
                  </div>
                  <div class="form-check form-check-custom form-check-solid">
                    <input
                      v-model="form.jenis_kelamin"
                      class="form-check-input"
                      type="radio"
                      value="P"
                      id="jk_perempuan"
                    />
                    <label class="form-check-label fw-semibold" for="jk_perempuan">Perempuan</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mb-5">
              <div class="col-md-6">
                <label class="form-label required fw-semibold">No. Telepon</label>
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

            <div class="mb-5">
              <label class="form-label required fw-semibold">Alamat Lengkap</label>
              <textarea
                v-model="form.alamat"
                class="form-control form-control-solid"
                rows="3"
                placeholder="Masukkan alamat lengkap"
                :class="{ 'is-invalid': errors.alamat }"
              ></textarea>
              <div v-if="errors.alamat" class="invalid-feedback">{{ errors.alamat }}</div>
            </div>

            <div class="row mb-5">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Golongan Darah</label>
                <select v-model="form.golongan_darah" class="form-select form-select-solid">
                  <option value="">-- Pilih --</option>
                  <option value="A">A</option>
                  <option value="B">B</option>
                  <option value="AB">AB</option>
                  <option value="O">O</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">No. BPJS</label>
                <input
                  v-model="form.no_bpjs"
                  type="text"
                  class="form-control form-control-solid"
                  placeholder="Nomor BPJS (jika ada)"
                />
              </div>
            </div>

            <div class="d-flex justify-content-end">
              <button
                type="button"
                class="btn btn-primary"
                @click="simpanProfil"
                :disabled="loadingSimpan"
              >
                <span v-if="!loadingSimpan">
                  <i class="ki-duotone ki-check fs-4 me-1">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                  Simpan Perubahan
                </span>
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
                <span v-if="!loadingPassword">
                  <i class="ki-duotone ki-lock fs-4 me-1">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                  Ganti Password
                </span>
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
</template>

<script lang="ts">
import { defineComponent, ref, reactive } from "vue";

export default defineComponent({
  name: "pasien-edit-profil",
  setup() {
    // Data form profil
    const form = reactive({
      nama_lengkap: "Destria Angelyne",
      nik: "3271234567890001",
      tanggal_lahir: "1995-05-15",
      jenis_kelamin: "P",
      no_telepon: "081234567890",
      email: "angelynedestria@gmail.com",
      alamat: "Jl. Contoh No. 1, Jakarta",
      golongan_darah: "A",
      no_bpjs: "0001234567890",
      no_rm: "RM-2026-00001",
      jenis_jaminan: "bpjs",
    });

    const errors = reactive<Record<string, string>>({});
    const successMessage = ref("");
    const loadingSimpan = ref(false);
    const previewPhoto = ref<string | null>(null);

    // Data form password
    const passwordForm = reactive({
      password_lama: "",
      password_baru: "",
      konfirmasi_password: "",
    });

    const passwordErrors = reactive<Record<string, string>>({});
    const loadingPassword = ref(false);
    const showPasswordLama = ref(false);
    const showPasswordBaru = ref(false);
    const showKonfirmasi = ref(false);

    // Handle upload foto
    const onPhotoChange = (e: Event) => {
      const target = e.target as HTMLInputElement;
      if (target.files && target.files[0]) {
        const reader = new FileReader();
        reader.onload = (ev) => {
          previewPhoto.value = ev.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
      }
    };

    // Validasi form profil
    const validasiProfil = () => {
      Object.keys(errors).forEach((key) => delete errors[key]);
      let valid = true;

      if (!form.nama_lengkap) { errors.nama_lengkap = "Nama lengkap wajib diisi"; valid = false; }
      if (!form.nik || form.nik.length !== 16) { errors.nik = "NIK harus 16 digit"; valid = false; }
      if (!form.tanggal_lahir) { errors.tanggal_lahir = "Tanggal lahir wajib diisi"; valid = false; }
      if (!form.no_telepon) { errors.no_telepon = "No. telepon wajib diisi"; valid = false; }
      if (!form.email) { errors.email = "Email wajib diisi"; valid = false; }
      if (!form.alamat) { errors.alamat = "Alamat wajib diisi"; valid = false; }

      return valid;
    };

    // Simpan profil
    const simpanProfil = async () => {
      if (!validasiProfil()) return;
      loadingSimpan.value = true;
      // TODO: panggil API update profil
      // await PasienService.updateProfil(form)
      setTimeout(() => {
        loadingSimpan.value = false;
        successMessage.value = "Profil berhasil diperbarui!";
        setTimeout(() => { successMessage.value = ""; }, 3000);
      }, 1000);
    };

    // Validasi form password
    const validasiPassword = () => {
      Object.keys(passwordErrors).forEach((key) => delete passwordErrors[key]);
      let valid = true;

      if (!passwordForm.password_lama) { passwordErrors.password_lama = "Password lama wajib diisi"; valid = false; }
      if (!passwordForm.password_baru || passwordForm.password_baru.length < 8) { passwordErrors.password_baru = "Password baru minimal 8 karakter"; valid = false; }
      if (passwordForm.password_baru !== passwordForm.konfirmasi_password) { passwordErrors.konfirmasi_password = "Konfirmasi password tidak cocok"; valid = false; }

      return valid;
    };

    // Ganti password
    const gantiPassword = async () => {
      if (!validasiPassword()) return;
      loadingPassword.value = true;
      // TODO: panggil API ganti password
      // await AuthService.gantiPassword(passwordForm)
      setTimeout(() => {
        loadingPassword.value = false;
        passwordForm.password_lama = "";
        passwordForm.password_baru = "";
        passwordForm.konfirmasi_password = "";
        successMessage.value = "Password berhasil diubah!";
        setTimeout(() => { successMessage.value = ""; }, 3000);
      }, 1000);
    };

    return {
      form,
      errors,
      successMessage,
      loadingSimpan,
      previewPhoto,
      onPhotoChange,
      simpanProfil,
      passwordForm,
      passwordErrors,
      loadingPassword,
      showPasswordLama,
      showPasswordBaru,
      showKonfirmasi,
      gantiPassword,
    };
  },
});
</script>
