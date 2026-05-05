<template>
  <div class="d-flex flex-column gap-7">

    <!-- TOOLBAR -->
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Daftar Antrian</h1>
        <span class="text-gray-500 fw-semibold fs-7">Daftar poli online tanpa perlu antre di loket</span>
      </div>
    </div>

    <!-- LOGIN WARNING -->
    <div v-if="!authStore.isLoggedIn" class="alert alert-warning d-flex align-items-center py-4">
      <i class="ki-outline ki-information-5 fs-2hx text-warning me-4"></i>
      <div class="flex-grow-1">
        <span class="fw-bold">Anda belum login.</span>
        Silakan login terlebih dahulu untuk mendaftar antrian.
      </div>
      <a href="/login" class="btn btn-sm btn-warning ms-4">Login Sekarang</a>
    </div>

    <!-- STEP INDICATOR -->
    <div class="card card-flush">
      <div class="card-body py-6">
        <div class="d-flex align-items-center justify-content-center gap-4">

          <div class="d-flex align-items-center gap-2">
            <div class="w-35px h-35px rounded-circle d-flex align-items-center justify-content-center fw-bold"
              :style="step > 1 ? 'background:#dcfce7;color:#16a34a' : step === 1 ? 'background:#0ea5e9;color:white' : 'background:#f1f5f9;color:#94a3b8'">
              <i v-if="step > 1" class="ki-outline ki-check fs-4"></i>
              <span v-else>1</span>
            </div>
            <span class="fw-semibold fs-7" :class="step === 1 ? 'text-primary' : step > 1 ? 'text-success' : 'text-gray-400'">Pilih Poli</span>
          </div>

          <div class="flex-grow-1 h-2px" style="max-width:50px" :style="{ background: step > 1 ? '#0ea5e9' : '#e2e8f0' }"></div>

          <div class="d-flex align-items-center gap-2">
            <div class="w-35px h-35px rounded-circle d-flex align-items-center justify-content-center fw-bold"
              :style="step > 2 ? 'background:#dcfce7;color:#16a34a' : step === 2 ? 'background:#0ea5e9;color:white' : 'background:#f1f5f9;color:#94a3b8'">
              <i v-if="step > 2" class="ki-outline ki-check fs-4"></i>
              <span v-else>2</span>
            </div>
            <span class="fw-semibold fs-7" :class="step === 2 ? 'text-primary' : step > 2 ? 'text-success' : 'text-gray-400'">Pilih Jadwal</span>
          </div>

          <div class="flex-grow-1 h-2px" style="max-width:50px" :style="{ background: step > 2 ? '#0ea5e9' : '#e2e8f0' }"></div>

          <div class="d-flex align-items-center gap-2">
            <div class="w-35px h-35px rounded-circle d-flex align-items-center justify-content-center fw-bold"
              :style="step === 3 ? 'background:#0ea5e9;color:white' : 'background:#f1f5f9;color:#94a3b8'">
              <span>3</span>
            </div>
            <span class="fw-semibold fs-7" :class="step === 3 ? 'text-primary' : 'text-gray-400'">Konfirmasi</span>
          </div>

        </div>
      </div>
    </div>

    <!-- ERROR ALERT -->
    <div v-if="errorMsg" class="alert alert-danger d-flex align-items-center py-4">
      <i class="ki-outline ki-information-5 fs-2hx text-danger me-4"></i>
      <div class="flex-grow-1">{{ errorMsg }}</div>
      <button type="button" class="btn-close" @click="errorMsg = ''"></button>
    </div>

    <!-- ══ STEP 1: PILIH POLI ══ -->
    <div v-if="step === 1 && !submitted">
      <div v-if="loadingPoli" class="d-flex justify-content-center py-15">
        <span class="spinner-border text-primary"></span>
      </div>
      <div v-else-if="poliList.length === 0" class="card card-flush">
        <div class="card-body d-flex flex-column align-items-center py-15">
          <i class="ki-outline ki-hospital fs-5x text-gray-300 mb-4"></i>
          <h3 class="text-gray-600 fw-bold mb-2">Tidak Ada Poli Tersedia</h3>
          <p class="text-gray-400 text-center">Belum ada sesi antrian yang dibuka hari ini. Coba lagi nanti.</p>
        </div>
      </div>
      <div v-else>
        <div class="row g-5 mb-7">
          <div v-for="poli in poliList" :key="poli.id" class="col-md-4">
            <div class="card card-flush h-100 cursor-pointer"
              :class="form.poli_id === poli.id
                ? 'border border-2 border-primary bg-light-primary'
                : 'border border-2 border-transparent bg-white'"
              style="transition: all 0.2s;"
              @click="pilihPoli(poli)">
              <div class="card-body d-flex align-items-center gap-4 py-6">
                <div class="symbol symbol-50px flex-shrink-0">
                  <div class="symbol-label fw-bold fs-3 rounded" :style="poliColor(poli.id)">
                    {{ poliInitial(poli.nama) }}
                  </div>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <div class="fw-bold text-gray-900 fs-6">{{ poli.nama }}</div>
                  <div class="text-gray-500 fs-7 text-truncate">{{ poli.keterangan || 'Pelayanan poliklinik' }}</div>
                </div>
                <i v-if="form.poli_id === poli.id" class="ki-outline ki-check-circle fs-1 text-primary flex-shrink-0"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button class="btn btn-primary" @click="nextToStep2" :disabled="!form.poli_id">
            Lanjut — Pilih Jadwal
            <i class="ki-outline ki-arrow-right ms-2"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- ══ STEP 2: PILIH DOKTER & SESI ══ -->
    <div v-if="step === 2 && !submitted">

      <div class="d-flex align-items-center gap-3 mb-6">
        <span class="badge badge-light-primary fs-7 py-2 px-4">
          <i class="ki-outline ki-hospital me-1"></i>{{ selectedPoli?.nama }}
        </span>
        <button class="btn btn-sm btn-light" @click="step = 1">
          <i class="ki-outline ki-arrow-left me-1"></i> Ganti Poli
        </button>
      </div>

      <!-- Pilih Dokter -->
      <div class="card card-flush mb-5">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">Pilih Dokter</h3>
        </div>
        <div class="card-body pt-3">
          <div v-if="loadingDokter" class="d-flex justify-content-center py-6">
            <span class="spinner-border spinner-border-sm text-primary"></span>
          </div>
          <div v-else-if="dokterList.length === 0" class="text-center py-6 text-gray-400 fs-7">
            Tidak ada dokter tersedia untuk poli ini hari ini
          </div>
          <div v-else class="d-flex flex-column gap-3">
            <div v-for="d in dokterList" :key="d.id"
              class="d-flex align-items-center gap-4 p-4 rounded cursor-pointer border border-2"
              :class="form.dokter_id === d.id ? 'border-primary bg-light-primary' : 'border-transparent bg-light'"
              style="transition: all 0.2s;"
              @click="pilihDokter(d)">
              <div class="symbol symbol-40px flex-shrink-0">
                <div class="symbol-label fw-bold fs-5"
                  style="background: linear-gradient(135deg, #0ea5e9, #8b5cf6); color: white;">
                  {{ d.nama.charAt(0) }}
                </div>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold text-gray-900 fs-6">{{ d.nama }}</div>
                <div class="text-gray-500 fs-7">{{ d.spesialis || selectedPoli?.nama }}</div>
              </div>
              <i v-if="form.dokter_id === d.id" class="ki-outline ki-check-circle fs-1 text-primary"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Pilih Sesi -->
      <div v-if="form.dokter_id" class="card card-flush mb-5">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">Pilih Sesi Waktu</h3>
        </div>
        <div class="card-body pt-3">
          <div v-if="loadingSesi" class="d-flex justify-content-center py-6">
            <span class="spinner-border spinner-border-sm text-primary"></span>
          </div>
          <div v-else-if="sesiList.length === 0" class="text-center py-6 text-gray-400 fs-7">
            Tidak ada sesi tersedia untuk dokter ini hari ini
          </div>
          <div v-else class="row g-4">
            <div v-for="s in sesiList" :key="s.id" class="col-6 col-md-3">
              <div class="p-4 rounded text-center border border-2"
                :class="s.is_full
                  ? 'border-transparent bg-light opacity-50'
                  : form.sesi_id === s.id
                    ? 'border-primary bg-light-primary'
                    : 'border-transparent bg-light'"
                :style="s.is_full ? 'cursor:not-allowed' : 'cursor:pointer; transition:all 0.2s'"
                @click="!s.is_full && (form.sesi_id = s.id)">
                <div class="fw-bold text-gray-700 fs-8 mb-1">{{ s.tanggal_fmt ?? s.hari }}</div>
                <div class="fw-bold text-gray-900 fs-7">{{ s.jam_buka }} – {{ s.jam_tutup }}</div>
                <div class="mt-1 fs-8 fw-semibold" :class="s.is_full ? 'text-danger' : 'text-success'">
                  {{ s.is_full ? 'Penuh' : `${s.sisa} sisa` }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Jaminan -->
      <div class="card card-flush mb-5">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">Jenis Jaminan</h3>
        </div>
        <div class="card-body pt-3">
          <div class="d-flex gap-4">
            <div v-for="j in ['BPJS', 'Umum']" :key="j"
              class="flex-grow-1 d-flex align-items-center gap-3 p-4 rounded cursor-pointer border border-2"
              :class="form.jaminan === j ? 'border-primary bg-light-primary' : 'border-transparent bg-light'"
              style="transition: all 0.2s;"
              @click="form.jaminan = j; form.no_bpjs = ''">
              <i class="fs-2" :class="j === 'BPJS' ? 'ki-outline ki-shield-tick text-success' : 'ki-outline ki-credit-cart text-info'"></i>
              <span class="fw-bold fs-6">{{ j }}</span>
              <i v-if="form.jaminan === j" class="ki-outline ki-check-circle ms-auto text-primary"></i>
            </div>
          </div>
          <div v-if="form.jaminan === 'BPJS'" class="mt-4">
            <label class="form-label fw-semibold fs-7 required">Nomor BPJS</label>
            <input v-model="form.no_bpjs" type="text" class="form-control form-control-solid"
              placeholder="Masukkan nomor BPJS Anda"
              @input="form.no_bpjs = form.no_bpjs.replace(/\D/g, '')"/>
          </div>
        </div>
      </div>

      <!-- Keluhan -->
      <div class="card card-flush mb-5">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">
            Keluhan <span class="text-gray-400 fw-normal fs-7 ms-1">(opsional)</span>
          </h3>
        </div>
        <div class="card-body pt-3">
          <textarea v-model="form.keluhan" class="form-control form-control-solid" rows="3"
            placeholder="Tuliskan keluhan utama Anda..."></textarea>
        </div>
      </div>

      <div class="d-flex justify-content-between">
        <button class="btn btn-light btn-active-light-primary" @click="step = 1">
          <i class="ki-outline ki-arrow-left me-1"></i> Kembali
        </button>
        <button class="btn btn-primary" @click="step = 3"
          :disabled="!form.dokter_id || !form.sesi_id || !form.jaminan">
          Lanjut — Konfirmasi
          <i class="ki-outline ki-arrow-right ms-2"></i>
        </button>
      </div>
    </div>

    <!-- ══ STEP 3: KONFIRMASI ══ -->
    <div v-if="step === 3 && !submitted">
      <div class="card card-flush mb-6">
        <div class="card-header pt-6">
          <div class="card-title d-flex align-items-center gap-3">
            <div class="symbol symbol-45px">
              <div class="symbol-label bg-light-primary">
                <i class="ki-outline ki-document fs-2 text-primary"></i>
              </div>
            </div>
            <div>
              <h3 class="fw-bold mb-0 fs-5">Ringkasan Pendaftaran</h3>
              <span class="text-gray-500 fs-7">Periksa kembali data sebelum mendaftar</span>
            </div>
          </div>
        </div>
        <div class="card-body pt-4">
          <div class="separator separator-dashed mb-6"></div>
          <div class="fs-7 fw-bold text-gray-400 text-uppercase mb-3">Info Kunjungan</div>
          <div class="row g-4 mb-6">
            <div class="col-6">
              <div class="text-gray-500 fs-7 mb-1">Poliklinik</div>
              <div class="fw-bold text-gray-900">{{ selectedPoli?.nama }}</div>
            </div>
            <div class="col-6">
              <div class="text-gray-500 fs-7 mb-1">Dokter</div>
              <div class="fw-bold text-gray-900">{{ selectedDokter?.nama }}</div>
            </div>
            <div class="col-6">
              <div class="text-gray-500 fs-7 mb-1">Tanggal</div>
              <div class="fw-bold text-gray-900">{{ selectedSesi?.tanggal_fmt ?? formatTanggalSesi(selectedSesi) }}</div>
            </div>
            <div class="col-6">
              <div class="text-gray-500 fs-7 mb-1">Sesi</div>
              <div class="fw-bold text-gray-900">{{ selectedSesi?.jam_buka }} – {{ selectedSesi?.jam_tutup }}</div>
            </div>
            <div class="col-6">
              <div class="text-gray-500 fs-7 mb-1">Jaminan</div>
              <span class="badge badge-light-success">{{ form.jaminan }}</span>
            </div>
            <div v-if="form.no_bpjs" class="col-6">
              <div class="text-gray-500 fs-7 mb-1">No. BPJS</div>
              <div class="fw-bold text-gray-900">{{ form.no_bpjs }}</div>
            </div>
            <div v-if="form.keluhan" class="col-12">
              <div class="text-gray-500 fs-7 mb-1">Keluhan</div>
              <div class="fw-bold text-gray-900">{{ form.keluhan }}</div>
            </div>
          </div>
          <div class="separator separator-dashed mb-5"></div>
          <div class="fs-7 fw-bold text-gray-400 text-uppercase mb-3">Data Pasien</div>
          <div class="d-flex align-items-center gap-3 p-4 bg-light rounded">
            <div class="symbol symbol-40px">
              <div class="symbol-label bg-primary fw-bold text-white fs-5">
                {{ authStore.user?.name?.charAt(0) ?? 'P' }}
              </div>
            </div>
            <div>
              <div class="fw-bold text-gray-900">{{ authStore.user?.name }}</div>
              <div class="text-gray-500 fs-7">{{ authStore.user?.email }}</div>
            </div>
            <span class="badge badge-light-success ms-auto">
              <i class="ki-outline ki-shield-tick me-1"></i>Terverifikasi
            </span>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between">
        <button class="btn btn-light btn-active-light-primary" @click="step = 2">
          <i class="ki-outline ki-arrow-left me-1"></i> Kembali
        </button>
        <button class="btn btn-success" @click="submit" :disabled="loading">
          <span v-if="!loading">
            <i class="ki-outline ki-check me-2"></i> Daftar Sekarang
          </span>
          <span v-else class="d-flex align-items-center gap-2">
            <span class="spinner-border spinner-border-sm"></span> Mendaftarkan...
          </span>
        </button>
      </div>
    </div>

    <!-- ══ SUKSES ══ -->
    <div v-if="submitted" class="card card-flush">
      <div class="card-body d-flex flex-column align-items-center py-15 text-center">
        <div class="symbol symbol-100px mb-7">
          <div class="symbol-label bg-light-success">
            <i class="ki-outline ki-check-circle fs-5x text-success"></i>
          </div>
        </div>
        <h2 class="fw-bold text-gray-900 mb-2">Pendaftaran Berhasil!</h2>
        <p class="text-gray-500 fs-6 mb-9">Anda telah terdaftar di <strong>{{ selectedPoli?.nama }}</strong></p>

        <!-- ══ STRUK ══ -->
        <div class="card border border-dashed border-primary w-100 mb-9" style="max-width: 420px;">
          <div class="card-body p-8">
            <div class="d-flex justify-content-between align-items-center mb-5">
              <span class="fw-bold text-gray-500 fs-7">{{ selectedPoli?.nama }}</span>
              <span class="badge badge-light-success">Terdaftar</span>
            </div>
            <div class="text-center mb-6">
              <div class="text-gray-400 fs-7 mb-1">Nomor Antrian Anda</div>
              <div class="fw-bolder text-primary" style="font-size: 80px; line-height: 1; font-family: serif;">
                {{ nomorAntrian }}
              </div>
            </div>
            <div class="d-flex justify-content-center my-6">
              <div class="p-3 bg-white rounded border border-gray-200" style="display:inline-block;">
                <div ref="qrContainer" style="width:160px;height:160px;"></div>
              </div>
            </div>
            <div class="text-center text-gray-400 fs-8 mb-5">
              Scan QR ini di loket puskesmas
            </div>
            <div class="separator separator-dashed mb-5"></div>
            <div class="d-flex flex-column gap-3">
              <div class="d-flex justify-content-between fs-7">
                <span class="text-gray-500">Dokter</span>
                <span class="fw-bold text-gray-900">{{ selectedDokter?.nama }}</span>
              </div>
              <div class="d-flex justify-content-between fs-7">
                <span class="text-gray-500">Tanggal</span>
                <span class="fw-bold text-gray-900">{{ selectedSesi?.tanggal_fmt ?? formatTanggalSesi(selectedSesi) }}</span>
              </div>
              <div class="d-flex justify-content-between fs-7">
                <span class="text-gray-500">Jam</span>
                <span class="fw-bold text-gray-900">{{ selectedSesi?.jam_buka }} – {{ selectedSesi?.jam_tutup }}</span>
              </div>
              <div class="d-flex justify-content-between fs-7">
                <span class="text-gray-500">Jaminan</span>
                <span class="fw-bold text-gray-900">{{ form.jaminan }}</span>
              </div>
              <div class="d-flex justify-content-between fs-7">
                <span class="text-gray-500">Nama</span>
                <span class="fw-bold text-gray-900">{{ authStore.user?.name }}</span>
              </div>
            </div>
            <div class="text-center mt-5 pt-5 border-top border-dashed border-gray-300 text-gray-400 fs-8">
              Tunjukkan nomor ini saat tiba di puskesmas
            </div>
          </div>
        </div>

        <div class="d-flex gap-4 flex-wrap justify-content-center">
          <router-link to="/pasien/antrian/status" class="btn btn-primary">
            <i class="ki-outline ki-notification me-2"></i>
            Lihat Status Antrian
          </router-link>
          <button class="btn btn-light btn-active-light-primary" @click="resetForm">
            <i class="ki-outline ki-plus me-2"></i>
            Daftar Lagi
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth.store'

const router    = useRouter()
const authStore = useAuthStore()

// ── State ─────────────────────────────────────────────────────────────────
const step      = ref(1)
const loading   = ref(false)
const submitted = ref(false)
const errorMsg  = ref('')

const loadingPoli   = ref(false)
const loadingDokter = ref(false)
const loadingSesi   = ref(false)

const nomorAntrian = ref('')
const qrContainer  = ref<HTMLElement | null>(null)

const poliList   = ref<any[]>([])
const dokterList = ref<any[]>([])
const sesiList   = ref<any[]>([])

const form = ref({
  poli_id  : null as number | null,
  dokter_id: null as number | null,
  sesi_id  : null as number | null,
  jaminan  : '' as 'BPJS' | 'Umum' | '',
  no_bpjs  : '',
  keluhan  : '',
})

// ── Computed ──────────────────────────────────────────────────────────────
const selectedPoli   = computed(() => poliList.value.find(p => p.id === form.value.poli_id))
const selectedDokter = computed(() => dokterList.value.find(d => d.id === form.value.dokter_id))
const selectedSesi   = computed(() => sesiList.value.find(s => s.id === form.value.sesi_id))

const poliColorList = [
  { background: '#eff6ff', color: '#2563eb' },
  { background: '#f0fdf4', color: '#16a34a' },
  { background: '#fdf2f8', color: '#db2777' },
  { background: '#fffbeb', color: '#d97706' },
  { background: '#f5f3ff', color: '#7c3aed' },
  { background: '#fff7ed', color: '#ea580c' },
]
const poliColor = (id: number) => {
  const idx = poliList.value.findIndex(p => p.id === id)
  return poliColorList[idx % poliColorList.length]
}
const poliInitial = (nama: string) => {
  const parts = nama.split(' ')
  return parts.length > 1 ? parts[parts.length - 1].charAt(0) : nama.charAt(0)
}

const formatTanggalSesi = (sesi: any) => {
  if (!sesi?.tanggal) return '-'
  return new Date(sesi.tanggal).toLocaleDateString('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
  })
}

