<template>
  <div class="d-flex flex-column gap-6" style="max-width:720px">

    <div class="d-flex align-items-center gap-3">
      <router-link :to="`/dokter/rekam-medis/${route.params.id}`" class="btn btn-icon btn-light btn-sm">
        <i class="ki-outline ki-arrow-left fs-4"></i>
      </router-link>
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Buat Rujukan</h1>
        <span class="text-gray-500 fw-semibold fs-7" v-if="pasienNama">{{ pasienNama }}</span>
      </div>
    </div>

    <div v-if="alertMsg" class="alert py-3 d-flex align-items-center gap-3"
      :class="alertType === 'success' ? 'alert-success' : 'alert-danger'">
      <i class="ki-outline fs-2hx"
        :class="alertType === 'success' ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger'"></i>
      <span class="fw-semibold flex-grow-1">{{ alertMsg }}</span>
      <button class="btn-close" @click="alertMsg = ''"></button>
    </div>

    <div class="card card-flush">
      <div class="card-header pt-5 min-h-auto">
        <h3 class="card-title fw-bold fs-6">Form Rujukan</h3>
      </div>
      <div class="card-body pt-3">
        <div class="row g-5">

          <!-- Jenis -->
          <div class="col-12">
            <label class="form-label fw-semibold required mb-2">Jenis Rujukan</label>
            <div class="d-flex gap-3">
              <div v-for="j in ['internal','eksternal']" :key="j"
                class="flex-grow-1 p-4 rounded-2 border cursor-pointer d-flex align-items-center gap-3"
                :class="form.jenis === j ? 'border-primary bg-light-primary' : 'border-gray-200 bg-light'"
                @click="form.jenis = j">
                <i class="ki-outline fs-2"
                  :class="j === 'internal' ? 'ki-home text-primary' : 'ki-send text-warning'"></i>
                <span class="fw-bold text-capitalize fs-7">{{ j }}</span>
                <i v-if="form.jenis === j" class="ki-outline ki-check-circle ms-auto text-primary"></i>
              </div>
            </div>
          </div>

          <!-- Faskes & Poli Tujuan -->
          <div class="col-md-7">
            <label class="form-label fw-semibold required">Faskes Tujuan</label>
            <input v-model="form.faskes_tujuan" type="text" class="form-control form-control-solid"
              :class="{ 'is-invalid': errors.faskes_tujuan }"
              placeholder="Nama Rumah Sakit / Klinik tujuan"/>
            <div class="invalid-feedback">{{ errors.faskes_tujuan }}</div>
          </div>
          <div class="col-md-5">
            <label class="form-label fw-semibold required">Poli / Spesialis Tujuan</label>
            <input v-model="form.poli_tujuan" type="text" class="form-control form-control-solid"
              :class="{ 'is-invalid': errors.poli_tujuan }"
              placeholder="Poli Jantung, Spesialis Paru..."/>
            <div class="invalid-feedback">{{ errors.poli_tujuan }}</div>
          </div>

          <!-- Diagnosa -->
          <div class="col-md-3">
            <label class="form-label fw-semibold fs-8 mb-1">Kode ICD-10</label>
            <input v-model="form.diagnosa_kode" type="text" class="form-control form-control-solid"
              placeholder="A00.0"/>
          </div>
          <div class="col-md-9">
            <label class="form-label fw-semibold required">Diagnosa</label>
            <input v-model="form.diagnosa_nama" type="text" class="form-control form-control-solid"
              :class="{ 'is-invalid': errors.diagnosa_nama }"
              placeholder="Diagnosa yang dirujuk..."/>
            <div class="invalid-feedback">{{ errors.diagnosa_nama }}</div>
          </div>

          <!-- Alasan -->
          <div class="col-12">
            <label class="form-label fw-semibold required">Alasan Rujukan</label>
            <textarea v-model="form.alasan_rujukan" class="form-control form-control-solid" rows="3"
              :class="{ 'is-invalid': errors.alasan_rujukan }"
              placeholder="Alasan merujuk pasien ke faskes tujuan..."></textarea>
            <div class="invalid-feedback">{{ errors.alasan_rujukan }}</div>
          </div>

          <!-- Terapi yang telah diberikan -->
          <div class="col-12">
            <label class="form-label fw-semibold">Terapi yang Telah Diberikan</label>
            <textarea v-model="form.terapi_yang_telah_diberikan" class="form-control form-control-solid" rows="2"
              placeholder="Terapi/pengobatan yang sudah diberikan sebelum rujukan..."></textarea>
          </div>

          <!-- Tanggal berlaku -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Tanggal Berlaku</label>
            <input v-model="form.tanggal_berlaku" type="date" class="form-control form-control-solid"/>
          </div>

          <!-- Catatan -->
          <div class="col-md-8">
            <label class="form-label fw-semibold">Catatan Tambahan</label>
            <textarea v-model="form.catatan" class="form-control form-control-solid" rows="2"
              placeholder="Catatan lain untuk dokter tujuan..."></textarea>
          </div>

        </div>
      </div>
      <div class="card-footer d-flex justify-content-end gap-3 py-4">
        <router-link :to="`/dokter/rekam-medis/${route.params.id}`" class="btn btn-light">Batal</router-link>
        <button class="btn btn-warning" @click="simpan" :disabled="loading">
          <span v-if="!loading"><i class="ki-outline ki-send me-1"></i>Simpan Rujukan</span>
          <span v-else><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
        </button>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route       = useRoute()
