<template>
  <div class="d-flex flex-column gap-7">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Laporan Harian</h1>
        <span class="text-gray-500 fw-semibold fs-7">Rekap antrian per hari</span>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <input v-model="tanggal" type="date" class="form-control form-control-sm form-control-solid w-auto" @change="fetchData"/>
        <button class="btn btn-primary btn-sm" @click="fetchData">
          <i class="ki-outline ki-magnifier me-1"></i>Tampilkan
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="d-flex justify-content-center py-15">
      <span class="spinner-border text-primary"></span>
    </div>

    <template v-else-if="data">
      <div class="text-center">
        <h5 class="fw-bold text-gray-700">{{ data.tanggal_fmt }}</h5>
        <span v-if="data.avg_tunggu" class="text-gray-500 fs-7">
          Rata-rata waktu tunggu: <strong>{{ data.avg_tunggu }} menit</strong>
        </span>
      </div>

      <!-- Statistik cards -->
      <div class="row g-4">
        <div class="col-6 col-md-2" v-for="s in statList" :key="s.label">
          <div class="card card-flush text-center py-6 px-3 h-100">
            <div class="fw-bolder fs-2" :class="s.color">{{ s.value }}</div>
            <div class="text-gray-500 fw-semibold fs-8 mt-1">{{ s.label }}</div>
          </div>
        </div>
      </div>

      <!-- Grafik baris 1: Donut + Bar per poli -->
      <div class="row g-5">
        <!-- Donut status -->
        <div class="col-md-4">
          <div class="card card-flush h-100">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Distribusi Status Antrian</h3>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center pt-4 pb-6">
              <div v-if="totalAntrian === 0" class="text-center text-gray-400 py-10">
                <i class="ki-outline ki-chart-pie-too fs-3x d-block mb-3 opacity-25"></i>
                <span class="fs-7 fw-semibold">Tidak ada data antrian</span>
              </div>
              <template v-else>
                <div style="position:relative;width:200px;height:200px">
                  <canvas ref="donutRef"></canvas>
                  <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none">
                    <span class="fw-bolder fs-2 text-gray-800">{{ totalAntrian }}</span>
                    <span class="text-gray-400 fs-9 fw-semibold">Total</span>
                  </div>
                </div>
                <div class="d-flex flex-wrap gap-3 justify-content-center mt-5">
                  <div v-for="l in donutLegend" :key="l.label" class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-inline-block" :style="`width:9px;height:9px;background:${l.color};flex-shrink:0`"></span>
                    <span class="text-gray-600 fs-8 fw-semibold">{{ l.label }} <span class="text-gray-400">({{ l.value }})</span></span>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Bar per poli -->
        <div class="col-md-8">
          <div class="card card-flush h-100">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Antrian per Poliklinik</h3>
            </div>
            <div class="card-body pt-4 d-flex align-items-center">
              <div v-if="!data.per_poli?.length" class="text-center text-gray-400 py-10 w-100">
                <i class="ki-outline ki-chart-simple fs-3x d-block mb-3 opacity-25"></i>
                <span class="fs-7 fw-semibold">Tidak ada data poliklinik</span>
              </div>
              <canvas v-else ref="barPoliRef" style="max-height:230px;width:100%"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Grafik baris 2: Bar horizontal per dokter -->
      <div class="card card-flush">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">Antrian per Dokter</h3>
        </div>
        <div class="card-body pt-4">
          <div v-if="!data.per_dokter?.length" class="text-center text-gray-400 py-10">
            <i class="ki-outline ki-doctor fs-3x d-block mb-3 opacity-25"></i>
            <span class="fs-7 fw-semibold">Tidak ada data dokter</span>
          </div>
          <canvas v-else ref="barDokterRef" style="max-height:220px"></canvas>
        </div>
      </div>

      <!-- Tabel per poli + per dokter -->
      <div class="row g-5">
        <div class="col-md-6">
          <div class="card card-flush h-100">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Per Poliklinik</h3>
            </div>
            <div class="card-body pt-3 p-0">
              <table class="table table-row-dashed align-middle gs-5 gy-3 mb-0">
                <thead>
                  <tr class="fw-bold text-gray-500 fs-8 text-uppercase">
                    <th>Poli</th><th class="text-center">Total</th><th class="text-center">Selesai</th><th class="text-center">BPJS</th><th class="text-center">Umum</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="p in data.per_poli" :key="p.poli">
                    <td class="fw-semibold text-gray-800">{{ p.poli }}</td>
                    <td class="text-center fw-bold">{{ p.total }}</td>
                    <td class="text-center text-success">{{ p.selesai }}</td>
                    <td class="text-center text-info">{{ p.bpjs }}</td>
                    <td class="text-center text-primary">{{ p.umum }}</td>
                  </tr>
                  <tr v-if="!data.per_poli?.length">
                    <td colspan="5" class="text-center text-gray-400 py-5">Tidak ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card card-flush h-100">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Per Dokter</h3>
            </div>
            <div class="card-body pt-3 p-0">
              <table class="table table-row-dashed align-middle gs-5 gy-3 mb-0">
                <thead>
                  <tr class="fw-bold text-gray-500 fs-8 text-uppercase">
                    <th>Dokter</th><th class="text-center">Total</th><th class="text-center">Selesai</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="d in data.per_dokter" :key="d.dokter">
                    <td class="fw-semibold text-gray-800">{{ d.dokter }}</td>
                    <td class="text-center fw-bold">{{ d.total }}</td>
                    <td class="text-center text-success">{{ d.selesai }}</td>
                  </tr>
                  <tr v-if="!data.per_dokter?.length">
                    <td colspan="3" class="text-center text-gray-400 py-5">Tidak ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Detail Antrian -->
      <div class="card card-flush">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">Detail Antrian</h3>
          <div class="card-toolbar">
            <span class="text-gray-400 fs-7">{{ data.detail?.length ?? 0 }} antrian</span>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-row-dashed align-middle gs-5 gy-3 mb-0">
              <thead>
                <tr class="fw-bold text-gray-500 fs-7 text-uppercase border-bottom">
                  <th>No.</th><th>Pasien</th><th>No. RM</th><th>Poli</th><th>Dokter</th>
                  <th class="text-center">Jaminan</th><th class="text-center">Status</th>
                  <th>Daftar</th><th>Selesai</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="a in data.detail" :key="a.nomor">
                  <td><span class="badge badge-light-primary fw-bold">{{ a.nomor }}</span></td>
                  <td class="fw-semibold text-gray-800">{{ a.pasien }}</td>
                  <td class="text-gray-500 fs-8">{{ a.no_rm }}</td>
                  <td class="text-gray-600 fs-7">{{ a.poli }}</td>
                  <td class="text-gray-600 fs-7">{{ a.dokter }}</td>
                  <td class="text-center"><span class="badge badge-light-info fs-9">{{ a.jenis_pasien }}</span></td>
                  <td class="text-center"><span class="badge fs-9" :class="statusBadge(a.status)">{{ a.status }}</span></td>
                  <td class="text-gray-500 fs-8">{{ a.waktu_daftar }}</td>
                  <td class="text-gray-500 fs-8">{{ a.waktu_selesai || '-' }}</td>
                </tr>
                <tr v-if="!data.detail?.length">
                  <td colspan="9" class="text-center text-gray-400 py-8">Tidak ada data antrian</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>

    <!-- Empty state -->
    <div v-else-if="!loading" class="text-center py-15 text-gray-400">
      <i class="ki-outline ki-calendar fs-5x mb-3 d-block opacity-50"></i>
      <p class="fw-semibold fs-6">Pilih tanggal lalu klik Tampilkan</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue'