// ── QR Code ───────────────────────────────────────────────────────────────
const generateQR = async () => {
  await nextTick()
  if (!qrContainer.value || !nomorAntrian.value) return
  const QRCode = await loadQRLib()
  if (!QRCode) return
  qrContainer.value.innerHTML = ''
  const qrData = [
    `Nomor   : ${nomorAntrian.value}`,
    `Poli    : ${selectedPoli.value?.nama}`,
    `Dokter  : ${selectedDokter.value?.nama}`,
    `Tanggal : ${selectedSesi.value?.tanggal ?? new Date().toISOString().slice(0, 10)}`,
    `Jam     : ${selectedSesi.value?.jam_buka} - ${selectedSesi.value?.jam_tutup}`,
    `Pasien  : ${authStore.user?.name}`,
    `Jaminan : ${form.value.jaminan}`,
  ].join('\n')
  new QRCode(qrContainer.value, {
    text        : qrData,
    width       : 160,
    height      : 160,
    colorDark   : '#1a1a2e',
    colorLight  : '#ffffff',
    correctLevel: QRCode.CorrectLevel.M,
  })
}

const loadQRLib = (): Promise<any> => {
  return new Promise((resolve) => {
    if ((window as any).QRCode) { resolve((window as any).QRCode); return }
    const script   = document.createElement('script')
    script.src     = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js'
    script.onload  = () => setTimeout(() => resolve((window as any).QRCode), 100)
    script.onerror = () => resolve(null)
    document.head.appendChild(script)
  })
}

