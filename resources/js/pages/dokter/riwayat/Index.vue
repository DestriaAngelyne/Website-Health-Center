<template>
  <div class="d-flex flex-column gap-7">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Riwayat Pasien</h1>
        <span class="text-gray-500 fw-semibold fs-7">Semua kunjungan yang pernah Anda tangani</span>
      </div>
    </div>

    <!-- Filter -->
    <div class="card card-flush">
      <div class="card-body py-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="position-relative">
              <i class="ki-outline ki-magnifier position-absolute fs-4"
                style="left:12px;top:50%;transform:translateY(-50%);color:#99a1b7"></i>
              <input v-model="search" @input="fetchData" type="text"
                class="form-control form-control-solid ps-10"
                placeholder="Cari nama pasien atau No. RM..."/>
            </div>
          </div>
          <div class="col-md-3">
            <input v-model="tanggal" @change="fetchData" type="date"
              class="form-control form-control-solid"/>
          </div>
          <div class="col-md-3">
            <button class="btn btn-light w-100" @click="clearFilter">
              <i class="ki-outline ki-arrows-circle me-1"></i>Reset Filter
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel -->
    <div class="card card-flush">
      <div class="card-body p-0">
        <div v-if="loading" class="d-flex justify-content-center py-10">
          <span class="spinner-border text-primary"></span>
        </div>
        <div v-else-if="riwayat.length === 0" class="text-center py-12 text-gray-400">
          <i class="ki-outline ki-notepad-edit fs-5x mb-3"></i>
          <p class="fw-semibold">Belum ada riwayat kunjungan</p>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-row-dashed align-middle gs-5 gy-4 mb-0">
            <thead>
              <tr class="fw-bold text-gray-500 fs-7 text-uppercase border-bottom">
                <th>Tanggal</th>
                <th>Pasien</th>
                <th>No. RM</th>
                <th>Poli</th>
                <th>Diagnosa</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="r in riwayat" :key="r.id">
                <td class="text-gray-500 fs-8">{{ r.tanggal }}</td>
                <td class="fw-bold text-gray-900">{{ r.pasien }}</td>
                <td><span class="badge badge-light-primary fs-9">{{ r.no_rm }}</span></td>
                <td class="text-gray-600 fs-8">{{ r.poli }}</td>
                <td>
                  <div class="fw-semibold text-gray-800 fs-8">{{ r.diagnosa }}</div>
                  <div class="text-gray-400 fs-9" v-if="r.diagnosa_kode">{{ r.diagnosa_kode }}</div>
                </td>
                <td class="text-center">
                  <span class="badge fs-9"
                    :class="r.status === 'selesai' ? 'badge-light-success' : 'badge-light-warning'">
                    {{ r.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1"
          class="d-flex align-items-center justify-content-between px-5 py-4 border-top">
          <span class="text-gray-500 fs-8">
            Menampilkan {{ pagination.from }}–{{ pagination.to }} dari {{ pagination.total }} data
          </span>
          <div class="d-flex gap-2">
            <button class="btn btn-icon btn-light btn-sm"
              :disabled="pagination.current_page === 1"
              @click="page--; fetchData()">
              <i class="ki-outline ki-arrow-left fs-5"></i>
            </button>
            <span class="btn btn-light btn-sm fw-bold">{{ pagination.current_page }}</span>
            <button class="btn btn-icon btn-light btn-sm"
              :disabled="pagination.current_page === pagination.last_page"
              @click="page++; fetchData()">
              <i class="ki-outline ki-arrow-right fs-5"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const loading    = ref(false)
const riwayat    = ref<any[]>([])
const search     = ref('')
const tanggal    = ref('')
const page       = ref(1)
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 })

let timer: any = null
function fetchData() {
  clearTimeout(timer)
  timer = setTimeout(async () => {
    loading.value = true
    try {
      const res = await api.get('/dokter/riwayat', {
        params: { search: search.value, tanggal: tanggal.value, page: page.value }
      })
      riwayat.value = res.data.data
      pagination.value = {
        current_page: res.data.current_page,
        last_page   : res.data.last_page,
        total       : res.data.total,
        from        : res.data.from,
        to          : res.data.to,
      }
    } finally {
      loading.value = false
    }
  }, 400)
}

function clearFilter() {
  search.value  = ''
  tanggal.value = ''
  page.value    = 1
  fetchData()
}

onMounted(fetchData)
</script>
