<template>
  <div class="publik-page">

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="nav-container">
        <a href="/" class="nav-brand">
          <div class="brand-icon">
            <svg viewBox="0 0 40 40" fill="none">
              <rect width="40" height="40" rx="10" fill="#0EA5E9"/>
              <path d="M20 8v24M8 20h24" stroke="white" stroke-width="3.5" stroke-linecap="round"/>
            </svg>
          </div>
          <div class="brand-text">
            <div class="brand-name">Puskesmas</div>
            <div class="brand-sub">Status Antrian</div>
          </div>
        </a>
        <a href="/pendaftaran-poli" class="btn-daftar">
          <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M10 4v12M4 10h12"/></svg>
          Daftar Antrian
        </a>
      </div>
    </nav>

    <div class="page-wrapper">
      <div class="page-content">

        <!-- SEARCH BOX -->
        <div class="search-section">
          <div class="search-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="1.5" style="width:28px;height:28px">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <h1 class="search-title">Cek Status Antrian</h1>
          <p class="search-desc">Masukkan nomor antrian Anda untuk melihat status terkini</p>

          <div class="search-form">
            <div class="search-input-wrap">
              <svg viewBox="0 0 20 20" fill="none" stroke="#9ca3af" stroke-width="1.5" style="width:16px;height:16px;flex-shrink:0">
                <path d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h0a2 2 0 002-2M9 5a2 2 0 012-2h0a2 2 0 012 2"/>
              </svg>
              <input
                v-model="nomorInput"
                type="text"
                class="search-input"
                placeholder="Contoh: F-001"
                @keyup.enter="cariAntrian"
                @input="nomorInput = nomorInput.toUpperCase()"
              />
              <button v-if="nomorInput" @click="nomorInput = ''; hasil = null; errorCari = ''" class="search-clear">×</button>
            </div>
            <button class="search-btn" @click="cariAntrian" :disabled="loadingCari || !nomorInput.trim()">
              <span v-if="!loadingCari">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;display:inline;vertical-align:middle;margin-right:5px"><circle cx="9" cy="9" r="5"/><path d="M15 15l-3-3"/></svg>
                Cari
              </span>
              <span v-else class="loading-row"><span class="spinner"></span> Mencari...</span>
            </button>
          </div>

          <div v-if="errorCari" class="error-msg">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width:15px;height:15px;flex-shrink:0"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ errorCari }}
          </div>
        </div>

        <!-- HASIL -->
        <div v-if="hasil" class="hasil-wrap">

          <!-- STATUS CARD -->
          <div class="status-card" :class="statusClass">
            <div class="status-header">
              <div class="status-nomor-label">Nomor Antrian</div>
              <div class="status-nomor">{{ hasil.nomor_display ?? hasil.kode_antrian + '-' + String(hasil.nomor_antrian).padStart(3,'0') }}</div>
              <div class="status-badge" :class="statusClass">
                <span class="status-dot"></span>
                {{ statusLabel }}
              </div>
            </div>

            <!-- POSISI ANTRIAN (jika menunggu) -->
            <div v-if="hasil.posisi !== null && hasil.posisi !== undefined && hasil.status === 'menunggu'" class="posisi-wrap">
              <div class="posisi-label">Posisi dalam antrian</div>
              <div class="posisi-angka">{{ hasil.posisi }}</div>
              <div class="posisi-sub">{{ hasil.posisi === 0 ? 'Anda dipanggil berikutnya!' : `${hasil.posisi} antrian di depan Anda` }}</div>
            </div>

            <!-- ANTRIAN SEBELUM (progress bar) -->
            <div v-if="hasil.total_antrian && hasil.status === 'menunggu'" class="progress-wrap">
              <div class="progress-label">
                <span>Progres Antrian</span>
                <span>{{ hasil.sudah_dilayani ?? 0 }} / {{ hasil.total_antrian }} selesai</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: progressPct + '%' }"></div>
              </div>
            </div>
          </div>

          <!-- DETAIL INFO -->
          <div class="detail-card">
            <div class="detail-title">Detail Kunjungan</div>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Poliklinik</span>
                <span class="detail-value">{{ hasil.nama_poli ?? hasil.poli?.nama ?? '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Dokter</span>
                <span class="detail-value">{{ hasil.nama_dokter ?? hasil.dokter?.nama ?? '-' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Tanggal</span>
                <span class="detail-value">{{ formatTanggal(hasil.tanggal ?? hasil.sesi?.tanggal) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Sesi</span>
                <span class="detail-value">{{ hasil.jam_buka ?? hasil.sesi?.jam_buka ?? '-' }} – {{ hasil.jam_tutup ?? hasil.sesi?.jam_tutup ?? '-' }} WIB</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Jaminan</span>
                <span class="detail-value">
                  <span class="badge-jaminan">{{ hasil.jaminan ?? '-' }}</span>
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Nama Pasien</span>
                <span class="detail-value">{{ hasil.nama_pasien ?? hasil.pasien?.nama ?? '-' }}</span>
              </div>
            </div>
          </div>

          <!-- INFO TAMBAHAN -->
          <div class="info-box" v-if="hasil.status === 'menunggu'">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0;color:#0369a1"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            <span>Harap berada di area puskesmas. Nomor Anda akan dipanggil saat giliran tiba.</span>
          </div>
          <div class="info-box success" v-else-if="hasil.status === 'dipanggil'">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0;color:#15803d"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span><strong>Nomor Anda sedang dipanggil!</strong> Segera menuju ruang pemeriksaan.</span>
          </div>
          <div class="info-box warning" v-else-if="hasil.status === 'selesai'">
            <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0;color:#854d0e"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Kunjungan Anda telah selesai. Terima kasih telah menggunakan layanan kami.</span>
          </div>

          <!-- REFRESH -->
          <div class="refresh-wrap">
            <button class="btn-refresh" @click="cariAntrian" :disabled="loadingCari">
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M4 4v5h5M16 16v-5h-5M4 9a8 8 0 0113.5-4M16 11a8 8 0 01-13.5 4"/></svg>
              Perbarui Status
            </button>
            <span class="refresh-hint">Terakhir diperbarui: {{ lastUpdate }}</span>
          </div>
        </div>

        <!-- EMPTY STATE awal -->
        <div v-else-if="!loadingCari && !errorCari" class="empty-state">
          <div class="empty-icon">🔍</div>
          <div class="empty-title">Masukkan Nomor Antrian</div>
          <div class="empty-desc">Nomor antrian tersedia di struk yang Anda terima saat mendaftar</div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route      = useRoute()
const nomorInput = ref('')
const loadingCari = ref(false)
const errorCari   = ref('')
const hasil       = ref<any>(null)
const lastUpdate  = ref('-')

// Auto-isi dari query string ?nomor=F-001 (dari QR)
onMounted(() => {
  const nomor = route.query.nomor as string
  if (nomor) {
    nomorInput.value = nomor.toUpperCase()
    cariAntrian()
  }
})

// Auto refresh tiap 30 detik jika ada hasil
let refreshTimer: ReturnType<typeof setInterval> | null = null
onMounted(() => {
  refreshTimer = setInterval(() => {
    if (hasil.value) cariAntrian(true)
  }, 30000)
})
onUnmounted(() => {
  if (refreshTimer) clearInterval(refreshTimer)
})

const dayNames_   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']
const monthNames_ = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
function formatTanggal(tgl: string | null | undefined) {
  if (!tgl) return '-'
  const d = new Date(tgl)
  return `${dayNames_[d.getDay()]}, ${d.getDate()} ${monthNames_[d.getMonth()]} ${d.getFullYear()}`
}

const statusLabel = computed(() => {
  const map: Record<string, string> = {
    menunggu  : 'Menunggu',
    dipanggil : 'Dipanggil',
    dilayani  : 'Sedang Dilayani',
    selesai   : 'Selesai',
    batal     : 'Dibatalkan',
    skip      : 'Dilewati',
  }
  return map[hasil.value?.status ?? ''] ?? (hasil.value?.status ?? '-')
})

const statusClass = computed(() => {
  const map: Record<string, string> = {
    menunggu  : 'status-menunggu',
    dipanggil : 'status-dipanggil',
    dilayani  : 'status-dilayani',
    selesai   : 'status-selesai',
    batal     : 'status-batal',
    skip      : 'status-batal',
  }
  return map[hasil.value?.status ?? ''] ?? 'status-menunggu'
})

const progressPct = computed(() => {
  if (!hasil.value?.total_antrian) return 0
  return Math.round(((hasil.value.sudah_dilayani ?? 0) / hasil.value.total_antrian) * 100)
})

async function cariAntrian(silent = false) {
  const nomor = nomorInput.value.trim()
  if (!nomor) return
  if (!silent) { loadingCari.value = true; errorCari.value = ''; hasil.value = null }
  try {
    // Endpoint publik — tidak perlu token
    const res = await api.get('/antrian/status', { params: { nomor } })
    const data = res.data?.data ?? res.data
    if (!data) throw new Error('Data tidak ditemukan')
    hasil.value = data
    lastUpdate.value = new Date().toLocaleTimeString('id-ID')
  } catch (e: any) {
    if (!silent) {
      errorCari.value = e.response?.data?.message ?? e.response?.status === 404
        ? `Nomor antrian "${nomor}" tidak ditemukan. Periksa kembali nomor Anda.`
        : 'Gagal mengambil data. Coba lagi.'
    }
  } finally {
    if (!silent) loadingCari.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.publik-page { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #0f172a; background: #f8fafc; min-height: 100vh; }

/* NAVBAR */
.navbar { background: #ffffff; border-bottom: 1.5px solid #e5e7eb; position: sticky; top: 0; z-index: 100; }
.nav-container { max-width: 960px; margin: 0 auto; padding: 0 28px; height: 60px; display: flex; align-items: center; justify-content: space-between; }
.nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; }
.brand-icon { width: 34px; height: 34px; flex-shrink: 0; }
.brand-text { display: flex; flex-direction: column; line-height: 1; }
.brand-name { font-size: 15px; font-weight: 800; color: #0f172a; }
.brand-sub { font-size: 10.5px; color: #4b5563; font-weight: 500; margin-top: 1px; }
.btn-daftar { display: inline-flex; align-items: center; gap: 6px; background: #0f172a; color: white; text-decoration: none; border-radius: 7px; padding: 7px 14px; font-size: 13px; font-weight: 700; transition: background .15s; font-family: inherit; }
.btn-daftar:hover { background: #1e293b; }

/* PAGE */
.page-wrapper { padding-bottom: 80px; }
.page-content { max-width: 600px; margin: 0 auto; padding: 40px 24px; }

/* SEARCH */
.search-section { background: white; border-radius: 16px; padding: 32px 28px; border: 1.5px solid #e5e7eb; margin-bottom: 24px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
.search-icon-wrap { width: 60px; height: 60px; border-radius: 14px; background: #f0f9ff; border: 1.5px solid #bae6fd; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.search-title { font-size: 22px; font-weight: 900; color: #0f172a; margin-bottom: 6px; letter-spacing: -0.02em; }
.search-desc { font-size: 14px; color: #4b5563; font-weight: 500; margin-bottom: 24px; }
.search-form { display: flex; gap: 10px; }
.search-input-wrap { flex: 1; display: flex; align-items: center; gap: 8px; border: 1.5px solid #e5e7eb; border-radius: 9px; padding: 0 12px; background: white; transition: border-color .15s; }
.search-input-wrap:focus-within { border-color: #0f172a; box-shadow: 0 0 0 3px rgba(15,23,42,.06); }
.search-input { flex: 1; border: none; outline: none; font-size: 15px; font-family: inherit; font-weight: 700; color: #0f172a; padding: 11px 0; background: transparent; letter-spacing: 0.5px; }
.search-input::placeholder { font-weight: 500; color: #9ca3af; letter-spacing: 0; }
.search-clear { background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 18px; line-height: 1; padding: 2px; }
.search-clear:hover { color: #4b5563; }
.search-btn { background: #0f172a; color: white; border: none; border-radius: 9px; padding: 11px 22px; font-size: 14px; font-weight: 700; cursor: pointer; transition: background .15s; font-family: inherit; white-space: nowrap; }
.search-btn:hover:not(:disabled) { background: #1e293b; }
.search-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.error-msg { display: flex; align-items: center; gap: 8px; margin-top: 14px; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #dc2626; font-weight: 600; text-align: left; }

/* HASIL */
.hasil-wrap { display: flex; flex-direction: column; gap: 16px; }

/* STATUS CARD */
.status-card { border-radius: 14px; padding: 28px; border: 2px solid; text-align: center; }
.status-menunggu { background: #eff6ff; border-color: #bfdbfe; }
.status-dipanggil { background: #f0fdf4; border-color: #86efac; animation: pulse-border 1.5s ease-in-out infinite; }
.status-dilayani { background: #f0fdf4; border-color: #86efac; }
.status-selesai { background: #f8fafc; border-color: #e2e8f0; }
.status-batal { background: #fef2f2; border-color: #fca5a5; }
@keyframes pulse-border { 0%,100% { border-color: #86efac; } 50% { border-color: #16a34a; } }

.status-nomor-label { font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
.status-nomor { font-size: 72px; font-weight: 900; color: #0f172a; line-height: 1; margin-bottom: 14px; letter-spacing: -0.04em; }
.status-badge { display: inline-flex; align-items: center; gap: 7px; padding: 6px 16px; border-radius: 100px; font-size: 13px; font-weight: 800; }
.status-menunggu .status-badge { background: #dbeafe; color: #1d4ed8; }
.status-dipanggil .status-badge { background: #dcfce7; color: #15803d; }
.status-dilayani .status-badge { background: #dcfce7; color: #15803d; }
.status-selesai .status-badge { background: #f1f5f9; color: #475569; }
.status-batal .status-badge { background: #fee2e2; color: #dc2626; }
.status-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; display: inline-block; }
.status-dipanggil .status-dot { animation: blink 1s ease-in-out infinite; }
@keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0; } }

.posisi-wrap { margin-top: 20px; padding-top: 20px; border-top: 1px dashed rgba(0,0,0,.1); }
.posisi-label { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .8px; }
.posisi-angka { font-size: 52px; font-weight: 900; color: #1d4ed8; line-height: 1.1; }
.posisi-sub { font-size: 13px; color: #4b5563; font-weight: 600; margin-top: 4px; }

.progress-wrap { margin-top: 16px; padding-top: 16px; border-top: 1px dashed rgba(0,0,0,.1); text-align: left; }
.progress-label { display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; color: #4b5563; margin-bottom: 8px; }
.progress-bar { height: 6px; background: #dbeafe; border-radius: 100px; overflow: hidden; }
.progress-fill { height: 100%; background: #3b82f6; border-radius: 100px; transition: width .5s; }

/* DETAIL CARD */
.detail-card { background: white; border-radius: 14px; padding: 22px; border: 1.5px solid #e5e7eb; }
.detail-title { font-size: 13px; font-weight: 800; color: #374151; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 16px; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.detail-item { display: flex; flex-direction: column; gap: 3px; }
.detail-label { font-size: 10px; font-weight: 800; color: #9ca3af; text-transform: uppercase; letter-spacing: .8px; }
.detail-value { font-size: 14px; font-weight: 700; color: #0f172a; }
.badge-jaminan { display: inline-block; background: #f3f4f6; color: #374151; border: 1.5px solid #e5e7eb; border-radius: 5px; padding: 2px 9px; font-size: 12px; font-weight: 800; }

/* INFO BOX */
.info-box { display: flex; align-items: flex-start; gap: 10px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 13px 16px; font-size: 13px; color: #0369a1; font-weight: 600; }
.info-box.success { background: #f0fdf4; border-color: #86efac; color: #15803d; }
.info-box.warning { background: #fefce8; border-color: #fde047; color: #854d0e; }

/* REFRESH */
.refresh-wrap { display: flex; align-items: center; justify-content: space-between; }
.btn-refresh { display: inline-flex; align-items: center; gap: 6px; background: white; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 700; color: #374151; cursor: pointer; transition: all .15s; font-family: inherit; }
.btn-refresh:hover:not(:disabled) { border-color: #4b5563; color: #0f172a; }
.btn-refresh:disabled { opacity: 0.4; cursor: not-allowed; }
.refresh-hint { font-size: 12px; color: #9ca3af; font-weight: 500; }

/* EMPTY */
.empty-state { text-align: center; padding: 48px 24px; }
.empty-icon { font-size: 48px; margin-bottom: 12px; }
.empty-title { font-size: 18px; font-weight: 800; color: #374151; margin-bottom: 6px; }
.empty-desc { font-size: 14px; color: #4b5563; font-weight: 500; }

/* LOADING */
.loading-row { display: flex; align-items: center; gap: 8px; }
.spinner { display: inline-block; width: 13px; height: 13px; border: 2.5px solid rgba(255,255,255,0.35); border-top-color: white; border-radius: 50%; animation: spin .65s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 540px) {
  .search-form { flex-direction: column; }
  .detail-grid { grid-template-columns: 1fr; }
  .status-nomor { font-size: 56px; }
  .refresh-wrap { flex-direction: column; gap: 8px; align-items: flex-start; }
  .page-content { padding: 24px 16px; }
}
</style>