import api from '@/services/api'

const COLORS = {
  primary:  '#009ef7',
  selesai:  '#50cd89',
  menunggu: '#ffc700',
  batal:    '#f1416c',
  dilayani: '#009ef7',
  purple:   '#7239ea',
}

let ChartJS: any = null
const loadChart = async () => {
  if (!ChartJS) { const m = await import('chart.js/auto'); ChartJS = m.default }
}

const loading      = ref(false)
const tanggal      = ref((() => {
  const d = new Date()
  return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
})())
const data         = ref<any>(null)
const donutRef     = ref<HTMLCanvasElement | null>(null)
const barPoliRef   = ref<HTMLCanvasElement | null>(null)
const barDokterRef = ref<HTMLCanvasElement | null>(null)
let donutChart: any = null, barPoliChart: any = null, barDokterChart: any = null

const totalAntrian = computed(() => data.value?.statistik?.total ?? 0)

const statList = computed(() => [
  { label: 'Total',    value: data.value?.statistik?.total    ?? 0, color: 'text-gray-800' },
  { label: 'Selesai',  value: data.value?.statistik?.selesai  ?? 0, color: 'text-success'  },
  { label: 'Batal',    value: data.value?.statistik?.batal    ?? 0, color: 'text-danger'   },
  { label: 'Menunggu', value: data.value?.statistik?.menunggu ?? 0, color: 'text-warning'  },
  { label: 'BPJS',     value: data.value?.statistik?.bpjs     ?? 0, color: 'text-info'     },
  { label: 'Umum',     value: data.value?.statistik?.umum     ?? 0, color: 'text-primary'  },
])

const donutLegend = computed(() => [
  { label: 'Selesai',  value: data.value?.statistik?.selesai  ?? 0, color: COLORS.selesai  },
  { label: 'Menunggu', value: data.value?.statistik?.menunggu ?? 0, color: COLORS.menunggu },
  { label: 'Batal',    value: data.value?.statistik?.batal    ?? 0, color: COLORS.batal    },
  { label: 'Dilayani', value: data.value?.statistik?.dilayani ?? 0, color: COLORS.dilayani },
])

