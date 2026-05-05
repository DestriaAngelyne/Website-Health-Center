<template>
  <div class="d-flex flex-column gap-7">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Dashboard Dokter</h1>
        <span class="text-gray-500 fw-semibold fs-7">Selamat datang, {{ data?.dokter?.nama }}</span>
      </div>
      <router-link to="/dokter/antrian" class="btn btn-primary btn-sm">
        <i class="ki-outline ki-people me-1"></i>Lihat Antrian
      </router-link>
    </div>

    <div v-if="loading" class="d-flex justify-content-center py-12">
      <span class="spinner-border text-primary"></span>
    </div>

    <template v-else-if="data">

      <!-- Info Dokter + Sesi -->
      <div class="row g-5">
        <div class="col-md-6">
          <div class="card card-flush h-100">
            <div class="card-body d-flex align-items-center gap-5 py-7">
              <div class="symbol symbol-70px">
                <div class="symbol-label fw-bold fs-1"
                  style="background:linear-gradient(135deg,#009ef7,#50cd89);color:white">
                  {{ data.dokter.nama?.charAt(0) }}
                </div>
              </div>
              <div>
                <div class="fw-bolder text-gray-900 fs-4">{{ data.dokter.nama }}</div>
                <div class="text-gray-500 fs-7">{{ data.dokter.spesialisasi ?? 'Dokter Umum' }}</div>
                <div class="badge badge-light-primary mt-2 fs-8">{{ data.dokter.poli }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card card-flush h-100" :class="data.sesi ? 'border border-success' : 'border border-warning'">
            <div class="card-body py-7">
              <div class="d-flex align-items-center gap-3 mb-3">
                <i class="ki-outline ki-calendar fs-2" :class="data.sesi ? 'text-success' : 'text-warning'"></i>
                <span class="fw-bold fs-6">Sesi Hari Ini</span>
                <span class="badge ms-auto" :class="data.sesi ? 'badge-light-success' : 'badge-light-warning'">
                  {{ data.sesi ? data.sesi.status : 'Belum Ada Sesi' }}
                </span>
              </div>
              <div v-if="data.sesi">
                <div class="fs-6 text-gray-800 fw-semibold">
                  {{ data.sesi.jam_buka }} – {{ data.sesi.jam_tutup }}
                </div>
                <div class="text-gray-500 fs-7 mt-1">Kuota: {{ data.sesi.kuota }} pasien</div>
              </div>
              <div v-else class="text-gray-400 fs-7">Hubungi petugas loket untuk membuka sesi</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistik -->
      <div class="row g-4">
        <div class="col-6 col-md-3" v-for="s in statList" :key="s.label">
          <div class="card card-flush h-100">
            <div class="card-body d-flex align-items-center gap-4 py-6 px-5">
              <div class="symbol symbol-50px flex-shrink-0">
                <div class="symbol-label rounded" :style="`background:${s.bg}`">
                  <i class="ki-outline fs-2" :class="`${s.icon} ${s.iconColor}`"></i>
                </div>
              </div>
              <div>
                <div class="fw-bolder fs-2 text-gray-900 lh-1">{{ s.value }}</div>
                <div class="text-gray-500 fw-semibold fs-8 mt-1">{{ s.label }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pasien Aktif -->
      <div v-if="data.pasien_aktif" class="card card-flush border border-primary">
        <div class="card-body d-flex align-items-center gap-5 py-6">
          <div class="symbol symbol-55px flex-shrink-0">
            <div class="symbol-label bg-light-primary">
              <i class="ki-outline ki-profile-user fs-2 text-primary"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <div class="text-gray-400 fs-8 mb-1">PASIEN SEDANG DILAYANI</div>
            <div class="fw-bolder text-gray-900 fs-5">{{ data.pasien_aktif.pasien }}</div>
            <div class="text-primary fw-bold fs-6">No. {{ data.pasien_aktif.nomor_display }}</div>
          </div>
          <router-link :to="`/dokter/rekam-medis/${data.pasien_aktif.id}`" class="btn btn-primary">
            <i class="ki-outline ki-notepad-edit me-1"></i>Buka Rekam Medis
          </router-link>
        </div>
      </div>

    </template>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(false)
const data    = ref<any>(null)

const statList = computed(() => [
  { label: 'Total Pasien',  value: data.value?.statistik?.total_pasien ?? 0, icon: 'ki-people',        iconColor: 'text-primary', bg: '#f1f5ff' },
  { label: 'Menunggu',      value: data.value?.statistik?.menunggu      ?? 0, icon: 'ki-time',          iconColor: 'text-warning', bg: '#fffbeb' },
  { label: 'Dilayani',      value: data.value?.statistik?.dilayani      ?? 0, icon: 'ki-heart-circle',  iconColor: 'text-info',    bg: '#f0f9ff' },
  { label: 'Selesai',       value: data.value?.statistik?.selesai       ?? 0, icon: 'ki-check-circle',  iconColor: 'text-success', bg: '#f0fdf4' },
])

async function fetchDashboard() {
  loading.value = true
  try {
    const res = await api.get('/dokter/dashboard')
    data.value = res.data
  } finally {
    loading.value = false
  }
}

onMounted(fetchDashboard)
</script>
