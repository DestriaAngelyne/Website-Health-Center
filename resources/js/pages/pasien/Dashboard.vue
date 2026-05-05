<template>
    <div class="d-flex flex-column gap-7 gap-lg-10">

        <!--begin::Welcome Banner-->
        <div class="card bgi-no-repeat bgi-size-cover bgi-position-x-center"
            style="background-color: #1C325E; background-image: url('/metronic8/demo1/media/patterns/vector-1.png')">
            <div class="card-body d-flex align-items-center py-8 px-10">
                <div class="symbol symbol-60px me-5" style="flex-shrink:0;">
                    <template v-if="authStore.fotoUrl">
                        <img
                            :src="authStore.fotoUrl"
                            style="width:60px;height:60px;object-fit:cover;border-radius:50%;display:block;box-shadow:0 2px 8px rgba(0,0,0,.2);"
                            alt="Foto Profil"
                        />
                    </template>
                    <div v-else class="symbol-label fs-2 fw-bold text-white bg-white bg-opacity-25">
                        {{ userInitial }}
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h2 class="text-white fw-bold mb-1">Selamat Datang, {{ userName }}!</h2>
                    <span class="text-white opacity-75 fs-6">
                        <i class="ki-outline ki-calendar text-white opacity-75 me-1"></i>
                        {{ todayDate }}
                    </span>
                </div>
                <div class="d-none d-md-flex gap-3">
                    <router-link to="/pasien/antrian/status" class="btn btn-sm btn-light">
                        <i class="ki-outline ki-abstract-26 me-1"></i>
                        Status Antrian
                    </router-link>
                    <router-link to="/pasien/riwayat" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-document me-1"></i>
                        Riwayat
                    </router-link>
                </div>
            </div>
        </div>
        <!--end::Welcome Banner-->

        <!--begin::Stats-->
        <div class="row g-5 g-xl-10">
            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="badge badge-light-primary mb-2 align-self-start px-3 py-2">
                                <i class="ki-outline ki-abstract-26 text-primary fs-5 me-1"></i> Antrian
                            </span>
                            <span class="fs-2hx fw-bold text-gray-900 lh-1">
                                {{ antrianHariIni?.nomor_display ?? '—' }}
                            </span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Nomor Antrian Hari Ini</span>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-4">
                        <span class="badge" :class="statusBadge">{{ statusLabel }}</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="badge badge-light-info mb-2 align-self-start px-3 py-2">
                                <i class="ki-outline ki-people text-info fs-5 me-1"></i> Posisi
                            </span>
                            <span class="fs-2hx fw-bold text-gray-900 lh-1">
                                {{ antrianHariIni?.posisi ?? '—' }}
                            </span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Antrian di Depan Anda</span>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-4">
                        <span class="text-gray-500 fs-7">
                            <i class="ki-outline ki-time text-gray-400 me-1"></i>
                            Estimasi: <strong>{{ estimasiWaktu }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="badge badge-light-success mb-2 align-self-start px-3 py-2">
                                <i class="ki-outline ki-hospital text-success fs-5 me-1"></i> Poli
                            </span>
                            <span class="fs-3 fw-bold text-gray-900 lh-1 mt-1">
                                {{ antrianHariIni?.poli ?? '—' }}
                            </span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Tujuan Poli</span>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-4">
                        <span class="text-gray-500 fs-7">
                            <i class="ki-outline ki-profile-user text-gray-400 me-1"></i>
                            Dokter: <strong>{{ antrianHariIni?.dokter ?? '-' }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="badge badge-light-warning mb-2 align-self-start px-3 py-2">
                                <i class="ki-outline ki-document text-warning fs-5 me-1"></i> Riwayat
                            </span>
                            <span class="fs-2hx fw-bold text-gray-900 lh-1">{{ totalKunjungan }}</span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Kunjungan</span>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-4">
                        <router-link to="/pasien/riwayat" class="btn btn-sm btn-light btn-active-primary">
                            Lihat Semua <i class="ki-outline ki-arrow-right ms-1"></i>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Stats-->

        <!--begin::Main Content-->
        <div class="row g-5 g-xl-10">

            <!--begin::Status Antrian-->
            <div class="col-xl-8">
                <div class="card card-flush h-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Status Antrian Realtime</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Diperbarui otomatis setiap 30 detik</span>
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-success fs-7">
                                <span class="bullet bullet-dot bg-success me-1 h-7px w-7px animation-blink"></span>
                                Live
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="isLoading" class="d-flex justify-content-center py-15">
                            <span class="spinner-border text-primary"></span>
                        </div>
                        <div v-else-if="antrianHariIni">
                            <div class="mb-7">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-semibold fs-7 text-gray-600">Progress Antrian</span>
                                    <span class="fw-bold fs-7 text-primary">{{ progressPersen }}%</span>
                                </div>
                                <div class="h-10px rounded w-100 bg-light">
                                    <div class="bg-primary rounded h-10px"
                                        :style="{ width: progressPersen + '%', transition: 'width 0.5s ease' }">
                                    </div>
                                </div>
                            </div>
                            <div class="timeline timeline-border-dashed">
                                <div v-for="(step, i) in antrianSteps" :key="i" class="timeline-item">
                                    <div class="timeline-line w-40px"></div>
                                    <div class="timeline-icon symbol symbol-circle symbol-40px">
                                        <div class="symbol-label"
                                            :class="step.done ? 'bg-light-success' : step.active ? 'bg-light-primary' : 'bg-light'">
                                            <i class="ki-outline fs-2"
                                                :class="[step.icon, step.done ? 'text-success' : step.active ? 'text-primary' : 'text-gray-400']">
                                            </i>
                                        </div>
                                    </div>
                                    <div class="timeline-content mb-8 mt-n2 ms-5">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="fw-semibold fs-6"
                                                :class="step.active ? 'text-primary' : step.done ? 'text-success' : 'text-gray-500'">
                                                {{ step.label }}
                                            </span>
                                            <span class="badge"
                                                :class="step.done ? 'badge-light-success' : step.active ? 'badge-light-primary' : 'badge-light'">
                                                {{ step.done ? 'Selesai' : step.active ? 'Berlangsung' : 'Menunggu' }}
                                            </span>
                                        </div>
                                        <span class="text-gray-500 fs-7">{{ step.desc }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-5">
                                <router-link to="/pasien/antrian/status" class="btn btn-sm btn-light-primary">
                                    <i class="ki-outline ki-eye me-1"></i>
                                    Lihat Detail Status
                                </router-link>
                            </div>
                        </div>
                        <div v-else class="text-center py-15">
                            <i class="ki-outline ki-abstract-26 fs-5x text-gray-300 mb-5"></i>
                            <p class="text-gray-500 fs-5 fw-semibold">Belum Ada Antrian Hari Ini</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Status Antrian-->

            <!--begin::Info Pasien-->
            <div class="col-xl-4">
                <div class="card card-flush h-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title">
                            <span class="card-label fw-bold text-gray-900">Informasi Pasien</span>
                        </h3>

                    </div>
                    <div class="card-body py-6">
                        <div v-if="loadingProfil" class="d-flex justify-content-center py-6">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                        </div>
                        <div v-else class="d-flex flex-column gap-4">
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">Nama Lengkap</span>
                                <span class="text-gray-800 fw-bold fs-7 text-end">{{ profil.nama_lengkap || '-' }}</span>
                            </div>
                            <div class="separator separator-dashed"></div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">NIK</span>
                                <span class="text-gray-800 fw-bold fs-7">{{ profil.nik || '-' }}</span>
                            </div>
                            <div class="separator separator-dashed"></div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">No. Telepon</span>
                                <span class="text-gray-800 fw-bold fs-7">{{ profil.no_telepon || '-' }}</span>
                            </div>
                            <div class="separator separator-dashed"></div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">Email</span>
                                <span class="text-gray-800 fw-bold fs-7 text-end">{{ profil.email || '-' }}</span>
                            </div>
                            <div class="separator separator-dashed"></div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">Jenis Kelamin</span>
                                <span class="text-gray-800 fw-bold fs-7">{{ jenisKelamin }}</span>
                            </div>
                            <div class="separator separator-dashed"></div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">No. RM</span>
                                <span class="text-gray-800 fw-bold fs-7">{{ profil.no_rm || '-' }}</span>
                            </div>
                            <div class="separator separator-dashed"></div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">Jaminan</span>
                                <span class="badge badge-light-success">{{ profil.jenis_jaminan?.toUpperCase() || '-' }}</span>
                            </div>
                            <div class="separator separator-dashed"></div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">No. BPJS</span>
                                <span class="text-gray-800 fw-bold fs-7">{{ profil.no_bpjs || '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Info Pasien-->

        </div>
        <!--end::Main Content-->

        <!--begin::Riwayat Terakhir-->
        <div class="card card-flush">
            <div class="card-header pt-7">
                <h3 class="card-title">
                    <span class="card-label fw-bold text-gray-900">Riwayat Kunjungan Terakhir</span>
                </h3>
                <div class="card-toolbar">
                    <router-link to="/pasien/riwayat" class="btn btn-sm btn-light btn-active-primary">
                        Lihat Semua <i class="ki-outline ki-arrow-right ms-1"></i>
                    </router-link>
                </div>
            </div>
            <div class="card-body py-4">
                <div v-if="isLoading" class="text-center py-10">
                    <span class="spinner-border text-primary"></span>
                </div>
                <div v-else-if="riwayatTerakhir.length === 0" class="text-center py-10">
                    <i class="ki-outline ki-document fs-5x text-gray-300 mb-3 d-block"></i>
                    <span class="text-gray-500 fs-6">Belum ada riwayat kunjungan</span>
                </div>
                <table v-else class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Tanggal</th>
                            <th class="min-w-100px">Poli</th>
                            <th class="min-w-150px">Dokter</th>
                            <th class="min-w-150px">Diagnosa</th>
                            <th class="min-w-80px">Status</th>
                            <th class="text-end min-w-70px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        <tr v-for="item in riwayatTerakhir" :key="item.id">
                            <td>{{ formatTanggal(item.tanggal) }}</td>
                            <td>{{ item.poli }}</td>
                            <td>{{ item.dokter }}</td>
                            <td>{{ item.diagnosa ?? '-' }}</td>
                            <td><span class="badge badge-light-success">Selesai</span></td>
                            <td class="text-end">
                                <router-link :to="`/pasien/riwayat/${item.id}`"
                                    class="btn btn-sm btn-icon btn-light btn-active-primary">
                                    <i class="ki-outline ki-eye fs-4"></i>
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--end::Riwayat-->

    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'

const authStore     = useAuthStore()
const isLoading     = ref(true)
const loadingProfil = ref(true)

const antrianHariIni  = ref<any>(null)
const riwayatTerakhir = ref<any[]>([])
const totalKunjungan  = ref(0)

const profil = ref({
    nama_lengkap  : '',
    email         : '',
    nik           : '',
    tanggal_lahir : '',
    jenis_kelamin : '',
    no_telepon    : '',
    alamat        : '',
    golongan_darah: '',
    no_bpjs       : '',
    no_rm         : '',
    jenis_jaminan : '',
})

let refreshInterval: any = null

const userName    = computed(() => authStore.user?.name?.split(' ')[0] ?? 'Pasien')
const userInitial = computed(() => authStore.user?.name?.charAt(0).toUpperCase() ?? 'P')

const jenisKelamin = computed(() => {
    const jk = profil.value.jenis_kelamin
    return jk === 'L' ? 'Laki-laki' : jk === 'P' ? 'Perempuan' : '-'
})

const todayDate = computed(() =>
    new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
)

const statusLabel = computed(() => {
    const s = antrianHariIni.value?.status
    return s === 'menunggu' ? 'Menunggu' : s === 'dipanggil' ? 'Dipanggil' : s === 'selesai' ? 'Selesai' : '-'
})

const statusBadge = computed(() => {
    const s = antrianHariIni.value?.status
    return s === 'menunggu' ? 'badge-light-warning' : s === 'dipanggil' ? 'badge-light-primary' : s === 'selesai' ? 'badge-light-success' : 'badge-light'
})

const estimasiWaktu = computed(() => {
    const p = antrianHariIni.value?.posisi ?? 0
    return p === 0 ? 'Giliran Anda!' : `~${p * 10} menit`
})

const progressPersen = computed(() => {
    if (!antrianHariIni.value) return 0
    const posisi = antrianHariIni.value.posisi ?? 0
    const total  = antrianHariIni.value.total  ?? 1
    return posisi === 0 ? 100 : Math.max(10, Math.round(((total - posisi) / total) * 100))
})

const antrianSteps = computed(() => {
    const s = antrianHariIni.value?.status ?? ''
    return [
        { icon: 'ki-check-circle', label: 'Pendaftaran', desc: 'Nomor antrian telah didaftarkan',    done: true,           active: false },
        { icon: 'ki-time',         label: 'Menunggu',    desc: 'Menunggu giliran dipanggil',          done: s !== 'menunggu' && s !== '', active: s === 'menunggu' },
        { icon: 'ki-notification', label: 'Dipanggil',   desc: 'Silakan menuju ruang pemeriksaan',   done: s === 'selesai', active: s === 'dipanggil' },
        { icon: 'ki-heart',        label: 'Pemeriksaan', desc: 'Proses pemeriksaan oleh dokter',     done: s === 'selesai', active: false },
        { icon: 'ki-check',        label: 'Selesai',     desc: 'Kunjungan telah selesai',            done: s === 'selesai', active: false },
    ]
})

function formatTanggal(tgl: string) {
    return new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

async function loadProfil() {
    loadingProfil.value = true
    try {
        const res = await api.get('/pasien/profil')
        Object.assign(profil.value, res.data.data)
    } catch {}
    finally { loadingProfil.value = false }
}

async function loadData() {
    try {
        const [antrianRes, riwayatRes] = await Promise.all([
            api.get('/pasien/antrian/hari-ini').catch(() => null),
            api.get('/pasien/riwayat?limit=5').catch(() => null),
        ])
        antrianHariIni.value  = antrianRes?.data?.data ?? null
        riwayatTerakhir.value = riwayatRes?.data?.data ?? []
        totalKunjungan.value  = riwayatRes?.data?.total ?? 0
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    loadProfil()
    loadData()
    refreshInterval = setInterval(loadData, 30000)
})

onUnmounted(() => clearInterval(refreshInterval))
</script>