const loading     = ref(false)
const alertMsg    = ref('')
const alertType   = ref<'success'|'danger'>('success')
const errors      = ref<any>({})
const pasienNama  = ref('')
const kunjunganId = ref<number | null>(null)

const form = ref({
  jenis                      : 'eksternal',
  faskes_tujuan              : '',
  poli_tujuan                : '',
  diagnosa_kode              : '',
  diagnosa_nama              : '',
  alasan_rujukan             : '',
  terapi_yang_telah_diberikan: '',
  tanggal_berlaku            : '',
  catatan                    : '',
})

async function simpan() {
  errors.value   = {}
  alertMsg.value = ''
  if (!kunjunganId.value) return
  loading.value = true
  try {
    await api.post(`/dokter/kunjungan/${kunjunganId.value}/rujukan`, form.value)
    alertMsg.value  = 'Rujukan berhasil disimpan.'
    alertType.value = 'success'
  } catch (e: any) {
    if (e.response?.data?.errors) errors.value = e.response.data.errors
    else { alertMsg.value = e.response?.data?.message ?? 'Gagal menyimpan rujukan.'; alertType.value = 'danger' }
  } finally {
    loading.value = false
  }
}

async function fetchInit() {
  const res = await api.get(`/dokter/kunjungan/${route.params.id}`)
  pasienNama.value  = res.data.pasien?.nama
  kunjunganId.value = res.data.kunjungan?.id
  if (res.data.kunjungan?.rujukan) {
    const r = res.data.kunjungan.rujukan
    form.value.faskes_tujuan               = r.faskes_tujuan ?? ''
    form.value.poli_tujuan                 = r.poli_tujuan ?? ''
    form.value.diagnosa_kode               = r.diagnosa_kode ?? ''
    form.value.diagnosa_nama               = r.diagnosa_nama ?? ''
    form.value.alasan_rujukan              = r.alasan ?? ''
    form.value.jenis                       = r.jenis ?? 'eksternal'
  }
  // Pre-fill diagnosa dari kunjungan
  if (res.data.kunjungan && !form.value.diagnosa_nama) {
    form.value.diagnosa_kode = res.data.kunjungan.diagnosa_kode ?? ''
    form.value.diagnosa_nama = res.data.kunjungan.diagnosa_nama ?? ''
  }
}

onMounted(fetchInit)
</script>