watch(submitted, async (val) => {
  if (val) {
    await nextTick()
    setTimeout(async () => { await generateQR() }, 300)
  }
})

// ── Lifecycle ─────────────────────────────────────────────────────────────
onMounted(async () => {
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser()
  }

  loadingPoli.value = true
  try {
    // ✅ FIX: Pakai endpoint pasien, bukan endpoint publik
    const res      = await api.get('/pasien/pendaftaran/poli')
    poliList.value = res.data.data ?? res.data.poli ?? res.data
  } catch {
    errorMsg.value = 'Gagal memuat daftar poli. Coba refresh halaman.'
  } finally {
    loadingPoli.value = false
  }
})

// ── Methods ───────────────────────────────────────────────────────────────
const nextToStep2 = () => {
  if (!authStore.isLoggedIn) {
    sessionStorage.setItem('redirect_after_login', '/pasien/pendaftaran')
    router.push('/login')
    return
  }
  step.value = 2
}

const pilihPoli = async (poli: any) => {
  form.value.poli_id   = poli.id
  form.value.dokter_id = null
  form.value.sesi_id   = null
  dokterList.value     = []
  sesiList.value       = []
  errorMsg.value       = ''

  loadingDokter.value = true
  try {
    // ✅ FIX: Pakai endpoint pasien, bukan endpoint publik
    const res        = await api.get(`/pasien/pendaftaran/dokter/${poli.id}`)
    dokterList.value = res.data.data ?? res.data.dokter ?? res.data
  } catch {
    errorMsg.value = 'Gagal memuat daftar dokter.'
  } finally {
    loadingDokter.value = false
  }
}

