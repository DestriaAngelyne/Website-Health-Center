<template>
  <div class="d-flex flex-column gap-7" style="max-width: 720px;">

    <div class="d-flex align-items-center gap-3">
      <router-link to="/loket/pendaftaran" class="btn btn-icon btn-light btn-sm">
        <i class="ki-outline ki-arrow-left fs-4"></i>
      </router-link>
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Daftar Pasien Baru</h1>
        <span class="text-gray-500 fw-semibold fs-7">Input data pasien baru sekaligus daftarkan ke antrian</span>
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

    <!-- Tiket sukses -->
    <div v-if="tiket" class="card card-flush border border-success">
      <div class="card-body d-flex flex-column align-items-center py-10 text-center">
        <div class="symbol symbol-70px mb-5">
          <div class="symbol-label bg-light-success">
            <i class="ki-outline ki-check-circle fs-3x text-success"></i>
          </div>
        </div>
        <h3 class="fw-bold text-gray-900 mb-1">Pendaftaran Berhasil!</h3>
        <p class="text-gray-500 fs-7 mb-2">{{ tiket.pasien }} — {{ tiket.poli }}</p>
        <p class="text-gray-400 fs-8 mb-7">No. RM: <strong>{{ tiket.no_rm }}</strong></p>
        <div class="border border-dashed border-primary rounded-2 p-7 mb-7" style="min-width:280px">
          <div class="text-gray-400 fs-8 mb-1">Nomor Antrian</div>
          <div class="fw-bolder text-primary mb-4" style="font-size:64px;line-height:1;font-family:serif">
            {{ tiket.nomor_display }}
          </div>
          <div class="separator separator-dashed mb-4"></div>
          <div class="d-flex justify-content-between fs-7 mb-2">
            <span class="text-gray-500">Dokter</span>
            <span class="fw-bold">{{ tiket.dokter }}</span>
          </div>
          <div class="d-flex justify-content-between fs-7">
            <span class="text-gray-500">Status</span>
            <span class="badge badge-light-warning">Menunggu</span>
          </div>
        </div>
        <div class="d-flex gap-3">
          <router-link :to="`/loket/antrian/tiket?id=${tiket.id}`" class="btn btn-light-primary">
            <i class="ki-outline ki-printer me-1"></i>Cetak Tiket
          </router-link>
          <button class="btn btn-primary" @click="resetForm">
            <i class="ki-outline ki-plus me-1"></i>Daftar Pasien Lagi
          </button>
        </div>
      </div>
    </div>

    <template v-else>
      <!-- Data Pasien -->
      <div class="card card-flush">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">
            <span class="step-badge">1</span> Data Diri Pasien
          </h3>
        </div>
        <div class="card-body pt-3">
          <div class="row g-5">
            <div class="col-12">
              <label class="form-label fw-semibold required">Nama Lengkap</label>
              <input v-model="form.nama" type="text" class="form-control form-control-solid"
                :class="{ 'is-invalid': errors.nama }" placeholder="Nama sesuai KTP"/>
              <div class="invalid-feedback">{{ errors.nama }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold required">NIK</label>
              <input v-model="form.nik" type="text" class="form-control form-control-solid"
                :class="{ 'is-invalid': errors.nik }" placeholder="16 digit NIK"
                maxlength="16" @input="form.nik = form.nik.replace(/\D/g,'')"/>
              <div class="invalid-feedback">{{ errors.nik }}</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold required">Tanggal Lahir</label>
              <input v-model="form.tanggal_lahir" type="date" class="form-control form-control-solid"
                :class="{ 'is-invalid': errors.tanggal_lahir }"/>
              <div class="invalid-feedback">{{ errors.tanggal_lahir }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold required">Jenis Kelamin</label>
              <select v-model="form.jenis_kelamin" class="form-select form-select-solid"
                :class="{ 'is-invalid': errors.jenis_kelamin }">
                <option value="">Pilih</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
              <div class="invalid-feedback">{{ errors.jenis_kelamin }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold required">No. HP</label>
              <input v-model="form.no_hp" type="text" class="form-control form-control-solid"
                :class="{ 'is-invalid': errors.no_hp }" placeholder="08xx..."
                @input="form.no_hp = form.no_hp.replace(/\D/g,'')"/>
              <div class="invalid-feedback">{{ errors.no_hp }}</div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">No. BPJS</label>
              <input v-model="form.no_bpjs" type="text" class="form-control form-control-solid"
                placeholder="Jika ada" @input="form.no_bpjs = form.no_bpjs.replace(/\D/g,'')"/>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Alamat</label>
              <textarea v-model="form.alamat" class="form-control form-control-solid" rows="2"
                placeholder="Alamat lengkap"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Pilih Sesi -->
      <div class="card card-flush">
        <div class="card-header pt-5 min-h-auto">
          <h3 class="card-title fw-bold fs-6">
            <span class="step-badge">2</span> Pilih Sesi & Jaminan
          </h3>
        </div>
        <div class="card-body pt-3">
          <div v-if="loadingSesi" class="d-flex justify-content-center py-4">
            <span class="spinner-border spinner-border-sm text-primary"></span>
          </div>
          <div v-else-if="sesiAktif.length === 0" class="alert alert-warning py-3 fs-7">
            Tidak ada sesi aktif hari ini.
          </div>
          <div v-else class="d-flex flex-column gap-2 mb-5">
            <label class="form-label fw-semibold required mb-1">Sesi Antrian</label>
            <div v-for="s in sesiAktif" :key="s.id"
              class="d-flex align-items-center gap-3 p-4 rounded-2 border cursor-pointer"
              :class="form.sesi_id === s.id ? 'border-primary bg-light-primary' : s.sisa===0 ? 'border-gray-200 opacity-50' : 'border-gray-200 bg-light'"
              @click="s.sisa > 0 && (form.sesi_id = s.id)">
              <div class="flex-grow-1">
                <div class="fw-bold text-gray-900 fs-7">{{ s.poli }}</div>
                <div class="text-gray-500 fs-8">{{ s.dokter }} · {{ s.jam_buka }}–{{ s.jam_tutup }}</div>
              </div>
              <div class="text-end">
                <div class="fw-bold fs-7" :class="s.sisa === 0 ? 'text-danger' : 'text-success'">
                  {{ s.sisa === 0 ? 'Penuh' : `${s.sisa} sisa` }}
                </div>
              </div>
              <i v-if="form.sesi_id === s.id" class="ki-outline ki-check-circle fs-1 text-primary"></i>
            </div>
          </div>

          <label class="form-label fw-semibold required mb-2">Jenis Jaminan</label>
          <div class="d-flex gap-3">
            <div v-for="j in ['umum','bpjs']" :key="j"
              class="flex-grow-1 d-flex align-items-center gap-3 p-4 rounded-2 border cursor-pointer"
              :class="form.jenis_pasien === j ? 'border-primary bg-light-primary' : 'border-gray-200 bg-light'"
              @click="form.jenis_pasien = j">
              <i class="ki-outline fs-2" :class="j==='bpjs' ? 'ki-shield-tick text-success' : 'ki-credit-cart text-info'"></i>
              <span class="fw-bold text-uppercase fs-7">{{ j }}</span>
              <i v-if="form.jenis_pasien === j" class="ki-outline ki-check-circle ms-auto text-primary"></i>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-3 py-4">
          <router-link to="/loket/pendaftaran" class="btn btn-light">Batal</router-link>
          <button class="btn btn-primary" @click="submit"
            :disabled="loading || !form.sesi_id || !form.jenis_pasien">
            <span v-if="!loading"><i class="ki-outline ki-check me-1"></i>Daftar & Simpan</span>
            <span v-else><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
          </button>
        </div>
      </div>
    </template>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router      = useRouter()
const loading     = ref(false)
const loadingSesi = ref(false)
const alertMsg    = ref('')
const alertType   = ref<'success'|'danger'>('success')
const errors      = ref<any>({})
const sesiList    = ref<any[]>([])
const tiket       = ref<any>(null)

const sesiAktif = computed(() => sesiList.value.filter(s => ['aktif','buka'].includes(s.status)))

const form = ref({
  nama: '', nik: '', tanggal_lahir: '', jenis_kelamin: '',
  no_hp: '', no_bpjs: '', alamat: '',
  sesi_id: null as number | null, jenis_pasien: 'umum',
})

async function fetchSesi() {
  loadingSesi.value = true
  try {
    const res = await api.get('/loket/sesi')
    sesiList.value = res.data.data
  } finally {
    loadingSesi.value = false
  }
}

async function submit() {
  errors.value  = {}
  alertMsg.value = ''
  loading.value  = true
  try {
    const sesi = sesiAktif.value.find(s => s.id === form.value.sesi_id)
    const res  = await api.post('/loket/pendaftaran/pasien-baru', {
      ...form.value,
      poli_id  : sesi?.poli_id,
      dokter_id: sesi?.dokter_id,
    })
    tiket.value = res.data.antrian
  } catch (e: any) {
    if (e.response?.data?.errors) errors.value = e.response.data.errors
    else { alertMsg.value = e.response?.data?.message ?? 'Terjadi kesalahan.'; alertType.value = 'danger' }
  } finally {
    loading.value = false
  }
}

function resetForm() {
  tiket.value = null
  errors.value = {}
  alertMsg.value = ''
  form.value = { nama:'', nik:'', tanggal_lahir:'', jenis_kelamin:'', no_hp:'', no_bpjs:'', alamat:'', sesi_id: null, jenis_pasien: 'umum' }
}

onMounted(fetchSesi)
</script>

<style scoped>
.step-badge {
  display: inline-flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; background: #009ef7; color: white;
  border-radius: 50%; font-size: 12px; font-weight: 700; margin-right: 8px; flex-shrink: 0;
}
</style>
