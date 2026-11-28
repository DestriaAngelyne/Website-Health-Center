<template>
  <div class="pm-page">
    <router-link to="/dokter/pemeriksaan" class="pm-kembali">&larr; Kembali ke daftar</router-link>

    <p v-if="loading" class="pm-sub">Memuat...</p>
    <p v-else-if="error" class="pm-error">{{ error }}</p>

    <div v-else-if="data" class="pm-grid">
      <div class="pm-box pm-pad">
        <div class="pm-judul">{{ data.nomor }} &middot; {{ data.pasien.nama }}</div>
        <div class="pm-meta">
          {{ data.pasien.no_rm }} &middot; {{ data.pasien.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}<span v-if="data.pasien.umur !== null"> &middot; {{ data.pasien.umur }} th</span>
        </div>
        <div class="pm-meta">
          {{ data.jenis_pasien === 'bpjs' ? 'BPJS' : 'Umum' }}<span v-if="data.jenis_pasien === 'bpjs' && data.pasien.no_bpjs"> ({{ data.pasien.no_bpjs }})</span>
          &middot; {{ data.jenis_kunjungan === 'baru' ? 'Kunjungan baru' : 'Kunjungan lama' }}
        </div>
        <div class="pm-label">Keluhan pasien</div>
        <div class="pm-keluhan">{{ data.keluhan || '-' }}</div>
        <div class="pm-label">Status</div>
        <span class="pm-st" :class="'pm-st-' + data.status">{{ labelStatus }}</span>
      </div>

      <div class="pm-box pm-pad">
        <div class="pm-label">Catatan pemeriksaan</div>

        <div v-if="data.status === 'siap_diperiksa'">
          <p class="pm-sub">Pasien sudah selesai skrining. Mulai pemeriksaan untuk mengisi catatan.</p>
          <button type="button" class="pm-btn pm-btn-utama" :disabled="proses" @click="mulai">Mulai Pemeriksaan</button>
        </div>

        <div v-else-if="data.status === 'diperiksa'">
          <textarea v-model="catatan" class="pm-textarea" rows="7" maxlength="2000" placeholder="Tuliskan hasil pemeriksaan, diagnosis, dan anjuran untuk pasien."></textarea>
          <p class="pm-bantu">Catatan wajib diisi sebelum pemeriksaan diselesaikan.</p>
          <div class="pm-tombol">
            <button type="button" class="pm-btn" :disabled="proses" @click="simpan">Simpan Catatan</button>
            <button type="button" class="pm-btn pm-btn-utama" :disabled="proses || !catatan.trim()" @click="selesai">Selesaikan Pemeriksaan</button>
          </div>
        </div>

        <div v-else>
          <div class="pm-keluhan">{{ data.catatan_dokter || '-' }}</div>
          <p class="pm-bantu">Pemeriksaan ini sudah selesai.</p>
        </div>

        <p v-if="pesan" class="pm-ok">{{ pesan }}</p>
        <p v-if="galat" class="pm-error">{{ galat }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const id = route.params.id

const data = ref(null)
const catatan = ref('')
const loading = ref(true)
const proses = ref(false)
const error = ref('')
const pesan = ref('')
const galat = ref('')

const LABEL = { siap_diperiksa: 'Siap Diperiksa', diperiksa: 'Sedang Diperiksa', selesai: 'Selesai' }
const labelStatus = computed(() => LABEL[data.value?.status] ?? data.value?.status)

function pesanError(e) {
  return e.response?.data?.errors?.catatan_dokter?.[0] || e.response?.data?.message || 'Terjadi kesalahan.'
}

async function muat() {
  try {
    const res = await api.get(`/dokter/pemeriksaan/${id}`)
    data.value = res.data.data
    catatan.value = data.value.catatan_dokter ?? ''
  } catch (e) {
    error.value = e.response?.status === 404 ? 'Antrean tidak ditemukan atau bukan milik Anda.' : (e.response?.data?.message || 'Gagal memuat data.')
  } finally {
    loading.value = false
  }
}

async function jalankan(fn) {
  proses.value = true; pesan.value = ''; galat.value = ''
  try { await fn() } catch (e) { galat.value = pesanError(e) } finally { proses.value = false }
}

const mulai = () => jalankan(async () => {
  const res = await api.patch(`/dokter/pemeriksaan/${id}/mulai`)
  data.value = res.data.data
  catatan.value = data.value.catatan_dokter ?? ''
})

const simpan = () => jalankan(async () => {
  const res = await api.patch(`/dokter/pemeriksaan/${id}/catatan`, { catatan_dokter: catatan.value })
  data.value = res.data.data
  pesan.value = 'Catatan tersimpan.'
})

const selesai = () => jalankan(async () => {
  if (!confirm('Selesaikan pemeriksaan pasien ini? Pasien akan menerima notifikasi selesai.')) return
  await api.patch(`/dokter/pemeriksaan/${id}/selesai`, { catatan_dokter: catatan.value })
  router.push('/dokter/pemeriksaan')
})

onMounted(muat)
</script>

<style scoped>
.pm-page { padding: .5rem 0; }
.pm-kembali { display: inline-block; margin-bottom: 1rem; text-decoration: none; opacity: .8; }
.pm-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; }
.pm-box { border: 1.5px solid rgba(128,128,128,.3); border-radius: 12px; }
.pm-pad { padding: 1.25rem; }
.pm-judul { font-size: 1.25rem; font-weight: 800; margin-bottom: .25rem; }
.pm-meta { font-size: .85rem; opacity: .75; margin-bottom: .15rem; }
.pm-label { margin-top: 1rem; margin-bottom: .35rem; font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; opacity: .65; font-weight: 700; }
.pm-keluhan { white-space: pre-wrap; }
.pm-sub { opacity: .75; }
.pm-textarea { width: 100%; padding: .75rem; border-radius: 10px; border: 1.5px solid rgba(128,128,128,.4); background: transparent; color: inherit; font: inherit; resize: vertical; }
.pm-bantu { font-size: .8rem; opacity: .7; margin: .4rem 0 0; }
.pm-tombol { display: flex; gap: .75rem; margin-top: 1rem; flex-wrap: wrap; }
.pm-btn { display: inline-block; padding: .55rem 1rem; border-radius: 10px; border: 1.5px solid rgba(128,128,128,.4); background: transparent; color: inherit; font-weight: 600; cursor: pointer; text-decoration: none; }
.pm-btn:disabled { opacity: .5; cursor: not-allowed; }
.pm-btn-utama { background: #0ea5e9; border-color: #0ea5e9; color: #fff; }
.pm-st { padding: .25rem .7rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
.pm-st-siap_diperiksa { background: rgba(14,165,233,.16); color: #0ea5e9; }
.pm-st-diperiksa { background: rgba(245,158,11,.16); color: #f59e0b; }
.pm-st-selesai { background: rgba(34,197,94,.16); color: #22c55e; }
.pm-error { color: #ef4444; margin-top: .75rem; }
.pm-ok { color: #22c55e; margin-top: .75rem; }
</style>