<template>
  <div class="d-flex flex-column gap-7">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Monitor Antrian</h1>
        <span class="text-gray-500 fw-semibold fs-7">Kelola antrian hari ini</span>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-icon btn-light btn-sm" @click="fetchData" title="Refresh">
          <i class="ki-outline ki-arrows-circle fs-4" :class="{ 'spin': loading }"></i>
        </button>
        <router-link to="/loket/pendaftaran" class="btn btn-primary btn-sm">
          <i class="ki-outline ki-plus me-1"></i>Daftar Pasien
        </router-link>
      </div>
    </div>

    <!-- Alert -->
    <div v-if="alertMsg" class="alert py-3 d-flex align-items-center gap-3"
      :class="alertType === 'success' ? 'alert-success' : 'alert-danger'">
      <i class="ki-outline fs-2hx"
        :class="alertType === 'success' ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger'"></i>
      <span class="fw-semibold flex-grow-1">{{ alertMsg }}</span>
      <button class="btn-close" @click="alertMsg = ''"></button>
    </div>

    <!-- Filter -->
    <div class="card card-flush">
      <div class="card-body py-4">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label fw-semibold fs-7 mb-1">Cari Pasien</label>
            <div class="position-relative">
              <i class="ki-outline ki-magnifier position-absolute fs-4"
                style="left:12px;top:50%;transform:translateY(-50%);color:#99a1b7"></i>
              <input v-model="search" @input="fetchData" type="text"
                class="form-control form-control-sm form-control-solid ps-10"
                placeholder="Nama atau No. RM..."/>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold fs-7 mb-1">Filter Poli</label>
            <select v-model="filterPoli" @change="fetchData"
              class="form-select form-select-sm form-select-solid">
              <option value="">Semua Poli</option>
              <option v-for="s in sesiList" :key="s.id" :value="s.poli_id">{{ s.poli }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold fs-7 mb-1">Status</label>
            <select v-model="filterStatus" @change="fetchData"
              class="form-select form-select-sm form-select-solid">
              <option value="">Semua Status</option>
              <option value="menunggu">Menunggu</option>
              <option value="dipanggil">Dipanggil</option>
              <option value="dilayani">Dilayani</option>
              <option value="selesai">Selesai</option>
              <option value="batal">Batal</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistik cepat -->
    <div class="d-flex gap-3 flex-wrap">
      <div v-for="s in quickStats" :key="s.label"
        class="d-flex align-items-center gap-2 px-4 py-2 rounded-2 border cursor-pointer"
        :class="filterStatus === s.val ? 'border-primary bg-light-primary' : 'border-gray-200 bg-light'"
        @click="filterStatus = filterStatus === s.val ? '' : s.val; fetchData()">
        <span class="fw-bolder fs-6" :class="s.color">{{ s.total }}</span>
        <span class="text-gray-600 fw-semibold fs-8">{{ s.label }}</span>
      </div>
    </div>

    <!-- Tabel Antrian -->
    <div class="card card-flush">
      <div class="card-body p-0">
        <div v-if="loading" class="d-flex justify-content-center py-10">
          <span class="spinner-border text-primary"></span>
        </div>
        <div v-else-if="antrianList.length === 0" class="text-center py-12 text-gray-400">
          <i class="ki-outline ki-notepad-edit fs-5x mb-3"></i>
          <p class="fw-semibold">Tidak ada antrian</p>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-row-dashed table-row-gray-200 align-middle gs-5 gy-4 mb-0">
            <thead>
              <tr class="fw-bold text-gray-500 fs-7 text-uppercase border-bottom">
                <th>No. Antrian</th>
                <th>Pasien</th>
                <th>No. RM</th>
                <th>Poli / Dokter</th>
                <th class="text-center">Jaminan</th>
                <th>Daftar</th>
                <th class="text-center">Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="a in antrianList" :key="a.id">
                <td>
                  <span class="badge badge-light-primary fw-bolder fs-7">{{ a.nomor_display }}</span>
                </td>
                <td class="fw-bold text-gray-900">{{ a.pasien }}</td>
                <td class="text-gray-500 fs-8">{{ a.no_rm }}</td>
                <td>
                  <div class="fw-semibold text-gray-800 fs-7">{{ a.poli }}</div>
                  <div class="text-gray-500 fs-8">{{ a.dokter }}</div>
                </td>
                <td class="text-center">
                  <span class="badge fs-9"
                    :class="a.jenis_pasien === 'bpjs' ? 'badge-light-success' : 'badge-light-info'">
                    {{ a.jenis_pasien }}
                  </span>
                </td>
                <td class="text-gray-500 fs-8">{{ a.waktu_daftar }}</td>
                <td class="text-center">
                  <span class="badge fs-8" :class="statusBadge(a.status)">{{ a.status }}</span>
                </td>
                <td class="text-center">
                  <button v-if="a.status === 'menunggu'"
                    class="btn btn-icon btn-light-danger btn-sm"
                    @click="batalkan(a)"
                    title="Batalkan antrian">
                    <i class="ki-outline ki-cross-circle fs-5"></i>
                  </button>
                  <span v-else class="text-gray-300">—</span>
                </td>
              </tr>
            </tbody>
          </table>
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

const antrianList  = ref<any[]>([])
const sesiList     = ref<any[]>([])
const search       = ref('')
const filterPoli   = ref('')
const filterStatus = ref('')
let   timer: any   = null

const quickStats = computed(() => {
  const all = antrianList.value
  return [
    { label: 'Semua',     val: '',          total: all.length,                                               color: 'text-gray-800' },
    { label: 'Menunggu',  val: 'menunggu',  total: all.filter(a => a.status === 'menunggu').length,          color: 'text-warning'  },
    { label: 'Dilayani',  val: 'dilayani',  total: all.filter(a => a.status === 'dilayani').length,          color: 'text-primary'  },
    { label: 'Selesai',   val: 'selesai',   total: all.filter(a => a.status === 'selesai').length,           color: 'text-success'  },
    { label: 'Batal',     val: 'batal',     total: all.filter(a => a.status === 'batal').length,             color: 'text-danger'   },
  ]
})

const statusBadge = (s: string) => ({
  menunggu : 'badge-light-warning',
  dipanggil: 'badge-light-info',
  skrining : 'badge-light-info',
  dalam_antrian_dokter: 'badge-light-primary',
  dilayani : 'badge-light-primary',
  selesai  : 'badge-light-success',
  dilewati : 'badge-light-secondary',
  batal    : 'badge-light-danger',
}[s] ?? 'badge-light-secondary')

async function fetchData() {
  loading.value = true
  try {
    const res = await api.get('/loket/antrian', {
      params: { search: search.value, poli_id: filterPoli.value, status: filterStatus.value }
    })
    antrianList.value = res.data.data
  } finally {
    loading.value = false
  }
}

async function fetchSesi() {
  const res  = await api.get('/loket/sesi')
  sesiList.value = res.data.data
}

async function batalkan(a: any) {
  if (!confirm(`Batalkan antrian ${a.nomor_display} — ${a.pasien}?`)) return
  try {
    await api.patch(`/loket/antrian/${a.id}/batalkan`)
    alertMsg.value  = `Antrian ${a.nomor_display} berhasil dibatalkan.`
    alertType.value = 'success'
    fetchData()
  } catch (e: any) {
    alertMsg.value  = e.response?.data?.message ?? 'Gagal membatalkan antrian.'
    alertType.value = 'danger'
  }
}

onMounted(() => {
  fetchSesi()
  fetchData()
  timer = setInterval(fetchData, 30000)
})
onUnmounted(() => clearInterval(timer))
</script>

<style scoped>
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
