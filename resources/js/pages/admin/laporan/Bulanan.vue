<template>
  <div class="d-flex flex-column gap-7">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Laporan Bulanan</h1>
        <span class="text-gray-500 fw-semibold fs-7">Rekap antrian per bulan</span>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <select v-model="bulan" class="form-select form-select-sm form-select-solid w-auto">
          <option v-for="(b, i) in bulanList" :key="i" :value="i+1">{{ b }}</option>
        </select>
        <select v-model="tahun" class="form-select form-select-sm form-select-solid w-auto">
          <option v-for="t in tahunList" :key="t" :value="t">{{ t }}</option>
        </select>
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
        <h5 class="fw-bold text-gray-700">{{ data.bulan_fmt }}</h5>
        <span v-if="data.hari_tersibuk" class="text-gray-500 fs-7">
          Hari tersibuk: <strong>{{ data.hari_tersibuk.label }}</strong>
          ({{ data.hari_tersibuk.total }} antrian)
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

      <!-- Line chart tren harian -->
      <div class="card card-flush">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">Tren Antrian Harian</h3>
          <div class="card-toolbar">
            <span v-if="data.hari_tersibuk" class="badge badge-light-primary fs-8">
              Puncak: {{ data.hari_tersibuk.label }} — {{ data.hari_tersibuk.total }} antrian
            </span>
          </div>
        </div>
        <div class="card-body pt-4">
          <div v-if="!data.grafik_harian?.length" class="text-center text-gray-400 py-10">
            <i class="ki-outline ki-chart-line fs-3x d-block mb-3 opacity-25"></i>
            <span class="fs-7 fw-semibold">Tidak ada data grafik harian</span>
          </div>
          <canvas v-else ref="lineRef" style="max-height:260px"></canvas>
        </div>
      </div>

      <!-- Donut + Bar per poli -->
      <div class="row g-5">
        <!-- Donut distribusi jaminan -->
        <div class="col-md-4">
          <div class="card card-flush h-100">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Jaminan Pasien</h3>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center pt-4 pb-6">
              <div v-if="!data.statistik?.total" class="text-center text-gray-400 py-10">
                <i class="ki-outline ki-chart-pie-too fs-3x d-block mb-3 opacity-25"></i>
                <span class="fs-7 fw-semibold">Tidak ada data</span>
              </div>
              <template v-else>
                <div style="position:relative;width:190px;height:190px">
                  <canvas ref="donutRef"></canvas>
                  <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none">
                    <span class="fw-bolder fs-2 text-gray-800">{{ data.statistik?.total }}</span>
                    <span class="text-gray-400 fs-9 fw-semibold">Total</span>
                  </div>
                </div>
                <div class="d-flex flex-wrap gap-3 justify-content-center mt-5">
                  <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-inline-block" style="width:9px;height:9px;background:#009ef7;flex-shrink:0"></span>
                    <span class="text-gray-600 fs-8 fw-semibold">BPJS <span class="text-gray-400">({{ data.statistik?.bpjs ?? 0 }})</span></span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-inline-block" style="width:9px;height:9px;background:#7239ea;flex-shrink:0"></span>
                    <span class="text-gray-600 fs-8 fw-semibold">Umum <span class="text-gray-400">({{ data.statistik?.umum ?? 0 }})</span></span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-inline-block" style="width:9px;height:9px;background:#50cd89;flex-shrink:0"></span>
                    <span class="text-gray-600 fs-8 fw-semibold">Selesai <span class="text-gray-400">({{ data.statistik?.selesai ?? 0 }})</span></span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-inline-block" style="width:9px;height:9px;background:#f1416c;flex-shrink:0"></span>
                    <span class="text-gray-600 fs-8 fw-semibold">Batal <span class="text-gray-400">({{ data.statistik?.batal ?? 0 }})</span></span>
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

      <!-- Top Dokter bar + tabel poli -->
      <div class="row g-5">
        <!-- Top 5 Dokter: bar chart -->
        <div class="col-md-5">
          <div class="card card-flush h-100">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Top 5 Dokter</h3>
            </div>
            <div class="card-body pt-4">
              <div v-if="!data.top_dokter?.length" class="text-center text-gray-400 py-10">
                <i class="ki-outline ki-doctor fs-3x d-block mb-3 opacity-25"></i>
                <span class="fs-7 fw-semibold">Tidak ada data dokter</span>
              </div>
              <template v-else>
                <canvas ref="barDokterRef" style="max-height:220px"></canvas>
                <!-- Ranking list di bawah chart -->
                <div class="mt-5">
                  <div v-for="(d, i) in data.top_dokter" :key="d.dokter"
                    class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                      <div class="symbol symbol-30px">
                        <div class="symbol-label fw-bold text-white fs-8 rounded"
                          :style="`background:${rankColors[i]}`">
                          {{ i + 1 }}
                        </div>
                      </div>
                      <span class="fw-semibold text-gray-800 fs-7">{{ d.dokter }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <span class="fw-bolder text-gray-900 fs-6">{{ d.total }}</span>
                      <span class="text-gray-400 fs-9">antrian</span>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <!-- Tabel per poli -->
        <div class="col-md-7">
          <div class="card card-flush h-100">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Per Poliklinik</h3>
            </div>
            <div class="card-body p-0">
              <table class="table table-row-dashed align-middle gs-5 gy-3 mb-0">
                <thead>
                  <tr class="fw-bold text-gray-500 fs-8 text-uppercase">
                    <th>Poli</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Selesai</th>
                    <th class="text-center">BPJS</th>
                    <th class="text-center">Umum</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="p in data.per_poli" :key="p.poli">
                    <td class="fw-semibold text-gray-800">{{ p.poli }}</td>
                    <td class="text-center fw-bold text-primary">{{ p.total }}</td>
                    <td class="text-center text-success">{{ p.selesai }}</td>
                    <td class="text-center text-info">{{ p.bpjs }}</td>
                    <td class="text-center">{{ p.umum }}</td>
                  </tr>
                  <tr v-if="!data.per_poli?.length">
                    <td colspan="5" class="text-center text-gray-400 py-5">Tidak ada data</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Empty state -->
    <div v-else class="text-center py-15 text-gray-400">
      <i class="ki-outline ki-chart fs-5x mb-3 d-block opacity-50"></i>
      <p class="fw-semibold fs-6">Pilih bulan dan tahun lalu klik Tampilkan</p>
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
  purple:   '#7239ea',
}
const rankColors = ['#009ef7', '#50cd89', '#ffc700', '#f1416c', '#7239ea']

