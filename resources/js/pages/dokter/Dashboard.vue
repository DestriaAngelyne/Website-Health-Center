<template>
  <div class="dk-page">
    <div class="dk-head">
      <div>
        
        <p v-if="info" class="dk-sub">{{ info.nama }}<span v-if="info.spesialis"> &middot; {{ info.spesialis }}</span> &middot; {{ info.poli }}</p>
      </div>
      <div class="dk-tools">
        <input v-model="tanggal" type="date" class="dk-input" @change="muat()" />
        <button type="button" class="dk-btn" :disabled="loading" @click="muat()">{{ loading ? 'Memuat...' : 'Muat ulang' }}</button>
      </div>
    </div>

    <p v-if="error" class="dk-error">{{ error }}</p>

    <div v-else>
      <div class="dk-cards">
        <div v-for="k in kartu" :key="k.label" class="dk-card">
          <span class="dk-card-val">{{ k.value }}</span>
          <span class="dk-card-lbl">{{ k.label }}</span>
        </div>
      </div>

      <div class="dk-box">
        <table class="dk-table">
          <thead>
            <tr><th>No.</th><th>Pasien</th><th>Jaminan</th><th>Keluhan</th><th>Status</th><th>Daftar</th></tr>
          </thead>
          <tbody>
            <tr v-for="a in antrian" :key="a.id">
              <td class="dk-no">{{ a.nomor }}</td>
              <td>
                <div class="dk-nama">{{ a.pasien.nama }}</div>
                <div class="dk-meta">{{ a.pasien.no_rm }} &middot; {{ a.pasien.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}<span v-if="a.pasien.umur !== null"> &middot; {{ a.pasien.umur }} th</span></div>
              </td>
              <td>
                {{ a.jenis_pasien === 'bpjs' ? 'BPJS' : 'Umum' }}
                <div class="dk-meta">{{ a.jenis_kunjungan === 'baru' ? 'Kunjungan baru' : 'Kunjungan lama' }}</div>
              </td>
              <td class="dk-keluhan">{{ a.keluhan || '-' }}</td>
              <td><span class="dk-st" :class="'dk-st-' + a.status">{{ label(a.status) }}</span></td>
              <td class="dk-meta">{{ jam(a.waktu_daftar) }}</td>
            </tr>
          </tbody>
        </table>
        <p v-if="!loading && !antrian.length" class="dk-empty">Belum ada antrean untuk tanggal ini.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'

const hariIni = () => {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
}

const tanggal = ref(hariIni())
const info = ref(null)
const ringkasan = ref({})
const antrian = ref([])
const loading = ref(false)
const error = ref('')
let timer = null

const LABEL = { menunggu: 'Menunggu', dipanggil: 'Dipanggil', skrining: 'Skrining', siap_diperiksa: 'Siap Diperiksa', diperiksa: 'Diperiksa', selesai: 'Selesai', dilewati: 'Dilewati', batal: 'Dibatalkan' }
const label = (s) => LABEL[s] ?? s

const jam = (t) => {
  if (!t) return '-'
  const d = new Date(String(t).replace(' ', 'T'))
  return isNaN(d) ? '-' : d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

const kartu = computed(() => [
  { label: 'Total antrean', value: ringkasan.value.total ?? 0 },
  { label: 'Menunggu', value: ringkasan.value.menunggu ?? 0 },
  { label: 'Di perawat', value: ringkasan.value.diproses ?? 0 }, { label: 'Siap diperiksa', value: ringkasan.value.siap_diperiksa ?? 0 }, { label: 'Sedang diperiksa', value: ringkasan.value.diperiksa ?? 0 },
  { label: 'Selesai', value: ringkasan.value.selesai ?? 0 },
  { label: 'Dilewati / batal', value: (ringkasan.value.dilewati ?? 0) + (ringkasan.value.batal ?? 0) },
])

async function muat(senyap = false) {
  if (!senyap) loading.value = true
  try {
    const res = await api.get('/dokter/dashboard', { params: { tanggal: tanggal.value } })
    const d = res.data.data
    info.value = d.dokter
    ringkasan.value = d.ringkasan
    antrian.value = d.antrian
    error.value = ''
  } catch (e) {
    error.value = e.response?.data?.message ?? 'Gagal memuat data antrean.'
  } finally {
    loading.value = false
  }
}

onMounted(() => { muat(); timer = setInterval(() => muat(true), 30000) })
onUnmounted(() => clearInterval(timer))
</script>

<style scoped>
.dk-page { padding: 1.5rem 2rem; }
.dk-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
.dk-title { font-size: 1.6rem; font-weight: 800; margin: 0; }
.dk-sub { margin: .25rem 0 0; opacity: .75; font-size: .9rem; }
.dk-tools { display: flex; gap: .5rem; align-items: center; }
.dk-input, .dk-btn { height: 40px; padding: 0 .9rem; border-radius: 10px; border: 1.5px solid rgba(128,128,128,.35); background: transparent; color: inherit; font: inherit; }
.dk-btn { cursor: pointer; font-weight: 600; }
.dk-btn:disabled { opacity: .55; cursor: not-allowed; }
.dk-error { padding: 1rem 1.25rem; border-radius: 10px; border: 1.5px solid rgba(220,38,38,.5); background: rgba(220,38,38,.08); color: #ef4444; font-weight: 600; }
.dk-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: .9rem; margin-bottom: 1.25rem; }
.dk-card { display: flex; flex-direction: column; gap: .2rem; padding: 1rem 1.1rem; border-radius: 12px; border: 1.5px solid rgba(128,128,128,.3); background: rgba(128,128,128,.07); }
.dk-card-val { font-size: 1.7rem; font-weight: 800; }
.dk-card-lbl { font-size: .8rem; opacity: .75; }
.dk-box { border-radius: 12px; border: 1.5px solid rgba(128,128,128,.3); overflow-x: auto; }
.dk-table { width: 100%; border-collapse: collapse; font-size: .9rem; }
.dk-table th { text-align: left; padding: .75rem 1rem; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; opacity: .7; border-bottom: 1.5px solid rgba(128,128,128,.3); white-space: nowrap; }
.dk-table td { padding: .8rem 1rem; border-bottom: 1px solid rgba(128,128,128,.18); vertical-align: top; }
.dk-table tr:last-child td { border-bottom: none; }
.dk-no { font-weight: 800; white-space: nowrap; }
.dk-nama { font-weight: 700; }
.dk-meta { font-size: .78rem; opacity: .7; }
.dk-keluhan { min-width: 220px; max-width: 420px; white-space: pre-wrap; }
.dk-st { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 700; white-space: nowrap; }
.dk-st-menunggu { background: rgba(245,158,11,.16); color: #d97706; }
.dk-st-dipanggil, .dk-st-skrining, .dk-st-siap_diperiksa, .dk-st-diperiksa { background: rgba(14,165,233,.16); color: #0ea5e9; }
.dk-st-selesai { background: rgba(34,197,94,.16); color: #16a34a; }
.dk-st-dilewati, .dk-st-batal { background: rgba(239,68,68,.14); color: #ef4444; }
.dk-empty { padding: 2rem; text-align: center; opacity: .7; margin: 0; }
</style>