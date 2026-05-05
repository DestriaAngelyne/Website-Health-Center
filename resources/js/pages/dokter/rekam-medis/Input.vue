<template>
  <div class="d-flex flex-column gap-6">

    <!-- Header -->
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <router-link to="/dokter/antrian" class="btn btn-icon btn-light btn-sm">
        <i class="ki-outline ki-arrow-left fs-4"></i>
      </router-link>
      <div class="flex-grow-1">
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Rekam Medis</h1>
        <span class="text-gray-500 fw-semibold fs-7" v-if="info">
          {{ info.pasien.nama }} — No. {{ info.antrian.nomor_display }}
        </span>
      </div>
      <div class="d-flex gap-2" v-if="info">
        <router-link :to="`/dokter/rekam-medis/${route.params.id}/resep`" class="btn btn-light-primary btn-sm">
          <i class="ki-outline ki-tablet me-1"></i>Resep
        </router-link>
        <router-link :to="`/dokter/rekam-medis/${route.params.id}/rujukan`" class="btn btn-light-warning btn-sm">
          <i class="ki-outline ki-send me-1"></i>Rujukan
        </router-link>
        <button class="btn btn-success btn-sm" @click="selesai" :disabled="loadingSelesai || info.antrian.status === 'selesai'">
          <span v-if="!loadingSelesai"><i class="ki-outline ki-check me-1"></i>Selesaikan</span>
          <span v-else><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
        </button>
      </div>
    </div>

    <!-- Alert -->
    <div v-if="alertMsg" class="alert py-3 d-flex align-items-center gap-3"
      :class="alertType === 'success' ? 'alert-success' : 'alert-danger'">
      <i class="ki-outline fs-2hx"
        :class="alertType === 'success' ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger'"></i>
      <span class="fw-semibold flex-grow-1">{{ alertMsg }}</span>
      <button class="btn-close" @click="alertMsg = ''"></button>
    </div>

    <div v-if="loading" class="d-flex justify-content-center py-12">
      <span class="spinner-border text-primary"></span>
    </div>

    <template v-else-if="info">
      <div class="row g-6">

        <!-- Kolom kiri: info pasien + riwayat -->
        <div class="col-lg-4">

          <!-- Info Pasien -->
          <div class="card card-flush mb-5">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Identitas Pasien</h3>
            </div>
            <div class="card-body pt-3">
              <div class="d-flex align-items-center gap-3 mb-4">
                <div class="symbol symbol-50px">
                  <div class="symbol-label fw-bold fs-4"
                    style="background:linear-gradient(135deg,#009ef7,#50cd89);color:white">
                    {{ info.pasien.nama?.charAt(0) }}
                  </div>
                </div>
                <div>
                  <div class="fw-bolder text-gray-900 fs-6">{{ info.pasien.nama }}</div>
                  <div class="text-gray-500 fs-8">No. RM: {{ info.pasien.no_rm }}</div>
                </div>
              </div>
              <div class="d-flex flex-column gap-2 fs-8">
                <div class="d-flex justify-content-between">
                  <span class="text-gray-400">Umur</span>
                  <span class="fw-semibold">{{ info.pasien.umur }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-gray-400">Jenis Kelamin</span>
                  <span class="fw-semibold">{{ info.pasien.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-gray-400">Jaminan</span>
                  <span class="badge fs-9" :class="info.antrian.jenis_pasien === 'bpjs' ? 'badge-light-success' : 'badge-light-info'">
                    {{ info.antrian.jenis_pasien?.toUpperCase() }}
                  </span>
                </div>
                <div v-if="info.pasien.alergi" class="d-flex justify-content-between">
                  <span class="text-gray-400">Alergi</span>
                  <span class="fw-semibold text-danger">{{ info.pasien.alergi }}</span>
                </div>
                <div v-if="info.pasien.golongan_darah" class="d-flex justify-content-between">
                  <span class="text-gray-400">Gol. Darah</span>
                  <span class="fw-semibold">{{ info.pasien.golongan_darah }}</span>
                </div>
                <div v-if="info.antrian.catatan" class="mt-2 p-2 bg-light rounded fs-8 text-gray-600">
                  <i class="ki-outline ki-notepad me-1"></i>{{ info.antrian.catatan }}
                </div>
              </div>
            </div>
          </div>

          <!-- Riwayat -->
          <div class="card card-flush">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">Riwayat Kunjungan</h3>
            </div>
            <div class="card-body pt-3">
              <div v-if="info.riwayat.length === 0" class="text-gray-400 fs-8 text-center py-4">
                Belum ada riwayat
              </div>
              <div v-else class="d-flex flex-column gap-3">
                <div v-for="r in info.riwayat" :key="r.id"
                  class="p-3 bg-light rounded-2 border border-gray-200">
                  <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold text-gray-800 fs-8">{{ r.tanggal }}</span>
                    <span class="text-gray-400 fs-9">{{ r.diagnosa_kode }}</span>
                  </div>
                  <div class="text-gray-600 fs-8">{{ r.diagnosa }}</div>
                  <div v-if="r.catatan_dokter" class="text-gray-400 fs-9 mt-1">{{ r.catatan_dokter }}</div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Kolom kanan: form rekam medis -->
        <div class="col-lg-8">

          <!-- Vital Sign -->
          <div class="card card-flush mb-5">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">
                <i class="ki-outline ki-heart-circle me-2 text-danger"></i>Vital Sign
              </h3>
            </div>
            <div class="card-body pt-3">
              <div class="row g-4">
                <div class="col-6 col-md-4">
                  <label class="form-label fw-semibold fs-8 mb-1">Berat Badan (kg)</label>
                  <input v-model="form.berat_badan" type="number" step="0.1"
                    class="form-control form-control-sm form-control-solid" placeholder="kg"
                    :disabled="sudahSelesai"/>
                </div>
                <div class="col-6 col-md-4">
                  <label class="form-label fw-semibold fs-8 mb-1">Tinggi Badan (cm)</label>
                  <input v-model="form.tinggi_badan" type="number" step="0.1"
                    class="form-control form-control-sm form-control-solid" placeholder="cm"
                    :disabled="sudahSelesai"/>
                </div>
                <div class="col-6 col-md-4">
                  <label class="form-label fw-semibold fs-8 mb-1">Tekanan Darah</label>
                  <input v-model="form.tekanan_darah" type="text"
                    class="form-control form-control-sm form-control-solid" placeholder="120/80"
                    :disabled="sudahSelesai"/>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label fw-semibold fs-8 mb-1">Nadi (bpm)</label>
                  <input v-model="form.nadi" type="number"
                    class="form-control form-control-sm form-control-solid" placeholder="bpm"
                    :disabled="sudahSelesai"/>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label fw-semibold fs-8 mb-1">Suhu (°C)</label>
                  <input v-model="form.suhu" type="number" step="0.1"
                    class="form-control form-control-sm form-control-solid" placeholder="°C"
                    :disabled="sudahSelesai"/>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label fw-semibold fs-8 mb-1">Respirasi (/mnt)</label>
                  <input v-model="form.respirasi" type="number"
                    class="form-control form-control-sm form-control-solid"
                    :disabled="sudahSelesai"/>
                </div>
                <div class="col-6 col-md-3">
                  <label class="form-label fw-semibold fs-8 mb-1">SpO2 (%)</label>
                  <input v-model="form.saturasi_oksigen" type="number"
                    class="form-control form-control-sm form-control-solid" placeholder="%"
                    :disabled="sudahSelesai"/>
                </div>
              </div>
            </div>
          </div>

          <!-- Anamnesis & Pemeriksaan -->
          <div class="card card-flush mb-5">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">
                <i class="ki-outline ki-notepad me-2 text-primary"></i>Anamnesis & Pemeriksaan
              </h3>
            </div>
            <div class="card-body pt-3 d-flex flex-column gap-4">
              <div>
                <label class="form-label fw-semibold required">Keluhan Utama</label>
                <textarea v-model="form.keluhan" class="form-control form-control-solid" rows="2"
                  :class="{ 'is-invalid': errors.keluhan }"
                  placeholder="Keluhan yang disampaikan pasien..."
                  :disabled="sudahSelesai"></textarea>
                <div class="invalid-feedback">{{ errors.keluhan }}</div>
              </div>
              <div>
                <label class="form-label fw-semibold">Anamnesis</label>
                <textarea v-model="form.anamnesis" class="form-control form-control-solid" rows="3"
                  placeholder="Riwayat penyakit, riwayat pengobatan..."
                  :disabled="sudahSelesai"></textarea>
              </div>
              <div>
                <label class="form-label fw-semibold">Pemeriksaan Fisik</label>
                <textarea v-model="form.pemeriksaan_fisik" class="form-control form-control-solid" rows="3"
                  placeholder="Hasil pemeriksaan fisik..."
                  :disabled="sudahSelesai"></textarea>
              </div>
            </div>
          </div>

          <!-- Diagnosa -->
          <div class="card card-flush mb-5">
            <div class="card-header pt-5 min-h-auto">
              <h3 class="card-title fw-bold fs-6">
                <i class="ki-outline ki-medal-star me-2 text-warning"></i>Diagnosa
              </h3>
            </div>
            <div class="card-body pt-3 d-flex flex-column gap-4">
              <div class="row g-4">
                <div class="col-md-3">
                  <label class="form-label fw-semibold fs-8 mb-1">Kode ICD-10</label>
                  <input v-model="form.diagnosa_kode" type="text"
                    class="form-control form-control-solid" placeholder="A00.0"
                    :disabled="sudahSelesai"/>
                </div>
                <div class="col-md-9">
                  <label class="form-label fw-semibold fs-8 mb-1 required">Diagnosa Utama</label>
                  <input v-model="form.diagnosa_nama" type="text"
                    class="form-control form-control-solid" :class="{ 'is-invalid': errors.diagnosa_nama }"
                    placeholder="Nama diagnosa utama..."
                    :disabled="sudahSelesai"/>
                  <div class="invalid-feedback">{{ errors.diagnosa_nama }}</div>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold fs-8 mb-1">Kode Sekunder</label>
                  <input v-model="form.diagnosa_sekunder_kode" type="text"
                    class="form-control form-control-solid" placeholder="B00.0"
                    :disabled="sudahSelesai"/>
                </div>
                <div class="col-md-9">
                  <label class="form-label fw-semibold fs-8 mb-1">Diagnosa Sekunder</label>
                  <input v-model="form.diagnosa_sekunder_nama" type="text"
                    class="form-control form-control-solid" placeholder="Diagnosa sekunder (jika ada)"
                    :disabled="sudahSelesai"/>
                </div>
              </div>
              <div>
                <label class="form-label fw-semibold fs-8 mb-1">Rencana Terapi</label>
                <textarea v-model="form.rencana_terapi" class="form-control form-control-solid" rows="2"
                  placeholder="Rencana tindakan dan terapi..."
                  :disabled="sudahSelesai"></textarea>
              </div>
              <div>
                <label class="form-label fw-semibold fs-8 mb-1">Catatan Dokter</label>
                <textarea v-model="form.catatan_dokter" class="form-control form-control-solid" rows="2"
                  placeholder="Catatan tambahan..."
                  :disabled="sudahSelesai"></textarea>
              </div>
            </div>
            <div class="card-footer d-flex justify-content-end py-4" v-if="!sudahSelesai">
              <button class="btn btn-primary" @click="simpan" :disabled="loadingSimpan">
                <span v-if="!loadingSimpan"><i class="ki-outline ki-check me-1"></i>Simpan Rekam Medis</span>
                <span v-else><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
              </button>
            </div>
          </div>

          <!-- Resep & Rujukan info -->
          <div class="row g-4" v-if="info.kunjungan">
            <div class="col-md-6">
              <div class="card card-flush h-100"
                :class="info.kunjungan.resep ? 'border border-success' : 'border border-dashed'">
                <div class="card-body py-5 d-flex align-items-center gap-3">
                  <i class="ki-outline ki-tablet fs-2x"
                    :class="info.kunjungan.resep ? 'text-success' : 'text-gray-300'"></i>
                  <div class="flex-grow-1">
                    <div class="fw-bold fs-7 text-gray-800">Resep Obat</div>
                    <div class="text-gray-400 fs-8" v-if="!info.kunjungan.resep">Belum ada resep</div>
                    <div class="text-success fs-8 fw-semibold" v-else>
                      {{ info.kunjungan.resep.detail?.length }} item · {{ info.kunjungan.resep.no_resep }}
                    </div>
                  </div>
                  <router-link :to="`/dokter/rekam-medis/${route.params.id}/resep`"
                    class="btn btn-sm" :class="info.kunjungan.resep ? 'btn-light-success' : 'btn-light-primary'">
                    {{ info.kunjungan.resep ? 'Edit' : 'Tulis' }}
                  </router-link>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card card-flush h-100"
                :class="info.kunjungan.rujukan ? 'border border-warning' : 'border border-dashed'">
                <div class="card-body py-5 d-flex align-items-center gap-3">
                  <i class="ki-outline ki-send fs-2x"
                    :class="info.kunjungan.rujukan ? 'text-warning' : 'text-gray-300'"></i>
                  <div class="flex-grow-1">
                    <div class="fw-bold fs-7 text-gray-800">Rujukan</div>
                    <div class="text-gray-400 fs-8" v-if="!info.kunjungan.rujukan">Belum ada rujukan</div>
                    <div class="text-warning fs-8 fw-semibold" v-else>
                      {{ info.kunjungan.rujukan.faskes_tujuan }}
                    </div>
                  </div>
                  <router-link :to="`/dokter/rekam-medis/${route.params.id}/rujukan`"
                    class="btn btn-sm" :class="info.kunjungan.rujukan ? 'btn-light-warning' : 'btn-light-warning'">
                    {{ info.kunjungan.rujukan ? 'Edit' : 'Buat' }}
                  </router-link>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </template>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route        = useRoute()
const router       = useRouter()
const loading      = ref(false)
const loadingSimpan = ref(false)
const loadingSelesai = ref(false)
const alertMsg     = ref('')
const alertType    = ref<'success'|'danger'>('success')
const errors       = ref<any>({})
const info         = ref<any>(null)

const sudahSelesai = computed(() => info.value?.antrian?.status === 'selesai')

const form = ref({
  keluhan: '', anamnesis: '', pemeriksaan_fisik: '',
  diagnosa_kode: '', diagnosa_nama: '',
  diagnosa_sekunder_kode: '', diagnosa_sekunder_nama: '',
  rencana_terapi: '', catatan_dokter: '',
  berat_badan: null, tinggi_badan: null, tekanan_darah: '',
  nadi: null, suhu: null, respirasi: null, saturasi_oksigen: null,
})

async function fetchInfo() {
  loading.value = true
  try {
    const res = await api.get(`/dokter/kunjungan/${route.params.id}`)
    info.value = res.data
    // Isi form dari data kunjungan yang ada
    if (res.data.kunjungan) {
      const k = res.data.kunjungan
      Object.keys(form.value).forEach(key => {
        if (k[key] !== undefined && k[key] !== null) (form.value as any)[key] = k[key]
      })
    }
  } finally {
    loading.value = false
  }
}

async function simpan() {
  errors.value   = {}
  alertMsg.value = ''
  loadingSimpan.value = true
  try {
    await api.post(`/dokter/kunjungan/${route.params.id}/simpan`, form.value)
    alertMsg.value  = 'Rekam medis berhasil disimpan.'
    alertType.value = 'success'
    fetchInfo()
  } catch (e: any) {
    if (e.response?.data?.errors) errors.value = e.response.data.errors
    else { alertMsg.value = e.response?.data?.message ?? 'Gagal menyimpan.'; alertType.value = 'danger' }
  } finally {
    loadingSimpan.value = false
  }
}

async function selesai() {
  if (!confirm('Selesaikan kunjungan ini? Pastikan rekam medis sudah lengkap.')) return
  loadingSelesai.value = true
  try {
    await api.post(`/dokter/kunjungan/${route.params.id}/selesai`)
    alertMsg.value  = 'Kunjungan selesai.'
    alertType.value = 'success'
    fetchInfo()
  } catch (e: any) {
    alertMsg.value  = e.response?.data?.message ?? 'Gagal menyelesaikan kunjungan.'
    alertType.value = 'danger'
  } finally {
    loadingSelesai.value = false
  }
}

onMounted(fetchInfo)
</script>
