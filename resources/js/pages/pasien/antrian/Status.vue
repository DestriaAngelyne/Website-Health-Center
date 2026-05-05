<template>
    <div class="d-flex flex-column gap-7">

        <!--begin::Toolbar-->
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Status Antrian</h1>
                <span class="text-gray-500 fw-semibold fs-7">Pantau posisi antrian Anda secara realtime</span>
            </div>
            <button class="btn btn-sm btn-light btn-active-primary" @click="loadData" :disabled="isLoading">
                <i class="ki-outline ki-arrows-circle me-1" :class="{ 'animation-spin': isLoading }"></i>
                Refresh
            </button>
        </div>
        <!--end::Toolbar-->

        <!--begin::Loading-->
        <div v-if="isLoading" class="d-flex justify-content-center py-20">
            <span class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></span>
        </div>
        <!--end::Loading-->

        <template v-else-if="antrian">

            <!--begin::Nomor Antrian Big Display-->
            <div class="card card-flush text-center py-10"
                style="background: linear-gradient(135deg, #1C325E 0%, #0EA5E9 100%);">
                <div class="card-body">
                    <p class="text-white opacity-75 fs-5 fw-semibold mb-3">Nomor Antrian Anda</p>
                    <div class="display-1 fw-bolder text-white mb-3" style="font-size: 120px; line-height: 1;">
                        {{ antrian.nomor_display }}
                    </div>
                    <span class="badge badge-lg px-5 py-3 fs-6" :class="statusBadge">
                        {{ statusLabel }}
                    </span>
                    <p class="text-white opacity-75 fs-7 mt-4 mb-0">
                        <i class="ki-outline ki-calendar text-white opacity-75 me-1"></i>
                        {{ todayDate }}
                    </p>
                </div>
            </div>
            <!--end::Nomor Antrian-->

            <!--begin::Info Row-->
            <div class="row g-5">

                <!--begin::Posisi-->
                <div class="col-sm-4">
                    <div class="card card-flush h-100 text-center py-5">
                        <div class="card-body">
                            <i class="ki-outline ki-people fs-2tx text-info mb-3"></i>
                            <div class="fs-2hx fw-bold text-gray-900">{{ antrian.posisi ?? 0 }}</div>
                            <span class="text-gray-500 fw-semibold fs-6">Antrian di Depan</span>
                            <div class="mt-3">
                                <span class="badge badge-light-info">
                                    Estimasi {{ estimasiWaktu }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Antrian Sekarang-->
                <div class="col-sm-4">
                    <div class="card card-flush h-100 text-center py-5">
                        <div class="card-body">
                            <i class="ki-outline ki-notification fs-2tx text-primary mb-3"></i>
                            <div class="fs-2hx fw-bold text-gray-900">{{ antrian.antrian_sekarang ?? '-' }}</div>
                            <span class="text-gray-500 fw-semibold fs-6">Sedang Dipanggil</span>
                            <div class="mt-3">
                                <span class="badge badge-light-primary">Antrian Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Poli-->
                <div class="col-sm-4">
                    <div class="card card-flush h-100 text-center py-5">
                        <div class="card-body">
                            <i class="ki-outline ki-hospital fs-2tx text-success mb-3"></i>
                            <div class="fs-3 fw-bold text-gray-900">{{ antrian.poli ?? '-' }}</div>
                            <span class="text-gray-500 fw-semibold fs-6">Tujuan Poli</span>
                            <div class="mt-3">
                                <span class="badge badge-light-success">{{ antrian.dokter ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Info Row-->

            <!--begin::Progress & Timeline-->
            <div class="row g-5">

                <!--begin::Progress Card-->
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title">
                                <span class="card-label fw-bold text-gray-900">Progress Antrian</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-semibold text-gray-500 fs-7">Progress</span>
                                <span class="fw-bold text-primary fs-7">{{ progressPersen }}%</span>
                            </div>
                            <div class="h-15px rounded bg-light mb-7">
                                <div class="h-15px rounded bg-primary"
                                    :style="{ width: progressPersen + '%', transition: 'width 0.6s ease' }"></div>
                            </div>

                            <div class="d-flex flex-column gap-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-gray-600 fw-semibold fs-7">Total Antrian Hari Ini</span>
                                    <span class="badge badge-light fs-7">{{ antrian.total ?? '-' }}</span>
                                </div>
                                <div class="separator separator-dashed"></div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-gray-600 fw-semibold fs-7">Posisi Anda</span>
                                    <span class="badge badge-light-primary fs-7">{{ antrian.posisi ?? '-' }}</span>
                                </div>
                                <div class="separator separator-dashed"></div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-gray-600 fw-semibold fs-7">Estimasi Waktu</span>
                                    <span class="badge badge-light-info fs-7">{{ estimasiWaktu }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Timeline-->
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title">
                                <span class="card-label fw-bold text-gray-900">Tahapan Antrian</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline timeline-border-dashed">
                                <div v-for="(step, i) in antrianSteps" :key="i" class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label"
                                            :class="step.done ? 'bg-light-success' : step.active ? 'bg-light-primary' : 'bg-light'">
                                            <i class="ki-outline fs-2"
                                                :class="[step.icon, step.done ? 'text-success' : step.active ? 'text-primary' : 'text-gray-400']"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-7 mt-n1 ms-5">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="fw-semibold fs-6"
                                                :class="step.active ? 'text-primary' : step.done ? 'text-success' : 'text-gray-500'">
                                                {{ step.label }}
                                            </span>
                                            <span class="badge fs-8"
                                                :class="step.done ? 'badge-light-success' : step.active ? 'badge-light-primary' : 'badge-light'">
                                                {{ step.done ? 'Selesai' : step.active ? 'Aktif' : 'Menunggu' }}
                                            </span>
                                        </div>
                                        <span class="text-gray-400 fs-7">{{ step.desc }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end::Progress & Timeline-->

            <!--begin::Dipanggil Alert-->
            <div v-if="antrian.status === 'dipanggil'" class="alert alert-success d-flex align-items-center">
                <i class="ki-outline ki-notification-on fs-2hx text-success me-4"></i>
                <div>
                    <h4 class="mb-1 text-success">Giliran Anda Tiba! 🎉</h4>
                    <span class="text-gray-700">Silakan menuju <strong>{{ antrian.poli }}</strong> untuk diperiksa oleh <strong>{{ antrian.dokter }}</strong></span>
                </div>
            </div>
            <!--end::Alert-->

        </template>

        <!--begin::Empty-->
        <div v-else-if="!isLoading" class="card card-flush">
            <div class="card-body d-flex flex-column align-items-center py-20">
                <i class="ki-outline ki-abstract-26 fs-5x text-gray-300 mb-5"></i>
                <h3 class="text-gray-600 fw-bold mb-2">Belum Ada Antrian Hari Ini</h3>
                <p class="text-gray-400 fs-6 text-center mb-0">
                    Silakan daftar antrian terlebih dahulu
                </p>
            </div>
        </div>
        <!--end::Empty-->

    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { PasienService } from '@/services/pasien.service'

const isLoading = ref(true)
const antrian   = ref<any>(null)
let refreshInterval: any = null

const todayDate = computed(() =>
    new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
)

const statusLabel = computed(() => {
    const s = antrian.value?.status
    return s === 'menunggu' ? 'Menunggu' : s === 'dipanggil' ? 'Dipanggil!' : s === 'selesai' ? 'Selesai' : '-'
})

const statusBadge = computed(() => {
    const s = antrian.value?.status
    return s === 'menunggu' ? 'badge-warning text-dark' : s === 'dipanggil' ? 'badge-primary' : s === 'selesai' ? 'badge-success' : 'badge-secondary'
})

const estimasiWaktu = computed(() => {
    const p = antrian.value?.posisi ?? 0
    return p === 0 ? 'Giliran Anda!' : `~${p * 10} menit`
})

const progressPersen = computed(() => {
    if (!antrian.value) return 0
    const posisi = antrian.value.posisi ?? 0
    const total  = antrian.value.total  ?? 1
    return posisi === 0 ? 100 : Math.max(5, Math.round(((total - posisi) / total) * 100))
})

const antrianSteps = computed(() => {
    const s = antrian.value?.status ?? ''
    return [
        { icon: 'ki-check-circle', label: 'Pendaftaran', desc: 'Nomor antrian telah didaftarkan',             done: true,           active: false           },
        { icon: 'ki-time',         label: 'Menunggu',    desc: 'Menunggu giliran dipanggil',                   done: s !== 'menunggu' && s !== '', active: s === 'menunggu'  },
        { icon: 'ki-notification', label: 'Dipanggil',   desc: 'Nama Anda dipanggil, menuju ruang periksa',   done: s === 'selesai', active: s === 'dipanggil' },
        { icon: 'ki-heart',        label: 'Pemeriksaan', desc: 'Diperiksa oleh dokter',                        done: s === 'selesai', active: false            },
        { icon: 'ki-shield-tick',  label: 'Selesai',     desc: 'Kunjungan selesai',                            done: s === 'selesai', active: false            },
    ]
})

async function loadData() {
    try {
        const res     = await PasienService.getAntrianHariIni()
        antrian.value = res.data?.data ?? null
    } catch {
        antrian.value = null
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    loadData()
    refreshInterval = setInterval(loadData, 30000) // auto refresh tiap 30 detik
})

onUnmounted(() => clearInterval(refreshInterval))
</script>
