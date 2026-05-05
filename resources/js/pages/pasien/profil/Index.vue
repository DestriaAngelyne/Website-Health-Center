<template>
  <div class="container-xxl">
    <div class="d-flex align-items-center justify-content-between mb-7">
      <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 my-0">Profil Saya</h1>
      <router-link to="/pasien/profil/edit" class="btn btn-primary">
        <i class="ki-duotone ki-pencil fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
        Edit Profil
      </router-link>
    </div>

    <div v-if="loading" class="d-flex justify-content-center py-20">
      <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div v-else class="row g-5 g-xl-10">
      <div class="col-xl-4">
        <div class="card mb-5">
          <div class="card-body d-flex flex-column align-items-center py-10">

            <!-- Avatar: tampilkan foto jika ada, fallback ke inisial -->
            <div class="mb-5">
              <template v-if="authStore.fotoUrl">
                <img
                  :src="authStore.fotoUrl"
                  style="width:100px;height:100px;object-fit:cover;border-radius:50%;display:block;margin:0 auto;box-shadow:0 4px 16px rgba(0,0,0,.15);"
                  alt="Foto Profil"
                />
              </template>
              <div v-else class="symbol symbol-100px symbol-circle">
                <div class="symbol-label fs-1 fw-bold text-white" style="background:#1B84FF;">
                  {{ profil.nama_lengkap?.charAt(0).toUpperCase() }}
                </div>
              </div>
            </div>

            <div class="fw-bold fs-4 text-gray-900 mb-1">{{ profil.nama_lengkap }}</div>
            <div class="text-muted fs-6 mb-3">{{ profil.email }}</div>
            <span class="badge badge-light-primary fw-semibold px-4 py-2 mb-6">Pasien</span>
            <div class="separator separator-dashed w-100 mb-6"></div>
            <div class="w-100">
              <div class="d-flex align-items-center justify-content-between bg-light rounded p-3 mb-3">
                <span class="text-muted fs-7">No. RM</span>
                <span class="fw-bold fs-7">{{ profil.no_rm || '-' }}</span>
              </div>
              <div class="d-flex align-items-center justify-content-between bg-light rounded p-3 mb-3">
                <span class="text-muted fs-7">Jaminan</span>
                <span class="badge badge-light-success fw-semibold">{{ profil.jenis_jaminan?.toUpperCase() || '-' }}</span>
              </div>
              <div class="d-flex align-items-center justify-content-between bg-light rounded p-3">
                <span class="text-muted fs-7">No. BPJS</span>
                <span class="fw-bold fs-7">{{ profil.no_bpjs || '-' }}</span>
              </div>
            </div>
            <div class="separator separator-dashed w-100 my-6"></div>
            <router-link to="/pasien/profil/edit" class="btn btn-light-primary btn-sm w-100">
              <i class="ki-duotone ki-pencil fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
              Edit Profil
            </router-link>
          </div>
        </div>
      </div>

      <div class="col-xl-8">
        <div class="card mb-5">
          <div class="card-header">
            <div class="card-title">
              <h3 class="fw-bold m-0">Informasi Pribadi</h3>
            </div>
          </div>
          <div class="card-body py-6">
            <div class="row mb-4">
              <div class="col-sm-6 mb-4">
                <span class="text-muted fs-7 d-block mb-1">Nama Lengkap</span>
                <span class="fw-bold fs-6 text-gray-800">{{ profil.nama_lengkap || '-' }}</span>
              </div>
              <div class="col-sm-6 mb-4">
                <span class="text-muted fs-7 d-block mb-1">NIK</span>
                <span class="fw-bold fs-6 text-gray-800">{{ profil.nik || '-' }}</span>
              </div>
              <div class="col-sm-6 mb-4">
                <span class="text-muted fs-7 d-block mb-1">Tanggal Lahir</span>
                <span class="fw-bold fs-6 text-gray-800">{{ formatTanggal(profil.tanggal_lahir) }}</span>
              </div>
              <div class="col-sm-6 mb-4">
                <span class="text-muted fs-7 d-block mb-1">Jenis Kelamin</span>
                <span class="fw-bold fs-6 text-gray-800">
                  {{ profil.jenis_kelamin === 'L' ? 'Laki-laki' : profil.jenis_kelamin === 'P' ? 'Perempuan' : '-' }}
                </span>
              </div>
              <div class="col-sm-6 mb-4">
                <span class="text-muted fs-7 d-block mb-1">No. Telepon</span>
                <span class="fw-bold fs-6 text-gray-800">{{ profil.no_telepon || '-' }}</span>
              </div>
              <div class="col-sm-6 mb-4">
                <span class="text-muted fs-7 d-block mb-1">Email</span>
                <span class="fw-bold fs-6 text-gray-800">{{ profil.email || '-' }}</span>
              </div>
              <div class="col-12">
                <span class="text-muted fs-7 d-block mb-1">Alamat Lengkap</span>
                <span class="fw-bold fs-6 text-gray-800">{{ profil.alamat || '-' }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-title">
              <h3 class="fw-bold m-0">Informasi Medis</h3>
            </div>
          </div>
          <div class="card-body py-6">
            <div class="row">
              <div class="col-sm-4">
                <div class="bg-light-danger rounded p-4 text-center mb-4">
                  <i class="ki-duotone ki-drop fs-2x text-danger mb-2"><span class="path1"></span><span class="path2"></span></i>
                  <div class="text-muted fs-7 mb-1">Golongan Darah</div>
                  <div class="fw-bold fs-2 text-danger">{{ profil.golongan_darah || '-' }}</div>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="bg-light-success rounded p-4 text-center mb-4">
                  <i class="ki-duotone ki-shield-tick fs-2x text-success mb-2"><span class="path1"></span><span class="path2"></span></i>
                  <div class="text-muted fs-7 mb-1">Jenis Jaminan</div>
                  <div class="fw-bold fs-4 text-success text-uppercase">{{ profil.jenis_jaminan || '-' }}</div>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="bg-light-info rounded p-4 text-center mb-4">
                  <i class="ki-duotone ki-credit-cart fs-2x text-info mb-2"><span class="path1"></span><span class="path2"></span></i>
                  <div class="text-muted fs-7 mb-1">No. BPJS</div>
                  <div class="fw-bold fs-6 text-info">{{ profil.no_bpjs || '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, onMounted } from "vue";
import { PasienService } from "@/services/pasien.service";
import { useAuthStore } from "@/stores/auth.store";

export default defineComponent({
  name: "pasien-profil",
  setup() {
    const authStore = useAuthStore();
    const loading = ref(true);
    const profil  = reactive({
      nama_lengkap  : "",
      nik           : "",
      tanggal_lahir : "",
      jenis_kelamin : "",
      no_telepon    : "",
      email         : "",
      alamat        : "",
      golongan_darah: "",
      no_bpjs       : "",
      no_rm         : "",
      jenis_jaminan : "",
    });

    onMounted(async () => {
      try {
        const res = await PasienService.getProfil();
        Object.assign(profil, res.data.data);
      } catch (e) {
        console.error("Gagal memuat profil", e);
      } finally {
        loading.value = false;
      }
    });

    const formatTanggal = (tanggal: string) => {
      if (!tanggal) return "-";
      return new Date(tanggal).toLocaleDateString("id-ID", {
        day: "numeric", month: "long", year: "numeric",
      });
    };

    return { authStore, profil, loading, formatTanggal };
  },
});
</script>
