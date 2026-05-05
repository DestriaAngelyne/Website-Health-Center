<template>
  <div class="d-flex flex-column gap-7">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Master Obat</h1>
        <span class="text-gray-500 fw-semibold fs-7">Kelola data obat dan stok</span>
      </div>
      <router-link to="/admin/master/obat/create" class="btn btn-primary btn-sm">
        <i class="ki-outline ki-plus fs-4 me-1"></i> Tambah Obat
      </router-link>
    </div>

    <!-- Filter -->
    <div class="card card-flush">
      <div class="card-body py-4">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label fw-semibold fs-7 mb-1">Cari Obat</label>
            <input v-model="search" @input="fetchData" type="text" class="form-control form-control-sm form-control-solid" placeholder="Nama, kode, atau generik..."/>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold fs-7 mb-1">Kategori</label>
            <select v-model="filterKategori" @change="fetchData" class="form-select form-select-sm form-select-solid">
              <option value="">Semua Kategori</option>
              <option v-for="k in kategoriList" :key="k" :value="k">{{ k }}</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold fs-7 mb-1">Status</label>
            <select v-model="filterStatus" @change="fetchData" class="form-select form-select-sm form-select-solid">
              <option value="">Semua</option>
              <option value="1">Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Alert stok menipis -->
    <div v-if="stokMenipis.length" class="alert alert-warning d-flex align-items-center py-3">
      <i class="ki-outline ki-information-5 fs-2hx text-warning me-3"></i>
      <span class="fw-semibold">{{ stokMenipis.length }} obat stok menipis atau habis.</span>
    </div>

    <!-- Table -->
    <div class="card card-flush">
      <div class="card-body p-0">
        <div v-if="loading" class="d-flex justify-content-center py-15">
          <span class="spinner-border text-primary"></span>
        </div>
        <div v-else-if="obatList.length === 0" class="text-center py-15 text-gray-400">
          <i class="ki-outline ki-medicine fs-5x mb-3"></i>
          <p class="fw-semibold">Belum ada data obat</p>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-row-dashed table-row-gray-200 align-middle gs-4 gy-3 mb-0">
            <thead>
              <tr class="fw-bold text-gray-500 fs-7 text-uppercase border-bottom border-gray-200">
                <th>Kode</th>
                <th>Nama Obat</th>
                <th>Generik</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th class="text-center">Stok</th>
                <th class="text-end">Harga</th>
                <th class="text-center">Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="obat in obatList" :key="obat.id">
                <td><span class="badge badge-light-primary fw-bold">{{ obat.kode }}</span></td>
                <td>
                  <div class="fw-bold text-gray-900 fs-6">{{ obat.nama }}</div>
                  <div v-if="obat.keterangan" class="text-gray-400 fs-8 text-truncate" style="max-width:200px">{{ obat.keterangan }}</div>
                </td>
                <td class="text-gray-600 fs-7">{{ obat.generik || '-' }}</td>
                <td><span class="badge badge-light-info">{{ obat.kategori }}</span></td>
                <td class="text-gray-600 fs-7">{{ obat.satuan }}</td>
                <td class="text-center">
                  <span class="fw-bold" :class="obat.stok <= 0 ? 'text-danger' : obat.stok <= obat.stok_minimum ? 'text-warning' : 'text-success'">
                    {{ obat.stok }}
                  </span>
                  <div class="text-gray-400 fs-9">min. {{ obat.stok_minimum }}</div>
                </td>
                <td class="text-end fw-semibold text-gray-700">Rp {{ formatRp(obat.harga) }}</td>
                <td class="text-center">
                  <span class="badge" :class="obat.is_active ? 'badge-light-success' : 'badge-light-danger'">
                    {{ obat.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="text-center">
                  <div class="d-flex gap-2 justify-content-center">
                    <router-link :to="`/admin/master/obat/${obat.id}/edit`" class="btn btn-icon btn-light btn-sm" title="Edit">
                      <i class="ki-outline ki-pencil fs-5"></i>
                    </router-link>
                    <button class="btn btn-icon btn-light btn-sm" @click="toggleActive(obat)" :title="obat.is_active ? 'Nonaktifkan' : 'Aktifkan'">
                      <i class="ki-outline fs-5" :class="obat.is_active ? 'ki-cross-circle' : 'ki-check-circle'"></i>
                    </button>
                    <button class="btn btn-icon btn-light-danger btn-sm" @click="hapus(obat)" title="Hapus">
                      <i class="ki-outline ki-trash fs-5"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="d-flex justify-content-between align-items-center px-6 py-4 border-top">
          <span class="text-gray-500 fs-7">{{ meta.from }}–{{ meta.to }} dari {{ meta.total }} obat</span>
          <div class="d-flex gap-1">
            <button class="btn btn-icon btn-light btn-sm" :disabled="meta.current_page === 1" @click="page--; fetchData()">
              <i class="ki-outline ki-arrow-left fs-5"></i>
            </button>
            <button class="btn btn-icon btn-light btn-sm" :disabled="meta.current_page === meta.last_page" @click="page++; fetchData()">
              <i class="ki-outline ki-arrow-right fs-5"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const loading       = ref(false)
const obatList      = ref<any[]>([])
const search        = ref('')
const filterKategori = ref('')
const filterStatus  = ref('')
const page          = ref(1)
const meta          = ref({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0 })

const kategoriList  = ['Antibiotik', 'Analgesik', 'Antasida', 'Vitamin', 'Antihistamin', 'Antihipertensi', 'Antidiabetik', 'Lainnya']
const stokMenipis   = computed(() => obatList.value.filter(o => o.stok <= o.stok_minimum))

const formatRp = (val: number) => Number(val).toLocaleString('id-ID')

async function fetchData() {
  loading.value = true
  try {
    const res = await api.get('/admin/obat', {
      params: { search: search.value, kategori: filterKategori.value, is_active: filterStatus.value, page: page.value }
    })
    obatList.value = res.data.data?.data ?? res.data.data ?? []
    if (res.data.data?.meta) meta.value = res.data.data.meta
    else if (res.data.data?.current_page) meta.value = res.data.data
  } finally {
    loading.value = false
  }
}

async function toggleActive(obat: any) {
  await api.patch(`/admin/obat/${obat.id}/toggle-active`)
  fetchData()
}

async function hapus(obat: any) {
  if (!confirm(`Hapus obat "${obat.nama}"?`)) return
  await api.delete(`/admin/obat/${obat.id}`)
  fetchData()
}

onMounted(fetchData)
</script>
