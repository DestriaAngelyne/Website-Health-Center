<template>
  <div class="d-flex flex-column gap-7">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Data Pasien</h1>
        <span class="text-gray-500 fw-semibold fs-7">Cari dan lihat data pasien terdaftar</span>
      </div>
    </div>

    <!-- Search -->
    <div class="card card-flush">
      <div class="card-body py-4">
        <div class="position-relative" style="max-width:420px">
          <i class="ki-outline ki-magnifier position-absolute fs-4"
            style="left:12px;top:50%;transform:translateY(-50%);color:#99a1b7"></i>
          <input v-model="search" @input="cariPasien" type="text"
            class="form-control form-control-solid ps-10"
            placeholder="Cari nama, No. RM, NIK, No. HP..."/>
        </div>
      </div>
    </div>

    <!-- Tabel -->
    <div class="card card-flush">
      <div class="card-body p-0">
        <div v-if="loading" class="d-flex justify-content-center py-12">
          <span class="spinner-border text-primary"></span>
        </div>
        <div v-else-if="pasienList.length === 0" class="text-center py-12 text-gray-400">
          <i class="ki-outline ki-profile-user fs-5x mb-3"></i>
          <p class="fw-semibold">{{ search.length >= 2 ? 'Pasien tidak ditemukan' : 'Ketik minimal 2 karakter untuk mencari' }}</p>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-row-dashed table-row-gray-200 align-middle gs-5 gy-4 mb-0">
            <thead>
              <tr class="fw-bold text-gray-500 fs-7 text-uppercase border-bottom">
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>NIK</th>
                <th>No. HP</th>
                <th class="text-center">Jaminan</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in pasienList" :key="p.id">
                <td><span class="badge badge-light-primary fw-bold">{{ p.no_rm }}</span></td>
                <td class="fw-bold text-gray-900">{{ p.nama }}</td>
                <td class="text-gray-500 fs-8">{{ p.nik }}</td>
                <td class="text-gray-500 fs-8">{{ p.no_hp }}</td>
                <td class="text-center">
                  <span class="badge fs-9"
                    :class="p.jenis_pasien === 'bpjs' ? 'badge-light-success' : 'badge-light-info'">
                    {{ p.jenis_pasien?.toUpperCase() }}
                  </span>
                </td>
                <td class="text-center">
                  <button class="btn btn-icon btn-light-primary btn-sm"
                    @click="daftarPasien(p)" title="Daftarkan ke antrian">
                    <i class="ki-outline ki-notepad-edit fs-5"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal daftarkan cepat -->
    <div v-if="modalPasien" class="modal-overlay" @click.self="modalPasien = null">
      <div class="modal-box">
        <div class="modal-header">
          <h4 class="fw-bold fs-5">Daftarkan ke Antrian</h4>
          <button class="btn btn-icon btn-light btn-sm" @click="modalPasien = null">
            <i class="ki-outline ki-cross fs-4"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="d-flex align-items-center gap-3 p-4 bg-light-primary rounded-2 mb-5">
            <div class="symbol symbol-40px">
              <div class="symbol-label fw-bold fs-5"
                style="background:linear-gradient(135deg,#009ef7,#50cd89);color:white">
                {{ modalPasien.nama.charAt(0) }}
              </div>
            </div>
            <div>
              <div class="fw-bold text-gray-900">{{ modalPasien.nama }}</div>
              <div class="text-gray-500 fs-8">RM: {{ modalPasien.no_rm }}</div>
            </div>
          </div>

          <div v-if="loadingSesi" class="d-flex justify-content-center py-4">
            <span class="spinner-border spinner-border-sm text-primary"></span>
          </div>
          <div v-else-if="sesiAktif.length === 0" class="alert alert-warning py-3 fs-7">
            Tidak ada sesi aktif hari ini.
          </div>
          <div v-else>
            <label class="form-label fw-semibold required mb-2">Pilih Sesi</label>
            <div class="d-flex flex-column gap-2 mb-4">
              <div v-for="s in sesiAktif" :key="s.id"
                class="d-flex align-items-center gap-3 p-3 rounded-2 border cursor-pointer"
                :class="modalForm.sesi_id === s.id ? 'border-primary bg-light-primary' : 'border-gray-200 bg-light'"
                @click="s.sisa > 0 && (modalForm.sesi_id = s.id)">
                <div class="flex-grow-1">
                  <div class="fw-bold text-gray-900 fs-7">{{ s.poli }}</div>
                  <div class="text-gray-500 fs-8">{{ s.dokter }} · {{ s.jam_buka }}–{{ s.jam_tutup }}</div>
                </div>
                <span class="fw-bold fs-7" :class="s.sisa===0 ? 'text-danger' : 'text-success'">
                  {{ s.sisa===0 ? 'Penuh' : `${s.sisa} sisa` }}
                </span>
              </div>
            </div>

            <label class="form-label fw-semibold required mb-2">Jaminan</label>
            <div class="d-flex gap-3">
              <div v-for="j in ['umum','bpjs']" :key="j"
                class="flex-grow-1 p-3 rounded-2 border cursor-pointer text-center"
                :class="modalForm.jenis_pasien === j ? 'border-primary bg-light-primary' : 'border-gray-200'"
                @click="modalForm.jenis_pasien = j">
                <span class="fw-bold text-uppercase fs-7">{{ j }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-light" @click="modalPasien = null">Batal</button>
          <button class="btn btn-primary" @click="submitModal"
            :disabled="!modalForm.sesi_id || loadingDaftar">
            <span v-if="!loadingDaftar"><i class="ki-outline ki-check me-1"></i>Daftarkan</span>
            <span v-else><span class="spinner-border spinner-border-sm me-1"></span>Mendaftarkan...</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { Loading } from 'element-plus/es/components/loading/src/service.mjs'

const router       = useRouter()
const loading      = ref(false)
const loadingSesi  = ref(false)
const loadingDaftar = ref(false)
const search       = ref('')
const pasienList   = ref<any[]>([])
const sesiList     = ref<any[]>([])
const modalPasien  = ref<any>(null)
const modalForm    = ref({ sesi_id: null as number|null, jenis_pasien: 'umum' })

const sesiAktif = computed(() => sesiList.value.filter(s => ['aktif','buka'].includes(s.status)))

let timer: any = null
function cariPasien() {
  clearTimeout(timer)
  if (search.value.length < 2) { pasienList.value = []; return }
  timer = setTimeout(async () => {
    loading.value = true
    try {
      const res = await api.get('/loket/pasien/cari', { params: { q: search.value } })
      pasienList.value = res.data.data
    } finally { loading.value = false }
  }, 400)
}

async function daftarPasien(p: any) {
  modalPasien.value = p
  modalForm.value   = { sesi_id: null, jenis_pasien: p.jenis_pasien ?? 'umum' }
  loadingSesi.value = true
  try {
    const res = await api.get('/loket/sesi')
    sesiList.value = res.data.data
  } finally { loadingSesi.value = false }
}

async function submitModal() {
  if (!modalPasien.value || !modalForm.value.sesi_id) return
  loadingDaftar.value = true
  const sesi = sesiAktif.value.find(s => s.id === modalForm.value.sesi_id)
  try {
    const res = await api.post('/loket/antrian/daftar', {
      pasien_id   : modalPasien.value.id,
      sesi_id     : modalForm.value.sesi_id,
      poli_id     : sesi?.poli_id,
      dokter_id   : sesi?.dokter_id,
      jenis_pasien: modalForm.value.jenis_pasien,
    })
    modalPasien.value = null
    router.push(`/loket/antrian/tiket?id=${res.data.antrian.id}`)
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Gagal mendaftarkan.')
  } finally { loadingDaftar.value = false }
}

onMounted(async () => {
    loading.value = true
    try{
        const res = await api.get('/loket/pasien/cari', { params: { q: '' } })
        pasienList.value = res.data.data
    } catch (e) {
        console.error("Gagal memuat data awal pasien:", e)
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.4);
  z-index: 9999; display: flex; align-items: center; justify-content: center;
}
.modal-box {
  background: white; border-radius: 16px; width: 100%; max-width: 480px;
  max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px 16px; border-bottom: 1px solid #f1f5f9;
}
.modal-body   { padding: 20px 24px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 16px 24px; border-top: 1px solid #f1f5f9; }
</style>