const pilihDokter = async (dokter: any) => {
  form.value.dokter_id = dokter.id
  form.value.sesi_id   = null
  sesiList.value       = []
  errorMsg.value       = ''

  loadingSesi.value = true
  try {
    const res      = await api.get('/pasien/pendaftaran/sesi', {
      params: { dokter_id: dokter.id, poli_id: form.value.poli_id }
    })
    sesiList.value = res.data.data ?? res.data.sesi ?? res.data
  } catch {
    errorMsg.value = 'Gagal memuat sesi antrian.'
  } finally {
    loadingSesi.value = false
  }
}

const submit = async () => {
  if (!authStore.isLoggedIn) {
    sessionStorage.setItem('redirect_after_login', '/pasien/pendaftaran')
    router.push('/login')
    return
  }

  errorMsg.value = ''
  loading.value  = true
  try {
    const payload: any = {
      poli_id  : form.value.poli_id,
      dokter_id: form.value.dokter_id,
      sesi_id  : form.value.sesi_id,
      jaminan  : form.value.jaminan,
      keluhan  : form.value.keluhan,
    }
    if (form.value.jaminan === 'BPJS' && form.value.no_bpjs) {
      payload.no_bpjs = form.value.no_bpjs
    }

    const res     = await api.post('/pasien/pendaftaran/daftar', payload)
    const antrian = res.data.antrian

    nomorAntrian.value = antrian.nomor_display
      ?? (antrian.kode_antrian + '-' + String(antrian.nomor_antrian).padStart(3, '0'))
    submitted.value = true

  } catch (e: any) {
    errorMsg.value = e.response?.data?.message ?? 'Gagal mendaftar antrian. Coba lagi.'
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  step.value         = 1
  submitted.value    = false
  nomorAntrian.value = ''
  errorMsg.value     = ''
  dokterList.value   = []
  sesiList.value     = []
  form.value = {
    poli_id  : null,
    dokter_id: null,
    sesi_id  : null,
    jaminan  : '',
    no_bpjs  : '',
    keluhan  : '',
  }
}
</script>
