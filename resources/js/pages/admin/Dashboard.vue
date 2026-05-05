<template>
  <div class="page">

    <!-- ══ TOPBAR ══ -->
    <div class="topbar">
      <div class="topbar-left">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-sub">Selamat datang kembali, <strong>{{ user?.name }}</strong> &mdash; {{ tanggalHari }}</p>
      </div>

    </div>

    <!-- ══ STAT CARDS ══ -->
    <div class="stats-grid">
      <div class="stat-card" v-for="(s, i) in statCards" :key="i"
           :style="{ '--delay': i * 0.06 + 's', '--accent': s.color }">
        <div class="stat-accent-bar"></div>
        <div class="stat-icon" :style="{ background: s.color + '12', border: '1.5px solid ' + s.color + '28' }">
          <svg viewBox="0 0 24 24" fill="none" :stroke="s.color" stroke-width="1.8" width="22" height="22" v-html="s.icon"></svg>
        </div>
        <div class="stat-content">
          <div class="stat-num">{{ s.value }}</div>
          <div class="stat-label">{{ s.label }}</div>
        </div>
      </div>
    </div>

    <!-- ══ MID SECTION ══ -->
    <div class="mid-grid">

      <!-- Antrian Per Poli -->
      <div class="panel">
        <div class="panel-head">
          <div class="panel-head-left">
            <svg viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="1.8" width="18" height="18"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
            <span class="panel-title">Antrian Per Poli</span>
          </div>
          <span class="badge badge-teal">Hari ini</span>
        </div>
        <div class="panel-body">
          <div v-if="loading" class="skeletons">
            <div class="skel" v-for="n in 5" :key="n"></div>
          </div>
          <div v-else-if="antrianPerPoli.length === 0" class="empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" width="40" height="40"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="13" y2="13"/></svg>
            <p>Belum ada data antrian hari ini</p>
          </div>
          <div v-else class="poli-list">
            <div class="poli-item" v-for="item in antrianPerPoli" :key="item.poli">
              <div class="poli-row">
                <span class="poli-name">{{ item.poli }}</span>
                <span class="poli-count">{{ item.total }} pasien</span>
              </div>
              <div class="progress-bg">
                <div class="progress-fill" :style="{ width: pctBar(item.total), background: barColor(item.total) }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Menu Cepat -->
      <div class="panel">
        <div class="panel-head">
          <div class="panel-head-left">
            <svg viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.8" width="18" height="18"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span class="panel-title">Menu Cepat</span>
          </div>
        </div>
        <div class="panel-body">
          <div class="menu-grid">
            <router-link v-for="m in menus" :key="m.to" :to="m.to" class="menu-card"
                         :style="{ '--mc': m.color }">
              <div class="menu-icon" :style="{ background: m.color + '12', border: '1.5px solid ' + m.color + '25' }">
                <svg viewBox="0 0 24 24" fill="none" :stroke="m.color" stroke-width="1.8" width="18" height="18" v-html="m.icon"></svg>
              </div>
              <span class="menu-label">{{ m.label }}</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="menu-arrow" width="14" height="14"><path d="M9 18l6-6-6-6"/></svg>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- ══ GRAFIK ══ -->
    <div class="panel">
      <div class="panel-head">
        <div class="panel-head-left">
          <svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.8" width="18" height="18"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          <span class="panel-title">Tren Antrian 7 Hari Terakhir</span>
        </div>
        <span class="badge">Mingguan</span>
      </div>
      <div class="panel-body">
        <div v-if="loading" class="skeletons"><div class="skel" style="height:160px"></div></div>
        <div v-else-if="grafikMingguan.length === 0" class="empty"><p>Belum ada data minggu ini</p></div>
        <div v-else class="chart-wrap">
          <div class="chart-bars">
            <div class="chart-col" v-for="g in grafikMingguan" :key="g.tanggal">
              <div class="chart-val">{{ g.total }}</div>
              <div class="chart-bar-wrap">
                <div class="chart-bar" :style="{ height: chartHeight(g.total) + '%' }"></div>
              </div>
              <div class="chart-label">{{ g.tanggal }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ══ TABLE ══ -->
    <div class="panel" style="margin-bottom:0">
      <div class="panel-head">
        <div class="panel-head-left">
          <svg viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.8" width="18" height="18"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span class="panel-title">Antrian Aktif Terbaru</span>
        </div>
        <span class="badge">10 terbaru</span>
      </div>

      <div v-if="loading" class="loading-row">
        <div class="spinner"></div>
        <span>Memuat data antrian...</span>
      </div>
      <div v-else-if="antrianAktif.length === 0" class="empty" style="padding:3.5rem 1.5rem">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" width="44" height="44"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <p>Belum ada antrian hari ini</p>
      </div>
      <div v-else class="table-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th>No. Antrian</th>
              <th>Pasien</th>
              <th>Poli</th>
              <th>Dokter</th>
              <th>Status</th>
              <th>Waktu Daftar</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="a in antrianAktif" :key="a.id">
              <td><span class="num-badge">{{ a.nomor_display }}</span></td>
              <td>
                <div class="patient">
                  <div class="av">{{ a.pasien?.charAt(0)?.toUpperCase() ?? '?' }}</div>
                  <span>{{ a.pasien ?? '-' }}</span>
                </div>
              </td>
              <td class="cell-muted">{{ a.poli ?? '-' }}</td>
              <td class="cell-muted">{{ a.dokter ?? '-' }}</td>
              <td><span class="st-pill" :class="stCls(a.status)">{{ a.status }}</span></td>
              <td class="cell-mono cell-muted">{{ a.waktu_daftar ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth.store'

const user    = useAuthStore().user
const loading = ref(false)

const raw            = ref({})
const antrianPerPoli = ref([])
const grafikMingguan = ref([])
const antrianAktif   = ref([])
let timer = null

const tanggalHari = new Date().toLocaleDateString('id-ID', {
  weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
})

const statCards = computed(() => [
  { label: 'Total Antrian', value: raw.value.total_antrian ?? 0, color: '#3b82f6',
    icon: '<rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>' },
  { label: 'Dilayani',      value: raw.value.dilayani      ?? 0, color: '#22c55e',
    icon: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>' },
  { label: 'Menunggu',      value: raw.value.menunggu      ?? 0, color: '#f59e0b',
    icon: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>' },
  { label: 'Dibatalkan',    value: raw.value.dibatal       ?? 0, color: '#ef4444',
    icon: '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>' },
  { label: 'Sesi Aktif',    value: raw.value.sesi_aktif    ?? 0, color: '#14b8a6',
    icon: '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>' },
  { label: 'Total Pasien',  value: raw.value.total_pasien  ?? 0, color: '#6366f1',
    icon: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>' },
  { label: 'Pasien Baru',   value: raw.value.pasien_baru   ?? 0, color: '#ec4899',
    icon: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/>' },
  { label: 'Total Dokter',  value: raw.value.total_dokter  ?? 0, color: '#a855f7',
    icon: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>' },
])

const menus = [
  { to: '/admin/master/poli',   label: 'Master Poli',   color: '#0ea5e9', icon: '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>' },
  { to: '/admin/master/dokter', label: 'Master Dokter', color: '#6366f1', icon: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>' },
  { to: '/admin/master/jadwal', label: 'Jadwal Dokter', color: '#14b8a6', icon: '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>' },
  { to: '/admin/users',         label: 'Kelola User',   color: '#f59e0b', icon: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>' },
  { to: '/admin/master/obat',   label: 'Master Obat',   color: '#ec4899', icon: '<path d="M10.5 20H4a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 0 1 1.66.9l.82 1.2a2 2 0 0 0 1.66.9H20a2 2 0 0 1 2 2v3"/><circle cx="18" cy="18" r="3"/><path d="M18 15v3l2 1"/>' },
  { to: '/loket/dashboard',     label: 'Monitor Loket', color: '#22c55e', icon: '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>' },
]

async function loadData() {
  loading.value = true
  try {
    const res = await api.get('/admin/dashboard')
    const d   = res.data
    raw.value            = d.statistik        ?? {}
    antrianPerPoli.value = d.antrian_per_poli ?? []
    grafikMingguan.value = d.grafik_mingguan  ?? []
    antrianAktif.value   = d.antrian_aktif    ?? []
  } catch (e) {
    console.error('Dashboard load error:', e)
  } finally {
    loading.value = false
  }
}

const maxGrafik = computed(() => Math.max(...grafikMingguan.value.map(g => g.total), 1))
function chartHeight(total) { return Math.max(Math.round((total / maxGrafik.value) * 100), 4) }

const maxPoli = computed(() => Math.max(...antrianPerPoli.value.map(p => p.total), 1))
function pctBar(total) { return Math.min(Math.round((total / maxPoli.value) * 100), 100) + '%' }
function barColor(total) {
  const p = maxPoli.value ? (total / maxPoli.value) * 100 : 0
  return p >= 80 ? '#ef4444' : p >= 50 ? '#f59e0b' : '#14b8a6'
}

function stCls(s) {
  return { menunggu:'st-wait', dipanggil:'st-call', dilayani:'st-serve', selesai:'st-done', batal:'st-cancel' }[s] ?? 'st-done'
}

onMounted(() => { loadData(); timer = setInterval(loadData, 60000) })
onUnmounted(() => clearInterval(timer))
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
  --bg:        #0b1222;
  --surface:   #152033;
  --surface2:  #1a2a40;
  --border:    #243450;
  --text:      #e8f0f8;
  --text2:     #8ba3bb;
  --text3:     #4a6380;
  --shadow:    0 1px 4px rgba(0,0,0,.5);
  --shadow-md: 0 4px 16px rgba(0,0,0,.4);
}

*, *::before, *::after { box-sizing: border-box; }

.page {
  padding: 2rem 2.25rem;
  background: var(--bg); min-height: 100vh;
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  color: var(--text);
}

/* ══ TOPBAR ══ */
.topbar {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 2rem; gap: 1rem;
}
.page-title { font-size: 1.875rem; font-weight: 800; letter-spacing: -0.035em; margin: 0; line-height: 1.15; }
.page-sub   { font-size: 0.925rem; color: var(--text2); margin: 0.35rem 0 0; line-height: 1.5; }
.page-sub strong { color: var(--text); font-weight: 700; }
.topbar-right { display: flex; align-items: center; gap: 0.6rem; flex-shrink: 0; margin-top: 0.25rem; }



/* ══ STAT CARDS ══ */
.stats-grid {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 1rem; margin-bottom: 1.25rem;
}
.stat-card {
  background: var(--surface); border: 1.5px solid var(--border);
  border-radius: 14px; padding: 1.4rem 1.5rem;
  display: flex; align-items: center; gap: 1.1rem;
  box-shadow: var(--shadow); position: relative; overflow: hidden;
  animation: fadeUp .4s ease both; animation-delay: var(--delay, 0s);
  transition: transform .2s, box-shadow .2s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.stat-accent-bar { position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent); border-radius: 14px 14px 0 0; }
.stat-icon { width: 52px; height: 52px; border-radius: 13px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.stat-content { display: flex; flex-direction: column; gap: 4px; }
.stat-num   { font-size: 2.1rem; font-weight: 800; letter-spacing: -0.04em; line-height: 1; }
.stat-label { font-size: 0.85rem; color: var(--text2); font-weight: 500; white-space: nowrap; }

/* ══ MID GRID ══ */
.mid-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.1rem; margin-bottom: 1.1rem; }

/* ══ PANELS ══ */
.panel {
  background: var(--surface); border: 1.5px solid var(--border);
  border-radius: 14px; overflow: hidden;
  box-shadow: var(--shadow); margin-bottom: 1.1rem;
}
.panel:last-child { margin-bottom: 0; }
.panel-head { display: flex; align-items: center; justify-content: space-between; padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border); }
.panel-head-left { display: flex; align-items: center; gap: 0.6rem; }
.panel-title { font-size: 1rem; font-weight: 700; letter-spacing: -0.01em; }
.badge { font-size: 0.775rem; font-weight: 600; color: var(--text3); background: var(--surface2); border: 1px solid var(--border); padding: 0.25rem 0.8rem; border-radius: 999px; }
.badge-teal { background: rgba(20,184,166,.1); color: #14b8a6; border-color: rgba(20,184,166,.3); }
.panel-body { padding: 1.35rem 1.5rem; }

/* Skeleton */
.skeletons { display: flex; flex-direction: column; gap: 0.75rem; }
.skel { height: 46px; border-radius: 9px; background: linear-gradient(90deg, var(--surface2) 25%, var(--border) 50%, var(--surface2) 75%); background-size: 200%; animation: shimmer 1.2s infinite; }

/* Empty */
.empty { display: flex; flex-direction: column; align-items: center; gap: 0.75rem; color: var(--text3); padding: 1rem 0; }
.empty svg { opacity: .4; } .empty p { margin: 0; font-size: 0.95rem; }

/* ══ POLI LIST ══ */
.poli-list { display: flex; flex-direction: column; gap: 1.1rem; }
.poli-row  { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem; }
.poli-name  { font-size: 0.925rem; font-weight: 600; }
.poli-count { font-size: 0.83rem; color: var(--text3); font-weight: 500; }
.progress-bg   { background: var(--surface2); border: 1px solid var(--border); border-radius: 999px; height: 7px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 999px; transition: width .6s ease; }

/* ══ MENU CARDS ══ */
.menu-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.7rem; }
.menu-card {
  display: flex; align-items: center; gap: 0.8rem;
  padding: 1rem 1.1rem; border-radius: 11px; border: 1.5px solid var(--border);
  background: var(--surface2); text-decoration: none; color: var(--text); transition: all .2s;
}
.menu-card:hover { border-color: var(--mc, #14b8a6); background: var(--surface); transform: translateY(-2px); box-shadow: var(--shadow-md); }
.menu-icon  { width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.menu-label { font-size: 0.9rem; font-weight: 600; flex: 1; }
.menu-arrow { color: var(--text3); opacity: 0; transition: opacity .18s, transform .18s, color .18s; }
.menu-card:hover .menu-arrow { opacity: 1; transform: translateX(3px); color: var(--mc, #14b8a6); }

/* ══ CHART ══ */
.chart-wrap { overflow-x: auto; }
.chart-bars { display: flex; align-items: flex-end; gap: 0.6rem; height: 160px; min-width: 300px; }
.chart-col  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; height: 100%; }
.chart-val  { font-size: 0.775rem; font-weight: 700; color: var(--text2); }
.chart-bar-wrap { flex: 1; width: 100%; display: flex; align-items: flex-end; }
.chart-bar  { width: 100%; min-height: 4px; border-radius: 5px 5px 0 0; background: linear-gradient(to top, #0ea5e9, #14b8a6); transition: height .5s cubic-bezier(.4,0,.2,1); }
.chart-label { font-size: 0.71rem; color: var(--text3); text-align: center; white-space: nowrap; font-weight: 500; }

/* ══ TABLE ══ */
.loading-row { display: flex; align-items: center; justify-content: center; gap: 0.875rem; padding: 3.5rem; color: var(--text3); font-size: 0.95rem; }
.spinner { width: 24px; height: 24px; border: 2.5px solid var(--border); border-top-color: #14b8a6; border-radius: 50%; animation: spin .7s linear infinite; flex-shrink: 0; }
.table-wrap { overflow-x: auto; }
.tbl { width: 100%; border-collapse: collapse; }
.tbl th { background: var(--surface2); padding: 0.9rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 700; color: var(--text3); text-transform: uppercase; letter-spacing: 0.07em; border-bottom: 1px solid var(--border); white-space: nowrap; }
.tbl td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); font-size: 0.925rem; }
.tbl tbody tr:last-child td { border-bottom: none; }
.tbl tbody tr { transition: background .12s; }
.tbl tbody tr:hover td { background: var(--surface2); }

.num-badge { background: var(--surface2); border: 1.5px solid var(--border); padding: 0.28rem 0.75rem; border-radius: 7px; font-weight: 700; font-size: 0.9rem; font-variant-numeric: tabular-nums; }
.patient { display: flex; align-items: center; gap: 0.65rem; font-weight: 500; }
.av { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #14b8a6, #0ea5e9); color: #fff; font-size: 0.8rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.cell-muted { color: var(--text2); }
.cell-mono  { font-variant-numeric: tabular-nums; font-size: 0.88rem; }

.st-pill { display: inline-block; padding: 0.3rem 0.8rem; border-radius: 7px; font-size: 0.82rem; font-weight: 700; }
.st-wait   { background: rgba(245,158,11,.12); color: #d97706; }
.st-call   { background: rgba(59,130,246,.12);  color: #2563eb; }
.st-serve  { background: rgba(34,197,94,.12);   color: #16a34a; }
.st-done   { background: var(--surface2); color: var(--text3); border: 1px solid var(--border); }
.st-cancel { background: rgba(239,68,68,.12);   color: #dc2626; }

/* ══ RESPONSIVE ══ */
@media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 900px)  { .mid-grid { grid-template-columns: 1fr; } .page { padding: 1.5rem; } }
@media (max-width: 600px)  {
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .menu-grid  { grid-template-columns: 1fr; }
  .page       { padding: 1rem; }
  .topbar     { flex-direction: column; }
  .page-title { font-size: 1.5rem; }
  .stat-num   { font-size: 1.75rem; }
  .tbl th, .tbl td { padding: 0.8rem 1rem; }
}

@keyframes fadeUp  { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: none; } }
@keyframes spin    { to { transform: rotate(360deg); } }
@keyframes shimmer { to { background-position: -200% center; } }
</style>