let ChartJS: any = null
const loadChart = async () => {
  if (!ChartJS) { const m = await import('chart.js/auto'); ChartJS = m.default }
}

const loading     = ref(false)
const data        = ref<any>(null)
const now         = new Date()
const bulan       = ref(now.getMonth() + 1)
const tahun       = ref(now.getFullYear())
const lineRef     = ref<HTMLCanvasElement | null>(null)
const donutRef    = ref<HTMLCanvasElement | null>(null)
const barPoliRef  = ref<HTMLCanvasElement | null>(null)
const barDokterRef = ref<HTMLCanvasElement | null>(null)
let lineChart: any = null, donutChart: any = null, barPoliChart: any = null, barDokterChart: any = null

const bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
const tahunList = Array.from({ length: 5 }, (_, i) => now.getFullYear() - i)

const statList = computed(() => [
  { label: 'Total',       value: data.value?.statistik?.total      ?? 0, color: 'text-gray-800' },
  { label: 'Selesai',     value: data.value?.statistik?.selesai    ?? 0, color: 'text-success'  },
  { label: 'Batal',       value: data.value?.statistik?.batal      ?? 0, color: 'text-danger'   },
  { label: 'BPJS',        value: data.value?.statistik?.bpjs       ?? 0, color: 'text-info'     },
  { label: 'Umum',        value: data.value?.statistik?.umum       ?? 0, color: 'text-primary'  },
  { label: 'Pasien Baru', value: data.value?.statistik?.pasienBaru ?? 0, color: 'text-warning'  },
])

function destroyCharts() {
  lineChart?.destroy();      lineChart      = null
  donutChart?.destroy();     donutChart     = null
  barPoliChart?.destroy();   barPoliChart   = null
  barDokterChart?.destroy(); barDokterChart = null
}

