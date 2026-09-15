<template>
    <div class="antrian-display d-flex flex-column min-vh-100"
        style="background: #0f172a; font-family: 'Inter', sans-serif;">

        <!--begin::Header-->
        <div class="d-flex align-items-center justify-content-between px-10 py-5"
            style="background: #1e293b; border-bottom: 1px solid #334155;">
            <div class="d-flex align-items-center gap-4">
                <div class="d-flex align-items-center justify-content-center rounded-2"
                    style="width:48px;height:48px;background:#0ea5e9;">
                    <svg viewBox="0 0 48 48" fill="none" width="32" height="32">
                        <path d="M24 8v32M8 24h32" stroke="white" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <h1 class="mb-0 fw-bold text-white fs-2">Puskesmas</h1>
                    <span class="text-slate-400 fs-7">Sistem Informasi Antrian Digital</span>
                </div>
            </div>
            <div class="text-end">
                <div class="fs-1 fw-bolder text-white" style="letter-spacing: 2px;">{{ jamSekarang }}</div>
                <div class="text-slate-400 fs-7">{{ tanggalSekarang }}</div>
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Main Content-->
        <div class="flex-grow-1 d-flex gap-0">

            <!--begin::Left - Nomor Dipanggil-->
            <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1 px-10 py-8"
                style="border-right: 1px solid #334155;">
                <p class="text-slate-400 fw-semibold fs-5 mb-4 text-uppercase letter-spacing-2">
                    Sedang Dilayani
                </p>
                <div class="text-white fw-bolder mb-4 text-center"
                    :class="animate ? 'animate-pulse' : ''"
                    style="font-size: clamp(100px, 20vw, 200px); line-height: 1; color: #0ea5e9 !important;">
                    {{ nomorDipanggil }}
                </div>
                <div class="badge px-6 py-3 fs-5 fw-bold mb-4"
                    style="background: #0ea5e9; color: white; border-radius: 50px;">
                    {{ poliAktif }}
                </div>
                <p class="text-slate-400 fs-6">Dokter: <strong class="text-white">{{ dokterAktif }}</strong></p>
            </div>
            <!--end::Left-->

            <!--begin::Right - Antrian Berikutnya-->
            <div class="d-flex flex-column" style="width: 420px; min-width: 380px;">

                <!--begin::Antrian Berikutnya-->
                <div class="flex-grow-1 px-8 py-8">
                    <p class="text-slate-400 fw-semibold fs-6 mb-5 text-uppercase">Antrian Berikutnya</p>
                    <div class="d-flex flex-column gap-3">
                        <div v-for="(item, i) in antrianBerikutnya" :key="i"
                            class="d-flex align-items-center justify-content-between rounded-3 px-5 py-4"
                            :style="{ background: i === 0 ? '#1e3a5f' : '#1e293b', border: i === 0 ? '1px solid #0ea5e9' : '1px solid #334155' }">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                    style="width:36px;height:36px;background:#334155;color:#94a3b8;font-size:12px;">
                                    {{ i + 2 }}
                                </div>
                                <div>
                                    <div class="text-white fw-bold fs-5">{{ item.nomor }}</div>
                                    <div class="text-slate-400 fs-7">{{ item.poli }}</div>
                                </div>
                            </div>
                            <span v-if="i === 0" class="badge" style="background:#0ea5e9;color:white;font-size:11px;">
                                Berikutnya
                            </span>
                        </div>

                        <div v-if="antrianBerikutnya.length === 0"
                            class="text-center py-10 text-slate-500">
                            Tidak ada antrian berikutnya
                        </div>
                    </div>
                </div>
                <!--end::Antrian Berikutnya-->

                <!--begin::Stats Footer-->
                <div class="px-8 py-5" style="background:#1e293b; border-top: 1px solid #334155;">
                    <div class="row g-4 text-center">
                        <div class="col-4">
                            <div class="fs-2 fw-bolder text-white">{{ stats.total }}</div>
                            <div class="text-slate-400 fs-8">Total</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-2 fw-bolder" style="color:#0ea5e9;">{{ stats.selesai }}</div>
                            <div class="text-slate-400 fs-8">Selesai</div>
                        </div>
                        <div class="col-4">
                            <div class="fs-2 fw-bolder text-warning">{{ stats.menunggu }}</div>
                            <div class="text-slate-400 fs-8">Menunggu</div>
                        </div>
                    </div>
                </div>
                <!--end::Stats-->

            </div>
            <!--end::Right-->

        </div>
        <!--end::Main Content-->

        <!--begin::Ticker-->
        <div class="px-8 py-3 d-flex align-items-center gap-4"
            style="background:#0ea5e9; overflow:hidden;">
            <span class="text-white fw-bold fs-7 text-nowrap">
                <i class="ki-outline ki-information me-1 text-white"></i> INFO
            </span>
            <div class="text-white fs-7 opacity-90 text-nowrap">
                Harap perhatikan nomor antrian Anda. Jika tidak hadir saat dipanggil 3x, nomor antrian akan hangus.
                &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                Jam Pelayanan: Senin - Jumat 08.00 - 14.00 WIB
                &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                Sabtu 08.00 - 11.00 WIB
            </div>
        </div>
        <!--end::Ticker-->

    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'

const nomorDipanggil    = ref('—')
const poliAktif         = ref('—')
const dokterAktif       = ref('—')
const antrianBerikutnya = ref<any[]>([])
const animate           = ref(false)
const stats = ref({ total: 0, selesai: 0, menunggu: 0 })

const jamSekarang    = ref('')
const tanggalSekarang = ref('')

let refreshInterval: any = null
let clockInterval: any   = null

function updateClock() {
    const now = new Date()
    jamSekarang.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    tanggalSekarang.value = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

async function loadData() {
    try {
        const res = await api.get('/display/antrian')
        const data = res.data?.data
        if (data) {
            const prevNomor = nomorDipanggil.value
            nomorDipanggil.value    = data.sedang_dilayani?.nomor_antrian ?? '—'
            poliAktif.value         = data.sedang_dilayani?.poli          ?? '—'
            dokterAktif.value       = data.sedang_dilayani?.dokter        ?? '—'
            antrianBerikutnya.value = data.berikutnya ?? []
            stats.value             = data.stats      ?? { total: 0, selesai: 0, menunggu: 0 }

            // Animasi saat nomor berubah
            if (prevNomor !== nomorDipanggil.value && prevNomor !== '—') {
                animate.value = true
                setTimeout(() => animate.value = false, 1000)
            }
        }
    } catch {
        // silently fail
    }
}

onMounted(() => {
    updateClock()
    loadData()
    clockInterval   = setInterval(updateClock, 1000)
    refreshInterval = setInterval(loadData, 10000)
})
onUnmounted(() => {
    clearInterval(clockInterval)
    clearInterval(refreshInterval)
})
</script>

<style scoped>
.text-slate-400 { color: #94a3b8; }
.text-slate-500 { color: #64748b; }
.letter-spacing-2 { letter-spacing: 2px; }
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.animate-pulse { animation: pulse 0.5s ease-in-out 3; }
</style>
