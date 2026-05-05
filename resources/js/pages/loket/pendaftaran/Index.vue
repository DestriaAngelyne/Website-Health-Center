<template>
  <div class="d-flex flex-column gap-7">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Pendaftaran Manual</h1>
        <span class="text-gray-500 fw-semibold fs-7">Daftarkan pasien ke antrian poli</span>
      </div>
      <router-link to="/loket/dashboard" class="btn btn-light btn-sm">
        <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Kembali
      </router-link>
    </div>

    <!-- Alert -->
    <div v-if="alertMsg" class="alert py-3 d-flex align-items-center gap-3"
      :class="alertType === 'success' ? 'alert-success' : 'alert-danger'">
      <i class="ki-outline fs-2hx"
        :class="alertType === 'success' ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger'"></i>
      <span class="fw-semibold flex-grow-1">{{ alertMsg }}</span>
      <button class="btn-close" @click="alertMsg = ''"></button>
    </div>

    <!-- Tiket sukses -->
    <div v-if="tiketAntrian" class="card card-flush border border-success">
      <div class="card-body d-flex flex-column align-items-center py-10 text-center">
        <div class="symbol symbol-70px mb-5">
          <div class="symbol-label bg-light-success">
            <i class="ki-outline ki-check-circle fs-3x text-success"></i>
          </div>
        </div>
        <h3 class="fw-bold text-gray-900 mb-1">Pendaftaran Berhasil!</h3>
        <p class="text-gray-500 fs-7 mb-7">{{ tiketAntrian.pasien }} — {{ tiketAntrian.poli }}</p>
        <div class="border border-dashed border-primary rounded-2 p-7 mb-7" style="min-width:280px">
          <div class="text-gray-400 fs-8 mb-1">Nomor Antrian</div>
          <div class="fw-bolder text-primary mb-4" style="font-size:64px;line-height:1;font-family:serif">
            {{ tiketAntrian.nomor_display }}
          </div>
          <div class="separator separator-dashed mb-4"></div>
          <div class="d-flex justify-content-between fs-7 mb-2">
            <span class="text-gray-500">Dokter</span>
            <span class="fw-bold">{{ tiketAntrian.dokter }}</span>
          </div>
          <div class="d-flex justify-content-between fs-7">
            <span class="text-gray-500">Status</span>
            <span class="badge badge-light-warning">Menunggu</span>
          </div>
        </div>
        <button class="btn btn-primary" @click="resetForm">
          <i class="ki-outline ki-plus me-1"></i>Daftar Pasien Lagi
        </button>
      </div>
    </div>

    <!-- Form pendaftaran -->
    <template v-else>
      <div class="row g-6">

        <!-- Step 1: Cari Pasien -->
        <div class="col-md-5">
          <div class="card card-flush h-100">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">
                <span class="step-badge">1</span> Cari Pasien
              </h3>
            </div>
            <div class="card-body pt-3">
              <div class="position-relative mb-3">
                <i class="ki-outline ki-magnifier position-absolute fs-4"
                  style="left:12px;top:50%;transform:translateY(-50%);color:#99a1b7"></i>
                <input v-model="searchPasien" @input="cariPasien" type="text"
                  class="form-control form-control-solid ps-10"
                  placeholder="Cari nama, No. RM, NIK, No. HP..."/>
              </div>

              <div v-if="loadingCari" class="d-flex justify-content-center py-6">
                <span class="spinner-border spinner-border-sm text-primary"></span>
              </div>

              <div v-else-if="hasilCari.length > 0" class="d-flex flex-column gap-2">
                <div v-for="p in hasilCari" :key="p.id"
                  class="d-flex align-items-center gap-3 p-3 rounded-2 border cursor-pointer"
                  :class="pasienDipilih?.id === p.id
                    ? 'border-primary bg-light-primary'
                    : 'border-gray-200 bg-light'"
                  style="transition:all .2s"
                  @click="pilihPasien(p)">
                  <div class="symbol symbol-40px flex-shrink-0">
                    <div class="symbol-label fw-bold fs-6"
                      style="background:linear-gradient(135deg,#009ef7,#50cd89);color:white">
                      {{ p.nama.charAt(0) }}
                    </div>
                  </div>
                  <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold text-gray-900 fs-7">{{ p.nama }}</div>
                    <div class="text-gray-500 fs-8">RM: {{ p.no_rm }} · {{ p.nik }}</div>
                  </div>
                  <span class="badge fs-9" :class="p.jenis_pasien === 'bpjs' ? 'badge-light-success' : 'badge-light-info'">
                    {{ p.jenis_pasien }}
                  </span>
                </div>
              </div>

              <div v-else-if="searchPasien.length >= 2" class="text-center py-6 text-gray-400 fs-7">
                Pasien tidak ditemukan
              </div>

              <!-- Pasien terpilih -->
              <div v-if="pasienDipilih" class="mt-4 p-4 bg-light-primary rounded-2 border border-primary">
                <div class="d-flex align-items-center gap-3">
                  <i class="ki-outline ki-check-circle fs-2 text-primary"></i>
                  <div>
                    <div class="fw-bold text-gray-900 fs-7">{{ pasienDipilih.nama }}</div>
                    <div class="text-gray-500 fs-8">RM: {{ pasienDipilih.no_rm }}</div>
                  </div>
                  <button class="btn btn-icon btn-light btn-sm ms-auto" @click="pasienDipilih = null">
                    <i class="ki-outline ki-cross fs-5"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Pilih Sesi & Info -->
        <div class="col-md-7">
          <div class="card card-flush">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">
                <span class="step-badge">2</span> Pilih Sesi & Jenis Pasien
              </h3>
            </div>
            <div class="card-body pt-3">

              <!-- Pilih Sesi -->
              <label class="form-label fw-semibold fs-7 required mb-2">Sesi Antrian</label>
              <div v-if="loadingSesi" class="d-flex justify-content-center py-4">
                <span class="spinner-border spinner-border-sm text-primary"></span>
              </div>
              <div v-else-if="sesiAktif.length === 0" class="alert alert-warning py-3 fs-7">
                Tidak ada sesi aktif. Buka sesi terlebih dahulu di
                <router-link to="/loket/dashboard" class="fw-bold text-warning">Dashboard</router-link>.
              </div>
              <div v-else class="d-flex flex-column gap-2 mb-5">
                <div v-for="s in sesiAktif" :key="s.id"
                  class="d-flex align-items-center gap-3 p-4 rounded-2 border cursor-pointer"
                  :class="form.sesi_id === s.id
                    ? 'border-primary bg-light-primary'
                    : s.sisa === 0 ? 'border-gray-200 opacity-50' : 'border-gray-200 bg-light'"
                  :style="s.sisa === 0 ? 'cursor:not-allowed' : 'transition:all .2s'"
                  @click="s.sisa > 0 && pilihSesi(s)">
                  <div class="flex-grow-1">
                    <div class="fw-bold text-gray-900 fs-7">{{ s.poli }}</div>
                    <div class="text-gray-500 fs-8">{{ s.dokter }} · {{ s.jam_buka }}–{{ s.jam_tutup }}</div>
                  </div>
                  <div class="text-end">
                    <div class="fw-bold fs-7" :class="s.sisa === 0 ? 'text-danger' : 'text-success'">
                      {{ s.sisa === 0 ? 'Penuh' : `${s.sisa} sisa` }}
                    </div>
                    <div class="text-gray-400 fs-9">{{ s.terpakai }}/{{ s.kuota }} terisi</div>
                  </div>
                  <i v-if="form.sesi_id === s.id" class="ki-outline ki-check-circle fs-1 text-primary"></i>
                </div>
              </div>

              <!-- Jenis Pasien -->
              <label class="form-label fw-semibold fs-7 required mb-2">Jenis Pasien</label>
              <div class="d-flex gap-3 mb-5">
                <div v-for="j in ['umum', 'bpjs']" :key="j"
                  class="flex-grow-1 d-flex align-items-center gap-3 p-4 rounded-2 border cursor-pointer"
                  :class="form.jenis_pasien === j ? 'border-primary bg-light-primary' : 'border-gray-200 bg-light'"
                  style="transition:all .2s"
                  @click="form.jenis_pasien = j">
                  <i class="ki-outline fs-2"
                    :class="j === 'bpjs' ? 'ki-shield-tick text-success' : 'ki-credit-cart text-info'"></i>
                  <span class="fw-bold text-uppercase fs-7">{{ j }}</span>
                  <i v-if="form.jenis_pasien === j" class="ki-outline ki-check-circle ms-auto text-primary"></i>
                </div>
              </div>

              <!-- Catatan -->
              <label class="form-label fw-semibold fs-7 mb-2">
                Catatan <span class="text-gray-400 fw-normal">(opsional)</span>
              </label>
              <textarea v-model="form.catatan" class="form-control form-control-solid" rows="2"
                placeholder="Catatan tambahan..."></textarea>

            </div>
            <div class="card-footer d-flex justify-content-end py-4">
              <button class="btn btn-primary" @click="daftarkan"
                :disabled="!pasienDipilih || !form.sesi_id || !form.jenis_pasien || loading">
                <span v-if="!loading">
                  <i class="ki-outline ki-check me-1"></i>Daftarkan Pasien
                </span>
                <span v-else>
                  <span class="spinner-border spinner-border-sm me-2"></span>Mendaftarkan...
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const loading      = ref(false)
const loadingCari  = ref(false)
const loadingSesi  = ref(false)
const alertMsg     = ref('')
const alertType    = ref<'success'|'danger'>('success')

