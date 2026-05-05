<template>
  <div class="dashboard-wrapper">
    <div class="mb-8 d-flex align-items-center justify-content-between">
      <div>
        <h1 class="fw-bold text-white">Dashboard Hari Ini</h1>
        <p class="text-gray-500">Ringkasan aktivitas antrean tanggal {{ tanggalString || '...' }}</p>
      </div>
      <button @click="fetchDashboardData" class="btn btn-sm btn-light-primary" :disabled="loading">
        <i :class="loading ? 'fa fa-spinner fa-spin' : 'ki-outline ki-arrows-circle'" class="fs-6 me-1"></i>
        {{ loading ? 'Memuat...' : 'Refresh Data' }}
      </button>
    </div>

    <div class="row g-6 mb-8">
      <div v-for="stat in statCards" :key="stat.label" class="col-md-3">
        <div class="card bg-dark-card border-0 shadow-sm">
          <div class="card-body d-flex align-items-center gap-4">
            <div :class="`symbol symbol-50px symbol-light-${stat.color}`">
              <div class="symbol-label">
                <i :class="`ki-outline ${stat.icon} fs-1 text-${stat.color}`"></i>
              </div>
            </div>
            <div>
              <div class="fs-2hx fw-bold text-white">{{ stat.value }}</div>
              <div class="fw-semibold text-gray-500">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-6">
      <div class="col-lg-8">
        <div class="card bg-dark-card border-0">
          <div class="card-header pt-5">
            <h3 class="card-title fw-bold text-white">Grafik Kunjungan Poli</h3>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-center align-items-center h-300px bg-light-dark rounded-3 border border-dashed border-gray-700">
              <span class="text-gray-600 fw-bold">Grafik tersinkronisasi dengan database</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card bg-dark-card border-0">
          <div class="card-header pt-5">
            <h3 class="card-title fw-bold text-white">Antrean Per Poli</h3>
          </div>
          <div class="card-body">
            <div v-if="antrianPerPoli.length === 0" class="text-gray-500 text-center py-10">
              Belum ada antrean hari ini.
            </div>
            <div v-for="(poli, index) in antrianPerPoli" :key="index" class="d-flex align-items-center mb-7 bg-light-dark p-4 rounded-3">
              <div class="flex-grow-1">
                <span class="text-white fw-bold fs-6 d-block">{{ poli.nama }}</span>
                <span class="text-gray-500 d-block fw-semibold fs-8">Aktif</span>
              </div>
              <div class="badge badge-light-primary fw-bold fs-7">{{ poli.jumlah }} Pasien</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const statistik = ref({})
const tanggalString = ref('')
const antrianPerPoli = ref([])
const loading = ref(false)

const statCards = computed(() => [
  { label: 'Total Antrean', value: statistik.value.total_antrian ?? 0, icon: 'ki-people', color: 'primary' },
  { label: 'Menunggu', value: statistik.value.menunggu ?? 0, icon: 'ki-timer', color: 'warning' },
  { label: 'Selesai', value: statistik.value.selesai ?? 0, icon: 'ki-check-circle', color: 'success' },
  { label: 'Batal', value: statistik.value.batal ?? 0, icon: 'ki-cross-circle', color: 'danger' },
])

async function fetchDashboardData() {
  loading.value = true
  try {
    const res = await api.get('/perawat/dashboard')
    statistik.value = res.data.statistik || {}
    tanggalString.value = res.data.hari_ini || ''
    antrianPerPoli.value = res.data.antrian_per_poli || []
  } catch (error) {
    console.error("Error Dashboard:", error)
  } finally {
    loading.value = false
  }
}

onMounted(() => { fetchDashboardData() })
</script>

<style scoped>
.bg-dark-card { background-color: #1e1e2d; }
.bg-light-dark { background-color: rgba(255, 255, 255, 0.03); }
</style>
