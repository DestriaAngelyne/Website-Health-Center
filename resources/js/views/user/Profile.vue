<template>
  <div class="py-10">
    <div class="mb-10">
      <h1 class="fw-bold text-gray-900">Profile Saya</h1>
      <div class="text-gray-500 fw-semibold fs-6">Update informasi profile kamu</div>
    </div>

    <div class="card">
      <div class="card-header border-0 pt-6">
        <div class="card-title">
          <h3 class="fw-bold text-gray-900">Edit Profile</h3>
        </div>
      </div>
      <div class="card-body pt-0">
        <form @submit.prevent="onSubmit">
          <div class="fv-row mb-7">
            <label class="form-label fw-bold text-gray-900 fs-6">Nama Lengkap</label>
            <input
              type="text"
              class="form-control form-control-solid"
              v-model="form.name"
              placeholder="Masukkan nama lengkap"
            />
          </div>

          <div class="fv-row mb-7">
            <label class="form-label fw-bold text-gray-900 fs-6">Email</label>
            <input
              type="email"
              class="form-control form-control-solid"
              v-model="form.email"
              placeholder="Masukkan email"
              disabled
            />
            <div class="text-muted fs-7 mt-1">Email tidak bisa diubah</div>
          </div>

          <div class="fv-row mb-7">
            <label class="form-label fw-bold text-gray-900 fs-6">Password Baru</label>
            <input
              type="password"
              class="form-control form-control-solid"
              v-model="form.password"
              placeholder="Kosongkan jika tidak ingin mengubah password"
            />
          </div>

          <div class="fv-row mb-10">
            <label class="form-label fw-bold text-gray-900 fs-6">Konfirmasi Password</label>
            <input
              type="password"
              class="form-control form-control-solid"
              v-model="form.password_confirmation"
              placeholder="Ulangi password baru"
            />
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <span v-if="!loading">Simpan Perubahan</span>
              <span v-else>
                Menyimpan...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive } from "vue";
import { useAuthStore } from "@/stores/auth";
import Swal from "sweetalert2/dist/sweetalert2.js";

export default defineComponent({
  name: "user-profile",
  setup() {
    const userStore = useAuthStore();
    const loading = ref(false);

    const form = reactive({
      name: userStore.user.name,
      email: userStore.user.email,
      password: "",
      password_confirmation: "",
    });

    const onSubmit = async () => {
      loading.value = true;
      setTimeout(() => {
        loading.value = false;
        Swal.fire({
          text: "Profile berhasil diupdate!",
          icon: "success",
          buttonsStyling: false,
          confirmButtonText: "Ok!",
          heightAuto: false,
          customClass: {
            confirmButton: "btn fw-semibold btn-light-primary",
          },
        });
      }, 1000);
    };

    return {
      form,
      loading,
      onSubmit,
      userStore,
    };
  },
});
</script>