const searchPasien  = ref('')
const hasilCari     = ref<any[]>([])
const pasienDipilih = ref<any>(null)
const sesiList      = ref<any[]>([])
const tiketAntrian  = ref<any>(null)

const sesiAktif = computed(() => sesiList.value.filter(s => ['aktif','buka'].includes(s.status)))

const form = ref({
  sesi_id     : null as number | null,
  jenis_pasien: 'umum',
  catatan     : '',
})

let cariTimer: any = null
function cariPasien() {
  clearTimeout(cariTimer)
  if (searchPasien.value.length < 2) { hasilCari.value = []; return }
  cariTimer = setTimeout(async () => {
    loadingCari.value = true
    try {
      const res = await api.get('/loket/pasien/cari', { params: { q: searchPasien.value } })
      hasilCari.value = res.data.data
    } finally {
      loadingCari.value = false
    }
  }, 400)
}

function pilihPasien(p: any) {
  pasienDipilih.value = p
  hasilCari.value     = []
  searchPasien.value  = ''
  // Otomatis set jenis pasien sesuai data pasien
  if (p.jenis_pasien) form.value.jenis_pasien = p.jenis_pasien
}

function pilihSesi(s: any) {
  form.value.sesi_id = s.id
}

async function daftarkan() {
  if (!pasienDipilih.value || !form.value.sesi_id) return
  alertMsg.value = ''
  loading.value  = true

  const sesi = sesiAktif.value.find(s => s.id === form.value.sesi_id)

  try {
    const res = await api.post('/loket/antrian/daftar', {
      pasien_id   : pasienDipilih.value.id,
      sesi_id     : form.value.sesi_id,
      poli_id     : sesi?.poli_id,
      dokter_id   : sesi?.dokter_id,
      jenis_pasien: form.value.jenis_pasien,
      catatan     : form.value.catatan,
    })
    tiketAntrian.value = res.data.antrian
    fetchSesi()
  } catch (e: any) {
    alertMsg.value  = e.response?.data?.message ?? 'Gagal mendaftarkan pasien.'
    alertType.value = 'danger'
  } finally {
    loading.value = false
  }
}

async function fetchSesi() {
  loadingSesi.value = true
  try {
    const res = await api.get('/loket/sesi')
    sesiList.value = res.data.data
  } finally {
    loadingSesi.value = false
  }
}

function resetForm() {
  tiketAntrian.value  = null
  pasienDipilih.value = null
  searchPasien.value  = ''
  form.value = { sesi_id: null, jenis_pasien: 'umum', catatan: '' }
  alertMsg.value = ''
}

onMounted(fetchSesi)
</script>

<style scoped>
.step-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  background: #009ef7;
  color: white;
  border-radius: 50%;
  font-size: 12px;
  font-weight: 700;
  margin-right: 8px;
  flex-shrink: 0;
}
</style>
