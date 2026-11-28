<template>
  <div class="pm-page">
    <div class="pm-head">
      <p class="pm-sub">Pasien Anda yang sudah selesai skrining, hari ini</p>
      <button type="button" class="pm-btn" :disabled="loading" @click="muat()">{{ loading ? 'Memuat...' : 'Muat ulang' }}</button>
    </div>

    <p v-if="error" class="pm-error">{{ error }}</p>

    <div v-else class="pm-box">
      <table class="pm-table">
        <thead>
          <tr><th>No.</th><th>Pasien</th><th>Keluhan</th><th>Status</th><th class="pm-aksi">Aksi</th></tr>
        </thead>
        <tbody>
          <tr v-for="a in daftar" :key="a.id">
            <td class="pm-no">{{ a.nomor }}</td>
            <td>
              <div class="pm-nama">{{ a.pasien.nama }}</div>
              <div class="pm-meta">{{ a.pasien.no_rm }}<span v-if="a.pasien.umur !== null"> &middot; {{ a.pasien.umur }} th</span></div>
            </td>
            <td>{{ a.keluhan || '-' }}</td>
            <td><span class="pm-st" :class="'pm-st-' + a.status">{{ a.status === 'diperiksa' ? 'Sedang Diperiksa' : 'Siap Diperiksa' }}</span></td>
            <td class="pm-aksi">
              <router-link :to="`/dokter/pemeriksaan/${a.id}`" class="pm-btn pm-btn-utama">
                {{ a.status === 'diperiksa' ? 'Lanjutkan' : 'Periksa' }}
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-if="!loading && !daftar.length" class="pm-empty">Belum ada pasien yang siap diperiksa.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'

const daftar = ref([])
const loading = ref(false)
const error = ref('')
let timer = null

async function muat(senyap = false) {
  if (!senyap) loading.value = true
  try {
    const res = await api.get('/dokter/dashboard')
    const semua = res.data.data.antrian || []
    daftar.value = semua
      .filter(a => ['siap_diperiksa', 'diperiksa'].includes(a.status))
      .sort((x, y) => (x.status === y.status ? 0 : x.status === 'diperiksa' ? -1 : 1))
    error.value = ''
  } catch (e) {
    error.value = e.response?.data?.message || 'Gagal memuat data.'
  } finally {
    loading.value = false
  }
}

onMounted(() => { muat(); timer = setInterval(() => muat(true), 30000) })
onUnmounted(() => clearInterval(timer))
</script>

<style scoped>
.pm-page { padding: .5rem 0; }
.pm-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; gap: 1rem; }
.pm-sub { margin: 0; opacity: .75; }
.pm-btn { display: inline-block; padding: .55rem 1rem; border-radius: 10px; border: 1.5px solid rgba(128,128,128,.4); background: transparent; color: inherit; font-weight: 600; cursor: pointer; text-decoration: none; }
.pm-btn:disabled { opacity: .5; cursor: not-allowed; }
.pm-btn-utama { background: #0ea5e9; border-color: #0ea5e9; color: #fff; }
.pm-error { color: #ef4444; }
.pm-box { border: 1.5px solid rgba(128,128,128,.3); border-radius: 12px; overflow-x: auto; }
.pm-table { width: 100%; border-collapse: collapse; }
.pm-table th { text-align: left; padding: .9rem 1rem; font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; border-bottom: 1.5px solid rgba(128,128,128,.3); }
.pm-table td { padding: .9rem 1rem; border-bottom: 1px solid rgba(128,128,128,.15); vertical-align: middle; }
.pm-table tr:last-child td { border-bottom: 0; }
.pm-aksi { text-align: right !important; }
.pm-no { font-weight: 800; }
.pm-nama { font-weight: 700; }
.pm-meta { font-size: .8rem; opacity: .7; }
.pm-st { padding: .25rem .7rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
.pm-st-siap_diperiksa { background: rgba(14,165,233,.16); color: #0ea5e9; }
.pm-st-diperiksa { background: rgba(245,158,11,.16); color: #f59e0b; }
.pm-empty { text-align: center; padding: 2rem 0; opacity: .75; }
</style>