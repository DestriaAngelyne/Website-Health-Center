<template>
  <div class="d-flex flex-column gap-7">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Pasien Hari Ini</h1>
        <span class="text-gray-500 fw-semibold fs-7">Daftar antrian yang ditujukan ke Anda</span>
      </div>
      <button class="btn btn-icon btn-light btn-sm" @click="fetchData" title="Refresh">
        <i class="ki-outline ki-arrows-circle fs-4" :class="{ spin: loading }"></i>
      </button>
    </div>

    <!-- Quick filter -->
    <div class="d-flex gap-2 flex-wrap">
      <button v-for="f in filterList" :key="f.val"
        class="btn btn-sm"
        :class="filterStatus === f.val ? 'btn-primary' : 'btn-light'"
        @click="filterStatus = f.val; fetchData()">
        {{ f.label }}
        <span class="badge badge-circle ms-2 fs-9"
          :class="filterStatus === f.val ? 'badge-light' : 'badge-light-primary'">
          {{ f.count }}
        </span>
      </button>
    </div>

    <!-- Alert -->
    <div v-if="alertMsg" class="alert py-3 d-flex align-items-center gap-3"
      :class="alertType === 'success' ? 'alert-success' : 'alert-danger'">
      <i class="ki-outline fs-2hx"
        :class="alertType === 'success' ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger'"></i>
      <span class="fw-semibold flex-grow-1">{{ alertMsg }}</span>
      <button class="btn-close" @click="alertMsg = ''"></button>
    </div>

    <div v-if="loading" class="d-flex justify-content-center py-10">
      <span class="spinner-border text-primary"></span>
    </div>

    <div v-else-if="antrianList.length === 0" class="card card-flush">
      <div class="card-body text-center py-12 text-gray-400">
        <i class="ki-outline ki-people fs-5x mb-3"></i>
        <p class="fw-semibold">Tidak ada antrian</p>
      </div>
    </div>

    <div v-else class="d-flex flex-column gap-4">
      <div v-for="a in antrianList" :key="a.id"
        class="card card-flush"
        :class="['dipanggil','dilayani'].includes(a.status) ? 'border border-primary' : ''">
        <div class="card-body py-5">
          <div class="d-flex align-items-center gap-4">

            <!-- Nomor -->
            <div class="text-center flex-shrink-0" style="min-width:70px">
              <div class="fw-bolder text-primary" style="font-size:32px;line-height:1;font-family:serif">
                {{ a.nomor_display }}
              </div>
              <span class="badge fs-9 mt-1" :class="statusBadge(a.status)">{{ statusLabel(a.status) }}</span>
            </div>

            <div class="separator separator-vertical h-60px"></div>

            <!-- Info Pasien -->
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="fw-bolder text-gray-900 fs-6">{{ a.pasien }}</span>
                <span class="badge fs-9"
                  :class="a.jenis_pasien === 'bpjs' ? 'badge-light-success' : 'badge-light-info'">
                  {{ a.jenis_pasien?.toUpperCase() }}
                </span>
                <span v-if="a.jenis_kunjungan" class="badge badge-light-secondary fs-9">
                  {{ a.jenis_kunjungan }}
                </span>
              </div>
              <div class="d-flex gap-4 text-gray-500 fs-8">
                <span><i class="ki-outline ki-medal-star me-1"></i>{{ a.no_rm }}</span>
                <span><i class="ki-outline ki-profile-user me-1"></i>{{ a.umur }} · {{ a.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                <span v-if="a.waktu_daftar"><i class="ki-outline ki-time me-1"></i>Daftar {{ a.waktu_daftar }}</span>
              </div>
              <div v-if="a.catatan" class="mt-2 text-gray-400 fs-8">
                <i class="ki-outline ki-notepad me-1"></i>{{ a.catatan }}
              </div>
            </div>

            <!-- Aksi -->
            <div class="d-flex gap-2 flex-shrink-0">
              <template v-if="a.status === 'menunggu'">
                <button class="btn btn-light-primary btn-sm" @click="panggil(a)">
                  <i class="ki-outline ki-notification-on me-1"></i>Panggil
                </button>
              </template>
              <template v-else-if="a.status === 'dipanggil'">
                <button class="btn btn-primary btn-sm" @click="mulai(a)">
                  <i class="ki-outline ki-arrow-right me-1"></i>Mulai Layani
                </button>
                <button class="btn btn-light btn-sm" @click="panggil(a)">
                  <i class="ki-outline ki-notification-on me-1"></i>Panggil Lagi
                </button>
              </template>
              <template v-else-if="a.status === 'dilayani'">
                <router-link :to="`/dokter/rekam-medis/${a.id}`" class="btn btn-primary btn-sm">
                  <i class="ki-outline ki-notepad-edit me-1"></i>Rekam Medis
                </router-link>
              </template>
              <template v-else-if="a.status === 'selesai'">
                <router-link :to="`/dokter/rekam-medis/${a.id}`" class="btn btn-light-success btn-sm">
                  <i class="ki-outline ki-eye me-1"></i>Lihat
                </router-link>
              </template>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'

const loading      = ref(false)
const alertMsg     = ref('')
const alertType    = ref<'success'|'danger'>('success')
const antrianRaw   = ref<any[]>([])
const filterStatus = ref('')
let   timer: any   = null

const antrianList = computed(() =>
  filterStatus.value
    ? antrianRaw.value.filter(a => a.status === filterStatus.value)
    : antrianRaw.value
)

const filterList = computed(() => [
  { val: '',          label: 'Semua',    count: antrianRaw.value.length },
  { val: 'menunggu',  label: 'Menunggu', count: antrianRaw.value.filter(a => a.status === 'menunggu').length },
  { val: 'dipanggil', label: 'Dipanggil',count: antrianRaw.value.filter(a => a.status === 'dipanggil').length },
  { val: 'dilayani',  label: 'Dilayani', count: antrianRaw.value.filter(a => a.status === 'dilayani').length },
  { val: 'selesai',   label: 'Selesai',  count: antrianRaw.value.filter(a => a.status === 'selesai').length },
])

const statusBadge = (s: string) => ({
  menunggu : 'badge-light-warning',
  dipanggil: 'badge-light-info',
  dilayani : 'badge-light-primary',
  selesai  : 'badge-light-success',
  dilewati : 'badge-light-secondary',
  batal    : 'badge-light-danger',
}[s] ?? 'badge-light-secondary')

const statusLabel = (s: string) => ({
  menunggu : 'Menunggu',
  dipanggil: 'Dipanggil',
  dilayani : 'Dilayani',
  selesai  : 'Selesai',
  dilewati : 'Dilewati',
  batal    : 'Batal',
}[s] ?? s)

async function fetchData() {
  loading.value = true
  try {
    const res = await api.get('/dokter/antrian')
    antrianRaw.value = res.data.data
  } finally {
    loading.value = false
  }
}

async function panggil(a: any) {
  try {
    await api.patch(`/dokter/antrian/${a.id}/panggil`)
    alertMsg.value  = `Pasien ${a.pasien} dipanggil.`
    alertType.value = 'success'
    fetchData()
  } catch (e: any) {
    alertMsg.value  = e.response?.data?.message ?? 'Gagal memanggil pasien.'
    alertType.value = 'danger'
  }
}

async function mulai(a: any) {
  try {
    const res = await api.patch(`/dokter/antrian/${a.id}/mulai`)
    alertMsg.value  = 'Sesi layanan dimulai.'
    alertType.value = 'success'
    fetchData()
  } catch (e: any) {
    alertMsg.value  = e.response?.data?.message ?? 'Gagal memulai layanan.'
    alertType.value = 'danger'
  }
}

onMounted(() => {
  fetchData()
  timer = setInterval(fetchData, 30000)
})
onUnmounted(() => clearInterval(timer))
</script>

<style scoped>
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
