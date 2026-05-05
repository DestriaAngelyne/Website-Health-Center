<template>
  <div class="d-flex flex-column gap-7">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Monitor Antrian</h1>
        <span class="text-gray-500 fw-semibold fs-7">Pantau antrian hari ini secara realtime</span>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <select v-model="poliFilter" @change="fetchData" class="form-select form-select-sm form-select-solid w-auto">
          <option value="">Semua Poli</option>
          <option v-for="p in poliList" :key="p.id" :value="p.id">{{ p.nama }}</option>
        </select>
        <button class="btn btn-icon btn-light btn-sm" @click="fetchData" title="Refresh">
          <i class="ki-outline ki-arrows-circle fs-4" :class="{ 'spin': loading }"></i>
        </button>
        <span class="text-gray-400 fs-8">Auto refresh 30s</span>
      </div>
    </div>

    <!-- Statistik -->
    <div class="row g-4">
      <div class="col-6 col-md-2" v-for="stat in statList" :key="stat.label">
        <div class="card card-flush text-center py-5 px-3">
          <div class="fw-bolder fs-2" :class="stat.color">{{ stat.value }}</div>
          <div class="text-gray-500 fw-semibold fs-8 mt-1">{{ stat.label }}</div>
        </div>
      </div>
    </div>

    <!-- Sesi Aktif -->
    <div class="card card-flush">
      <div class="card-header pt-5 min-h-auto">
        <h3 class="card-title fw-bold fs-6">Sesi Hari Ini</h3>
      </div>
      <div class="card-body pt-3">
        <div v-if="sesiList.length === 0" class="text-center py-6 text-gray-400 fs-7">Tidak ada sesi hari ini</div>
        <div v-else class="row g-3">
          <div v-for="s in sesiList" :key="s.id" class="col-md-4 col-lg-3">
            <div class="border rounded p-4" :class="s.status === 'aktif' || s.status === 'buka' ? 'border-success bg-light-success' : 'border-gray-200'">
              <div class="fw-bold text-gray-900 fs-7 mb-1">{{ s.poli }}</div>
              <div class="text-gray-500 fs-8 mb-2">{{ s.dokter }}</div>
              <div class="text-gray-700 fs-8 mb-2">{{ s.jam_buka }} – {{ s.jam_tutup }}</div>
              <div class="d-flex align-items-center gap-2">
                <div class="flex-grow-1 bg-gray-200 rounded" style="height:6px">
                  <div class="bg-primary rounded" :style="`width:${Math.min((s.terisi/s.kuota)*100,100)}%`" style="height:6px;transition:width 0.3s"></div>
                </div>
                <span class="text-gray-600 fs-9 fw-semibold">{{ s.terisi }}/{{ s.kuota }}</span>
              </div>
              <div class="mt-2">
                <span class="badge" :class="s.status === 'aktif' || s.status === 'buka' ? 'badge-light-success' : 'badge-light-secondary'">
                  {{ s.status }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel Antrian -->
    <div class="card card-flush">
      <div class="card-header pt-5 min-h-auto">
        <h3 class="card-title fw-bold fs-6">Daftar Antrian Hari Ini</h3>
        <div class="card-toolbar">
          <div class="d-flex gap-2">
            <select v-model="statusFilter" class="form-select form-select-sm form-select-solid w-auto">
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
      <div class="card-body p-0">
        <div v-if="loading" class="d-flex justify-content-center py-10">
          <span class="spinner-border text-primary"></span>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-row-dashed table-row-gray-200 align-middle gs-4 gy-3 mb-0">
            <thead>
              <tr class="fw-bold text-gray-500 fs-7 text-uppercase border-bottom">
                <th>No. Antrian</th>
                <th>Pasien</th>
                <th>Poli</th>
                <th>Dokter</th>
                <th>Daftar</th>
                <th>Selesai</th>
                <th class="text-center">Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="filteredAntrian.length === 0">
                <td colspan="8" class="text-center py-8 text-gray-400">Tidak ada antrian</td>
              </tr>
              <tr v-for="a in filteredAntrian" :key="a.id">
                <td><span class="badge badge-light-primary fw-bolder fs-7">{{ a.nomor_display }}</span></td>
                <td class="fw-semibold text-gray-800">{{ a.pasien }}</td>
                <td class="text-gray-600 fs-7">{{ a.poli }}</td>
                <td class="text-gray-600 fs-7">{{ a.dokter }}</td>
                <td class="text-gray-500 fs-8">{{ a.waktu_daftar }}</td>
                <td class="text-gray-500 fs-8">{{ a.waktu_selesai || '-' }}</td>
                <td class="text-center">
                  <span class="badge fs-8" :class="statusBadge(a.status)">{{ a.status }}</span>
                </td>
                <td class="text-center">
                  <select v-if="!['selesai','batal'].includes(a.status)"
                    class="form-select form-select-sm w-auto d-inline-block"
                    style="min-width:130px"
                    @change="updateStatus(a.id, ($event.target as HTMLSelectElement).value)">
                    <option value="">Ubah Status</option>
                    <option value="dipanggil">Dipanggil</option>
                    <option value="dilayani">Dilayani</option>
                    <option value="selesai">Selesai</option>
                    <option value="batal">Batal</option>
                  </select>
                  <span v-else class="text-gray-400 fs-8">—</span>
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
const poliList     = ref<any[]>([])
const sesiList     = ref<any[]>([])
const antrianList  = ref<any[]>([])
const statistik    = ref<any>({})
const poliFilter   = ref('')
const statusFilter = ref('')
let   timer: any   = null

const filteredAntrian = computed(() =>
  statusFilter.value
    ? antrianList.value.filter(a => a.status === statusFilter.value)
    : antrianList.value
)

const statList = computed(() => [
  { label: 'Total',     value: statistik.value.total     ?? 0, color: 'text-gray-800' },
  { label: 'Menunggu',  value: statistik.value.menunggu  ?? 0, color: 'text-warning'  },
  { label: 'Dipanggil', value: statistik.value.dipanggil ?? 0, color: 'text-info'     },
  { label: 'Dilayani',  value: statistik.value.dilayani  ?? 0, color: 'text-primary'  },
  { label: 'Selesai',   value: statistik.value.selesai   ?? 0, color: 'text-success'  },
  { label: 'Batal',     value: statistik.value.batal     ?? 0, color: 'text-danger'   },
])

const statusBadge = (s: string) => ({
  menunggu             : 'badge-light-warning',
  dipanggil            : 'badge-light-info',
  skrining             : 'badge-light-info',
  dalam_antrian_dokter : 'badge-light-primary',
  dilayani             : 'badge-light-primary',
  selesai              : 'badge-light-success',
  dilewati             : 'badge-light-secondary',
  batal                : 'badge-light-danger',
}[s] ?? 'badge-light-secondary')

async function fetchData() {
  loading.value = true
  try {
    const res = await api.get('/admin/antrian/monitor', { params: { poli_id: poliFilter.value || undefined } })
    // Handle both { data: { poli_list, ... } } and { poli_list, ... }
    const d = res.data?.data ?? res.data
    poliList.value    = d?.poli_list  ?? []
    sesiList.value    = d?.sesi       ?? []
    antrianList.value = d?.antrian    ?? []
    statistik.value   = d?.statistik  ?? {}
  } catch (e) {
    console.error('[Monitor] fetchData error:', e)
  } finally {
    loading.value = false
  }
}

async function updateStatus(id: number, status: string) {
  if (!status) return
  try {
    await api.patch(`/admin/antrian/${id}/status`, { status })
    fetchData()
  } catch (e) {
    console.error('[Monitor] updateStatus error:', e)
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