function renderLine() {
  if (!lineRef.value || !ChartJS || !data.value?.grafik_harian?.length) return
  const g = data.value.grafik_harian
  lineChart = new ChartJS(lineRef.value, {
    type: 'line',
    data: {
      labels: g.map((d: any) => d.label ?? d.tanggal),
      datasets: [
        {
          label: 'Total Antrian',
          data: g.map((d: any) => d.total),
          borderColor: COLORS.primary,
          backgroundColor: 'rgba(0,158,247,0.08)',
          borderWidth: 2.5,
          tension: 0.35,
          fill: true,
          pointRadius: 4,
          pointBackgroundColor: COLORS.primary,
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointHoverRadius: 6,
        },
        ...(g[0]?.selesai !== undefined ? [{
          label: 'Selesai',
          data: g.map((d: any) => d.selesai ?? 0),
          borderColor: COLORS.selesai,
          backgroundColor: 'transparent',
          borderWidth: 2,
          borderDash: [5, 4],
          tension: 0.35,
          fill: false,
          pointRadius: 3,
          pointBackgroundColor: COLORS.selesai,
          pointBorderColor: '#fff',
          pointBorderWidth: 1.5,
        }] : []),
      ],
    },
    options: {
      responsive: true,
      interaction: { mode: 'index' as const, intersect: false },
      plugins: {
        legend: { position: 'bottom' as const, labels: { boxWidth: 12, padding: 16, font: { size: 11 } } },
        tooltip: { callbacks: { title: (items: any[]) => `Tanggal: ${items[0].label}` } },
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45 } },
        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, stepSize: 1 } },
      },
      animation: { duration: 700 },
    },
  })
}

function renderDonut() {
  if (!donutRef.value || !ChartJS || !data.value?.statistik?.total) return
  const s = data.value.statistik
  donutChart = new ChartJS(donutRef.value, {
    type: 'doughnut',
    data: {
      labels: ['BPJS', 'Umum', 'Selesai', 'Batal'],
      datasets: [{
        data: [s.bpjs ?? 0, s.umum ?? 0, s.selesai ?? 0, s.batal ?? 0],
        backgroundColor: [COLORS.primary, COLORS.purple, COLORS.selesai, COLORS.batal],
        borderWidth: 3, borderColor: '#fff', hoverOffset: 8,
      }],
    },
    options: {
      cutout: '72%',
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx: any) => {
              const total = data.value?.statistik?.total ?? 1
              return ` ${ctx.label}: ${ctx.raw} (${Math.round((ctx.raw / total) * 100)}%)`
            },
          },
        },
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
      plugins: { legend: { position: 'bottom' as const, labels: { boxWidth: 12, padding: 16, font: { size: 11 } } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, stepSize: 1 } },
      },
    },
  })
}

function renderBarDokter() {
  if (!barDokterRef.value || !ChartJS || !data.value?.top_dokter?.length) return
  const dokter = data.value.top_dokter
  barDokterChart = new ChartJS(barDokterRef.value, {
    type: 'bar',
    data: {
      labels: dokter.map((d: any) => d.dokter),
      datasets: [{
        label: 'Antrian',
        data: dokter.map((d: any) => d.total),
        backgroundColor: rankColors.map(c => c + '33'),
        borderColor: rankColors,
        borderWidth: 2,
        borderRadius: 8,
        borderSkipped: false,
      }],
    },
    options: {
      indexAxis: 'y' as const,
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, stepSize: 1 } },
        y: { grid: { display: false }, ticks: { font: { size: 11 } } },
      },
      animation: { duration: 500 },
    },
  })
}

async function fetchData() {
  loading.value = true
  destroyCharts()
  data.value = null
  try {
    const res  = await api.get('/admin/laporan/bulanan', { params: { bulan: bulan.value, tahun: tahun.value } })
    data.value = res.data.data
    await nextTick()
    await loadChart()
    renderLine()
    renderDonut()
    renderBarPoli()
    renderBarDokter()
  } catch (e: any) {
    console.error('Gagal memuat laporan:', e)
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
onBeforeUnmount(destroyCharts)
</script>
