<template>
    <div class="d-flex flex-column gap-7">

        <!--begin::Toolbar-->
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Daftar Antrian</h1>
                <span class="text-gray-500 fw-semibold fs-7">Daftarkan diri Anda untuk mendapatkan nomor antrian</span>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Antrian Aktif Warning-->
        <div v-if="antrianAktif" class="alert alert-warning d-flex align-items-center">
            <i class="ki-outline ki-information-5 fs-2hx text-warning me-4"></i>
            <div class="flex-grow-1">
                <h5 class="mb-1">Anda sudah memiliki antrian hari ini</h5>
                <span class="text-gray-700">Nomor antrian: <strong>{{ antrianAktif.kode_antrian }}-{{ String(antrianAktif.nomor_antrian).padStart(3,'0') }}</strong> — {{ antrianAktif.poli?.nama }}</span>
            </div>
            <div class="d-flex gap-2">
                <router-link to="/pasien/antrian/status" class="btn btn-sm btn-warning">
                    Lihat Status
                </router-link>
                <button class="btn btn-sm btn-light-danger" @click="batalkanAntrian" :disabled="loadingBatal">
                    <span v-if="loadingBatal" class="spinner-border spinner-border-sm me-1"></span>
                    Batalkan
                </button>
            </div>
        </div>
        <!--end::Antrian Aktif Warning-->

        <!--begin::Stepper-->
        <div class="card">
            <div class="card-body">

                <!--begin::Step Indicators-->
                <div class="stepper stepper-links d-flex flex-column gap-5">
                    <div class="stepper-nav d-flex justify-content-center gap-3 flex-wrap mb-5">
                        <div v-for="(step, i) in steps" :key="i"
                            class="stepper-item d-flex align-items-center gap-2"
                            :class="{ 'current': currentStep === i+1, 'completed': currentStep > i+1 }">
                            <div class="stepper-icon w-40px h-40px rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                :class="currentStep > i+1 ? 'bg-success text-white' : currentStep === i+1 ? 'bg-primary text-white' : 'bg-light text-gray-500'">
                                <i v-if="currentStep > i+1" class="ki-outline ki-check fs-4 text-white"></i>
                                <span v-else>{{ i+1 }}</span>
                            </div>
                            <span class="stepper-label fw-semibold fs-7"
                                :class="currentStep === i+1 ? 'text-primary' : currentStep > i+1 ? 'text-success' : 'text-gray-500'">
                                {{ step }}
                            </span>
                            <i v-if="i < steps.length-1" class="ki-outline ki-arrow-right text-gray-400 fs-5"></i>
                        </div>
                    </div>

                    <!--begin::Step 1 - Pilih Poli-->
                    <div v-if="currentStep === 1">
                        <h4 class="fw-bold text-gray-900 mb-5">Pilih Poli Tujuan</h4>
                        <div v-if="loadingPoli" class="d-flex justify-content-center py-10">
                            <span class="spinner-border text-primary"></span>
                        </div>
                        <div v-else-if="poliList.length === 0" class="text-center py-10">
                            <i class="ki-outline ki-hospital fs-5x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Tidak ada poli yang tersedia hari ini</p>
                        </div>
                        <div v-else class="row g-4">
                            <div v-for="poli in poliList" :key="poli.id" class="col-sm-6 col-xl-4">
                                <div class="card card-flush cursor-pointer border-2 h-100"
                                    :class="selectedPoli?.id === poli.id ? 'border-primary bg-light-primary' : 'border-transparent'"
                                    @click="selectedPoli = poli">
                                    <div class="card-body d-flex align-items-center gap-4 py-5">
                                        <div class="symbol symbol-50px">
                                            <div class="symbol-label bg-light-success">
                                                <i class="ki-outline ki-hospital fs-2 text-success"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-bold text-gray-900 fs-6 d-block">{{ poli.nama }}</span>
                                            <span class="text-gray-500 fs-7">{{ poli.sesi_count ?? 0 }} sesi tersedia</span>
                                        </div>
                                        <i v-if="selectedPoli?.id === poli.id" class="ki-outline ki-check-circle fs-2 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Step 1-->

                    <!--begin::Step 2 - Pilih Dokter-->
                    <div v-if="currentStep === 2">
                        <h4 class="fw-bold text-gray-900 mb-5">Pilih Dokter</h4>
                        <div v-if="loadingDokter" class="d-flex justify-content-center py-10">
                            <span class="spinner-border text-primary"></span>
                        </div>
                        <div v-else-if="dokterList.length === 0" class="text-center py-10">
                            <i class="ki-outline ki-profile-user fs-5x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Tidak ada dokter tersedia untuk poli ini</p>
                        </div>
                        <div v-else class="row g-4">
                            <div v-for="dokter in dokterList" :key="dokter.id" class="col-sm-6 col-xl-4">
                                <div class="card card-flush cursor-pointer border-2 h-100"
                                    :class="selectedDokter?.id === dokter.id ? 'border-primary bg-light-primary' : 'border-transparent'"
                                    @click="selectedDokter = dokter">
                                    <div class="card-body d-flex align-items-center gap-4 py-5">
                                        <div class="symbol symbol-50px">
                                            <div class="symbol-label bg-light-primary">
                                                <i class="ki-outline ki-profile-user fs-2 text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-bold text-gray-900 fs-6 d-block">{{ dokter.nama }}</span>
                                            <span class="text-gray-500 fs-7">{{ dokter.spesialis ?? 'Dokter Umum' }}</span>
                                        </div>
                                        <i v-if="selectedDokter?.id === dokter.id" class="ki-outline ki-check-circle fs-2 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Step 2-->

                    <!--begin::Step 3 - Pilih Sesi-->
                    <div v-if="currentStep === 3">
                        <h4 class="fw-bold text-gray-900 mb-5">Pilih Sesi Antrian</h4>
                        <div v-if="loadingSesi" class="d-flex justify-content-center py-10">
                            <span class="spinner-border text-primary"></span>
                        </div>
                        <div v-else-if="sesiList.length === 0" class="text-center py-10">
                            <i class="ki-outline ki-time fs-5x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Tidak ada sesi tersedia untuk dokter ini hari ini</p>
                        </div>
                        <div v-else class="d-flex flex-column gap-4">
                            <div v-for="sesi in sesiList" :key="sesi.id"
                                class="card card-flush cursor-pointer border-2"
                                :class="[
                                    sesi.is_full ? 'opacity-50' : '',
                                    selectedSesi?.id === sesi.id ? 'border-primary bg-light-primary' : 'border-transparent'
                                ]"
                                @click="!sesi.is_full && (selectedSesi = sesi)">
                                <div class="card-body d-flex align-items-center justify-content-between py-5">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="symbol symbol-50px">
                                            <div class="symbol-label bg-light-info">
                                                <i class="ki-outline ki-time fs-2 text-info"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-gray-900 fs-6 d-block">
                                                {{ sesi.jam_buka }} - {{ sesi.jam_tutup }}
                                            </span>
                                            <span class="text-gray-500 fs-7">
                                                Sisa kuota: {{ sesi.sisa }} dari {{ sesi.kuota }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex flex-column align-items-end">
                                            <div class="h-8px w-100px rounded bg-light mb-1">
                                                <div class="h-8px rounded"
                                                    :class="sesi.is_full ? 'bg-danger' : 'bg-success'"
                                                    :style="{ width: Math.round((sesi.terpakai / sesi.kuota) * 100) + '%' }">
                                                </div>
                                            </div>
                                            <span class="badge" :class="sesi.is_full ? 'badge-light-danger' : 'badge-light-success'">
                                                {{ sesi.is_full ? 'Penuh' : 'Tersedia' }}
                                            </span>
                                        </div>
                                        <i v-if="selectedSesi?.id === sesi.id" class="ki-outline ki-check-circle fs-2 text-primary"></i>
                                    </div>
                                </div>
                            </div>

                            <!--begin::Catatan-->
                            <div class="mt-3">
                                <label class="form-label fw-semibold text-gray-700">Catatan (opsional)</label>
                                <textarea v-model="catatan" class="form-control form-control-solid"
                                    rows="3" placeholder="Tuliskan keluhan atau catatan tambahan..."></textarea>
                            </div>
                            <!--end::Catatan-->
                        </div>
                    </div>
                    <!--end::Step 3-->

                    <!--begin::Step 4 - Konfirmasi-->
                    <div v-if="currentStep === 4">
                        <h4 class="fw-bold text-gray-900 mb-5">Konfirmasi Pendaftaran</h4>
                        <div class="card bg-light-primary border-0">
                            <div class="card-body py-6">
                                <div class="d-flex flex-column gap-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-gray-600 fw-semibold fs-6">Poli</span>
                                        <span class="fw-bold text-gray-900 fs-6">{{ selectedPoli?.nama }}</span>
                                    </div>
                                    <div class="separator separator-dashed"></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-gray-600 fw-semibold fs-6">Dokter</span>
                                        <span class="fw-bold text-gray-900 fs-6">{{ selectedDokter?.nama }}</span>
                                    </div>
                                    <div class="separator separator-dashed"></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-gray-600 fw-semibold fs-6">Sesi</span>
                                        <span class="fw-bold text-gray-900 fs-6">{{ selectedSesi?.jam_buka }} - {{ selectedSesi?.jam_tutup }}</span>
                                    </div>
                                    <div class="separator separator-dashed"></div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-gray-600 fw-semibold fs-6">Tanggal</span>
                                        <span class="fw-bold text-gray-900 fs-6">{{ todayDate }}</span>
                                    </div>
                                    <div v-if="catatan" class="separator separator-dashed"></div>
                                    <div v-if="catatan" class="d-flex justify-content-between align-items-start">
                                        <span class="text-gray-600 fw-semibold fs-6">Catatan</span>
                                        <span class="fw-bold text-gray-900 fs-6 text-end" style="max-width: 60%">{{ catatan }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-primary d-flex align-items-center mt-5">
                            <i class="ki-outline ki-information-5 fs-2 text-primary me-3"></i>
                            <span class="text-gray-700 fs-7">Pastikan data sudah benar sebelum mendaftar. Nomor antrian tidak dapat diubah setelah pendaftaran.</span>
                        </div>
                    </div>
                    <!--end::Step 4-->

                    <!--begin::Step 5 - Berhasil-->
                    <div v-if="currentStep === 5" class="text-center py-10">
                        <div class="mb-5">
                            <i class="ki-outline ki-check-circle fs-5x text-success"></i>
                        </div>
                        <h2 class="fw-bold text-gray-900 mb-3">Pendaftaran Berhasil!</h2>
                        <p class="text-gray-500 fs-6 mb-8">Nomor antrian Anda adalah</p>
                        <div class="d-inline-block px-10 py-5 rounded mb-8"
                            style="background: linear-gradient(135deg, #1C325E 0%, #0EA5E9 100%);">
                            <span class="display-4 fw-bolder text-white">{{ nomorAntrian }}</span>
                        </div>
                        <div class="d-flex justify-content-center gap-3 mt-5">
                            <router-link to="/pasien/antrian/status" class="btn btn-primary">
                                <i class="ki-outline ki-abstract-26 me-2"></i>
                                Lihat Status Antrian
                            </router-link>
                            <router-link to="/pasien/dashboard" class="btn btn-light">
                                Kembali ke Dashboard
                            </router-link>
                        </div>
                    </div>
                    <!--end::Step 5-->

                    <!--begin::Navigation-->
                    <div v-if="currentStep < 5" class="d-flex justify-content-between pt-5 border-top">
                        <button class="btn btn-light" @click="prevStep" :disabled="currentStep === 1">
                            <i class="ki-outline ki-arrow-left me-1"></i> Sebelumnya
                        </button>
                        <button v-if="currentStep < 4" class="btn btn-primary" @click="nextStep" :disabled="!canNext">
                            Selanjutnya <i class="ki-outline ki-arrow-right ms-1"></i>
                        </button>
                        <button v-if="currentStep === 4" class="btn btn-success" @click="daftar" :disabled="loadingDaftar">
                            <span v-if="loadingDaftar" class="spinner-border spinner-border-sm me-2"></span>
                            <i v-else class="ki-outline ki-check me-2"></i>
                            Daftar Sekarang
                        </button>
                    </div>
                    <!--end::Navigation-->

                </div>
            </div>
        </div>
        <!--end::Stepper-->

    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

const currentStep   = ref(1)
const steps         = ['Pilih Poli', 'Pilih Dokter', 'Pilih Sesi', 'Konfirmasi', 'Selesai']

const poliList      = ref<any[]>([])
const dokterList    = ref<any[]>([])
const sesiList      = ref<any[]>([])
const antrianAktif  = ref<any>(null)
const nomorAntrian  = ref('')
const catatan       = ref('')

const selectedPoli   = ref<any>(null)
const selectedDokter = ref<any>(null)
const selectedSesi   = ref<any>(null)

const loadingPoli   = ref(false)
const loadingDokter = ref(false)
const loadingSesi   = ref(false)
const loadingDaftar = ref(false)
const loadingBatal  = ref(false)

const todayDate = computed(() =>
    new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
)

const canNext = computed(() => {
    if (currentStep.value === 1) return !!selectedPoli.value
    if (currentStep.value === 2) return !!selectedDokter.value
    if (currentStep.value === 3) return !!selectedSesi.value
    return true
})

async function fetchPoli() {
    loadingPoli.value = true
    try {
        const res  = await api.get('/pasien/pendaftaran/poli')
        poliList.value = res.data?.data ?? []
    } catch { poliList.value = [] }
    finally { loadingPoli.value = false }
}

async function fetchDokter() {
    if (!selectedPoli.value) return
    loadingDokter.value = true
    dokterList.value    = []
    selectedDokter.value = null
    try {
        const res = await api.get(`/pasien/pendaftaran/dokter/${selectedPoli.value.id}`)
        dokterList.value = res.data?.data ?? []
    } catch { dokterList.value = [] }
    finally { loadingDokter.value = false }
}

async function fetchSesi() {
    if (!selectedPoli.value || !selectedDokter.value) return
    loadingSesi.value  = true
    sesiList.value     = []
    selectedSesi.value = null
    try {
        const res = await api.get('/pasien/pendaftaran/sesi', {
            params: { poli_id: selectedPoli.value.id, dokter_id: selectedDokter.value.id }
        })
        sesiList.value = res.data?.data ?? []
    } catch { sesiList.value = [] }
    finally { loadingSesi.value = false }
}

async function cekAntrianAktif() {
    try {
        const res = await api.get('/pasien/pendaftaran/cek-antrian-aktif')
        antrianAktif.value = res.data?.data ?? null
    } catch { antrianAktif.value = null }
}

async function batalkanAntrian() {
    if (!antrianAktif.value) return
    loadingBatal.value = true
    try {
        await api.patch(`/pasien/pendaftaran/${antrianAktif.value.id}/batalkan`)
        antrianAktif.value = null
    } catch {}
    finally { loadingBatal.value = false }
}

async function nextStep() {
    if (currentStep.value === 1) await fetchDokter()
    if (currentStep.value === 2) await fetchSesi()
    currentStep.value++
}

function prevStep() {
    currentStep.value--
}

async function daftar() {
    loadingDaftar.value = true
    try {
        const res = await api.post('/pasien/pendaftaran/daftar', {
            poli_id         : selectedPoli.value.id,
            dokter_id       : selectedDokter.value.id,
            sesi_antrian_id : selectedSesi.value.id,
            jenis_kunjungan : 'baru',
            catatan         : catatan.value,
        })
        const data     = res.data?.antrian
        nomorAntrian.value = data?.kode_antrian + '-' + String(data?.nomor_antrian).padStart(3, '0')
        currentStep.value  = 5
    } catch (err: any) {
        alert(err?.response?.data?.message ?? 'Pendaftaran gagal, silakan coba lagi.')
    } finally {
        loadingDaftar.value = false
    }
}

onMounted(() => {
    fetchPoli()
    cekAntrianAktif()
})
</script>
