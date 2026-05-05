<template>
  <div class="d-flex flex-column gap-7">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Dashboard Loket</h1>
        <span class="text-gray-500 fw-semibold fs-7">
          {{ hariIni }} — Selamat datang, {{ authStore.user?.name }}
        </span>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-light-primary btn-sm" @click="bukaSesiOtomatis" :disabled="loadingBuka">
          <span v-if="!loadingBuka"><i class="ki-outline ki-calendar-add fs-4 me-1"></i>Buka Semua Sesi</span>
          <span v-else><span class="spinner-border spinner-border-sm me-2"></span>Membuka...</span>
        </button>
        <router-link to="/loket/pendaftaran" class="btn btn-primary btn-sm">
          <i class="ki-outline ki-plus fs-4 me-1"></i>Daftar Pasien
        </router-link>
      </div>
    </div>

    <!-- Alert -->
    <div v-if="alertMsg" class="alert py-3 d-flex align-items-center"
      :class="alertType === 'success' ? 'alert-success' : 'alert-danger'">
      <i class="ki-outline fs-2hx me-3"
        :class="alertType === 'success' ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger'"></i>
      <span class="fw-semibold">{{ alertMsg }}</span>
      <button class="btn-close ms-auto" @click="alertMsg = ''"></button>
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

    <!-- Sesi Hari Ini -->
    <div class="card card-flush">
      <div class="card-header pt-5 min-h-auto">
        <h3 class="card-title fw-bold fs-6">Sesi Antrian Hari Ini</h3>
        <div class="card-toolbar">
          <button class="btn btn-icon btn-light btn-sm" @click="fetchAll" title="Refresh">
            <i class="ki-outline ki-arrows-circle fs-4"></i>
          </button>
        </div>
      </div>
      <div class="card-body pt-3">
        <div v-if="loadingSesi" class="d-flex justify-content-center py-8">
          <span class="spinner-border text-primary"></span>
        </div>
        <div v-else-if="sesiList.length === 0" class="text-center py-8 text-gray-400">
          <i class="ki-outline ki-calendar fs-5x mb-3"></i>
          <p class="fw-semibold">Belum ada sesi dibuka hari ini</p>
          <button class="btn btn-light-primary btn-sm mt-2" @click="bukaSesiOtomatis">
            Buka Semua Sesi Otomatis
          </button>
        </div>
        <div v-else class="row g-4">
          <div v-for="s in sesiList" :key="s.id" class="col-md-4 col-lg-3">
            <div class="border rounded-2 p-4 h-100"
              :class="isAktif(s) ? 'border-success bg-light-success' : 'border-gray-200 bg-light'">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="fw-bold text-gray-900 fs-7">{{ s.poli }}</div>
                <span class="badge fs-9" :class="isAktif(s) ? 'badge-light-success' : 'badge-light-secondary'">
                  {{ s.status }}
                </span>
              </div>
              <div class="text-gray-600 fs-8 mb-1">
                <i class="ki-outline ki-profile-user fs-7 me-1"></i>{{ s.dokter }}
              </div>
              <div class="text-gray-500 fs-8 mb-3">
                <i class="ki-outline ki-time fs-7 me-1"></i>{{ s.jam_buka }} – {{ s.jam_tutup }}
              </div>
              <!-- Kuota bar -->
              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="flex-grow-1 bg-gray-200 rounded" style="height:6px">
                  <div class="rounded" :class="kuotaColor(s)"
                    :style="`width:${Math.min((s.terpakai/s.kuota)*100,100)}%;height:6px;transition:width .3s`"></div>
                </div>
                <span class="text-gray-600 fs-9 fw-semibold flex-shrink-0">{{ s.terpakai }}/{{ s.kuota }}</span>
              </div>
              <div class="d-flex gap-2">
                <button v-if="isAktif(s)" class="btn btn-sm btn-light-danger flex-grow-1"
                  @click="tutupSesi(s.id)">
                  <i class="ki-outline ki-cross-circle fs-6 me-1"></i>Tutup
                </button>
                <span v-else class="text-gray-400 fs-8 w-100 text-center">Sesi ditutup</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Antrian Per Poli -->
    <div v-if="antrianPerPoli.length" class="card card-flush">
      <div class="card-header pt-5 min-h-auto">
        <h3 class="card-title fw-bold fs-6">Antrian Per Poli Hari Ini</h3>
      </div>
      <div class="card-body pt-3 p-0">
        <table class="table table-row-dashed align-middle gs-5 gy-3 mb-0">
          <thead>
            <tr class="fw-bold text-gray-500 fs-8 text-uppercase border-bottom">
              <th>Poliklinik</th>
              <th class="text-center">Total Antrian</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in antrianPerPoli" :key="p.poli">
              <td class="fw-semibold text-gray-800">{{ p.poli }}</td>
              <td class="text-center">
                <span class="badge badge-light-primary fw-bold fs-7">{{ p.total }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth.store'

const authStore    = useAuthStore()
const loadingSesi  = ref(false)
const loadingBuka  = ref(false)
const alertMsg     = ref('')
const alertType    = ref<'success'|'danger'>('success')

const hariIni      = ref('')
const statistik    = ref<any>({})
const sesiList     = ref<any[]>([])
const antrianPerPoli = ref<any[]>([])

const statList = computed(() => [
  { label: 'Total Antrian', value: statistik.value.total_antrian ?? 0, icon: 'ki-notepad-edit', iconColor: 'text-primary',  bg: '#f1f5ff' },
  { label: 'Menunggu',      value: statistik.value.menunggu      ?? 0, icon: 'ki-time',          iconColor: 'text-warning',  bg: '#fffbeb' },
  { label: 'Dilayani',      value: statistik.value.dilayani      ?? 0, icon: 'ki-check-circle',  iconColor: 'text-success',  bg: '#f0fdf4' },
  { label: 'Sesi Aktif',    value: statistik.value.sesi_aktif    ?? 0, icon: 'ki-calendar',      iconColor: 'text-info',     bg: '#f0f9ff' },
])

const isAktif   = (s: any) => ['aktif', 'buka'].includes(s.status)
const kuotaColor = (s: any) => {
  const pct = s.terpakai / s.kuota
  if (pct >= 0.9) return 'bg-danger'
  if (pct >= 0.6) return 'bg-warning'
  return 'bg-success'
}

async function fetchDashboard() {
  const res = await api.get('/loket/dashboard')
  statistik.value    = res.data.statistik
  antrianPerPoli.value = res.data.antrian_per_poli
  hariIni.value      = res.data.hari_ini
}

async function fetchSesi() {
  loadingSesi.value = true
  try {
    const res = await api.get('/loket/sesi')
    sesiList.value = res.data.data
  } finally {
    loadingSesi.value = false
  }
}

async function fetchAll() {
  await Promise.all([fetchDashboard(), fetchSesi()])
}

async function bukaSesiOtomatis() {
  loadingBuka.value = true
  try {
    const res = await api.post('/loket/sesi/buka-semua')
    alertMsg.value  = res.data.message
    alertType.value = 'success'
    fetchAll()
  } catch (e: any) {
    alertMsg.value  = e.response?.data?.message ?? 'Gagal membuka sesi.'
    alertType.value = 'danger'
  } finally {
    loadingBuka.value = false
  }
}

async function tutupSesi(id: number) {
  if (!confirm('Tutup sesi ini?')) return
  try {
    await api.patch(`/loket/sesi/${id}/tutup`)
    alertMsg.value  = 'Sesi berhasil ditutup.'
    alertType.value = 'success'
    fetchAll()
  } catch (e: any) {
    alertMsg.value  = e.response?.data?.message ?? 'Gagal menutup sesi.'
    alertType.value = 'danger'
  }
}6

onMounted(fetchAll)
</script>