const statusBadge = (s: string) => ({
  menunggu: 'badge-light-warning', dilayani: 'badge-light-primary',
  selesai:  'badge-light-success', batal:    'badge-light-danger',
}[s] ?? 'badge-light-secondary')

function destroyCharts() {
  donutChart?.destroy();     donutChart     = null
  barPoliChart?.destroy();   barPoliChart   = null
  barDokterChart?.destroy(); barDokterChart = null
}

function renderDonut() {
  if (!donutRef.value || !ChartJS || totalAntrian.value === 0) return
  donutChart = new ChartJS(donutRef.value, {
    type: 'doughnut',
    data: {
      labels: ['Selesai', 'Menunggu', 'Batal', 'Dilayani'],
      datasets: [{
        data: [
          data.value?.statistik?.selesai  ?? 0,
          data.value?.statistik?.menunggu ?? 0,
          data.value?.statistik?.batal    ?? 0,
          data.value?.statistik?.dilayani ?? 0,
        ],
        backgroundColor: [COLORS.selesai, COLORS.menunggu, COLORS.batal, COLORS.dilayani],
        borderWidth: 3, borderColor: '#fff', hoverOffset: 8,
      }],
    },
    options: {
      cutout: '72%',
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (ctx: any) => ` ${ctx.label}: ${ctx.raw} (${totalAntrian.value ? Math.round((ctx.raw / totalAntrian.value) * 100) : 0}%)` } },
      },
      animation: { duration: 600 },
    },
  })
}

function renderBarPoli() {
  if (!barPoliRef.value || !ChartJS || !data.value?.per_poli?.length) return
  const poli = data.value.per_poli
  barPoliChart = new ChartJS(barPoliRef.value, {
    type: 'bar',
    data: {
      labels: poli.map((p: any) => p.poli),
      datasets: [
        { label: 'Total',   data: poli.map((p: any) => p.total),   backgroundColor: 'rgba(0,158,247,0.15)',  borderColor: COLORS.primary, borderWidth: 2, borderRadius: 6, borderSkipped: false },
        { label: 'Selesai', data: poli.map((p: any) => p.selesai), backgroundColor: 'rgba(80,205,137,0.2)',  borderColor: COLORS.selesai, borderWidth: 2, borderRadius: 6, borderSkipped: false },
        { label: 'BPJS',    data: poli.map((p: any) => p.bpjs),    backgroundColor: 'rgba(114,57,234,0.15)', borderColor: COLORS.purple,  borderWidth: 2, borderRadius: 6, borderSkipped: false },
      ],
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16, font: { size: 11 } } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, stepSize: 1 } },
      },
    },
  })
}

function renderBarDokter() {
  if (!barDokterRef.value || !ChartJS || !data.value?.per_dokter?.length) return
  const dokter = data.value.per_dokter
  const bgColors  = ['rgba(0,158,247,0.2)', 'rgba(80,205,137,0.2)', 'rgba(255,199,0,0.2)', 'rgba(241,65,108,0.2)', 'rgba(114,57,234,0.2)']
  const bdrColors = [COLORS.primary, COLORS.selesai, COLORS.menunggu, COLORS.batal, COLORS.purple]
  barDokterChart = new ChartJS(barDokterRef.value, {
    type: 'bar',
    data: {
      labels: dokter.map((d: any) => d.dokter),
      datasets: [
        {
          label: 'Total Antrian',
          data: dokter.map((d: any) => d.total),
          backgroundColor: dokter.map((_: any, i: number) => bgColors[i % bgColors.length]),
          borderColor:      dokter.map((_: any, i: number) => bdrColors[i % bdrColors.length]),
          borderWidth: 2, borderRadius: 8, borderSkipped: false,
        },
        {
          label: 'Selesai',
          data: dokter.map((d: any) => d.selesai),
          backgroundColor: 'rgba(80,205,137,0.6)',
          borderColor: COLORS.selesai,
          borderWidth: 1.5, borderRadius: 8, borderSkipped: false,
        },
      ],
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16, font: { size: 11 } } } },
      scales: {
        x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, stepSize: 1 } },
        y: { grid: { display: false }, ticks: { font: { size: 11 } } },
      },
    },
  })
}

async function fetchData() {
  loading.value = true
  destroyCharts()
  try {
    const res  = await api.get('/admin/laporan/harian', { params: { tanggal: tanggal.value } })
    data.value = res.data?.data ?? res.data
    await nextTick()
    await loadChart()
    renderDonut()
    renderBarPoli()
    renderBarDokter()
  } catch (e) {
    console.error('[Harian] fetchData error:', e)
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
onBeforeUnmount(destroyCharts)
</script>
