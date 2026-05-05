<template>
    <div class="d-flex flex-column gap-7">

        <!--begin::Toolbar-->
        <div class="d-flex align-items-center gap-4">
            <button class="btn btn-sm btn-icon btn-light btn-active-primary" @click="$router.back()">
                <i class="ki-outline ki-arrow-left fs-4"></i>
            </button>
            <div>
                <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Detail Kunjungan</h1>
                <span class="text-gray-500 fw-semibold fs-7">Informasi lengkap kunjungan Anda</span>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Loading-->
        <div v-if="isLoading" class="d-flex justify-content-center py-20">
            <span class="spinner-border text-primary" style="width:3rem;height:3rem;"></span>
        </div>
        <!--end::Loading-->

        <!--begin::Not Found-->
        <div v-else-if="!kunjungan" class="card card-flush">
            <div class="card-body d-flex flex-column align-items-center py-20">
                <i class="ki-outline ki-document fs-5x text-gray-300 mb-4"></i>
                <h3 class="text-gray-600 fw-bold">Data Tidak Ditemukan</h3>
                <button class="btn btn-sm btn-primary mt-4" @click="$router.push('/pasien/riwayat')">
                    Kembali ke Riwayat
                </button>
            </div>
        </div>
        <!--end::Not Found-->

        <template v-else>

            <!--begin::Header Card-->
            <div class="card card-flush"
                style="background: linear-gradient(135deg, #1C325E 0%, #0EA5E9 100%);">
                <div class="card-body py-8 px-10">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                        <div>
                            <p class="text-white opacity-75 fs-7 mb-1">Tanggal Kunjungan</p>
                            <h2 class="text-white fw-bold mb-1">{{ formatTanggal(kunjungan.tanggal) }}</h2>
                            <span class="badge badge-success px-4 py-2 fs-7">
                                <i class="ki-outline ki-shield-tick text-white me-1"></i>
                                Selesai
                            </span>
                        </div>
                        <div class="text-end">
                            <p class="text-white opacity-75 fs-7 mb-1">No. Antrian</p>
                            <div class="display-6 fw-bolder text-white">{{ kunjungan.nomor_antrian ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Header Card-->

            <!--begin::Content Row-->
            <div class="row g-5 g-xl-10">

                <!--begin::Info Kunjungan-->
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title">
                                <span class="card-label fw-bold text-gray-900">Informasi Kunjungan</span>
                            </h3>
                        </div>
                        <div class="card-body py-6">
                            <div class="d-flex flex-column gap-4">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-gray-500 fw-semibold fs-7">Poli</span>
                                    <span class="badge badge-light-primary fs-7">{{ kunjungan.poli }}</span>
                                </div>
                                <div class="separator separator-dashed"></div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-gray-500 fw-semibold fs-7">Dokter</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="symbol symbol-30px">
                                            <div class="symbol-label fs-8 fw-bold bg-light-primary text-primary">
                                                {{ kunjungan.dokter?.charAt(0) ?? 'D' }}
                                            </div>
                                        </div>
                                        <span class="text-gray-800 fw-bold fs-7">{{ kunjungan.dokter }}</span>
                                    </div>
                                </div>
                                <div class="separator separator-dashed"></div>

                                <div class="d-flex justify-content-between">
                                    <span class="text-gray-500 fw-semibold fs-7">Keluhan Utama</span>
                                    <span class="text-gray-800 fw-semibold fs-7 text-end" style="max-width:60%;">
                                        {{ kunjungan.keluhan ?? '-' }}
                                    </span>
                                </div>
                                <div class="separator separator-dashed"></div>

                                <div class="d-flex justify-content-between">
                                    <span class="text-gray-500 fw-semibold fs-7">Diagnosa</span>
                                    <span class="text-gray-800 fw-bold fs-7 text-end" style="max-width:60%;">
                                        {{ kunjungan.diagnosa ?? '-' }}
                                    </span>
                                </div>
                                <div class="separator separator-dashed"></div>

                                <div class="d-flex justify-content-between">
                                    <span class="text-gray-500 fw-semibold fs-7">Catatan Dokter</span>
                                    <span class="text-gray-800 fs-7 text-end" style="max-width:60%;">
                                        {{ kunjungan.catatan ?? '-' }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Info Kunjungan-->

                <!--begin::Tanda Vital-->
                <div class="col-xl-6">
                    <div class="card card-flush h-100">
                        <div class="card-header pt-7">
                            <h3 class="card-title">
                                <span class="card-label fw-bold text-gray-900">Tanda Vital</span>
                            </h3>
                        </div>
                        <div class="card-body py-6">
                            <div class="row g-4">
                                <div class="col-6">
                                    <div class="bg-light-primary rounded-3 p-4 text-center">
                                        <i class="ki-outline ki-heart fs-2x text-primary mb-2"></i>
                                        <div class="fs-3 fw-bold text-gray-900">{{ kunjungan.vital?.tekanan_darah ?? '-' }}</div>
                                        <div class="text-gray-500 fs-8 fw-semibold">Tekanan Darah</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light-danger rounded-3 p-4 text-center">
                                        <i class="ki-outline ki-graph-up fs-2x text-danger mb-2"></i>
                                        <div class="fs-3 fw-bold text-gray-900">{{ kunjungan.vital?.nadi ?? '-' }}</div>
                                        <div class="text-gray-500 fs-8 fw-semibold">Nadi (bpm)</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light-warning rounded-3 p-4 text-center">
                                        <i class="ki-outline ki-element-11 fs-2x text-warning mb-2"></i>
                                        <div class="fs-3 fw-bold text-gray-900">{{ kunjungan.vital?.suhu ?? '-' }}</div>
                                        <div class="text-gray-500 fs-8 fw-semibold">Suhu (°C)</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light-success rounded-3 p-4 text-center">
                                        <i class="ki-outline ki-loading fs-2x text-success mb-2"></i>
                                        <div class="fs-3 fw-bold text-gray-900">{{ kunjungan.vital?.berat_badan ?? '-' }}</div>
                                        <div class="text-gray-500 fs-8 fw-semibold">Berat Badan (kg)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Tanda Vital-->

            </div>
            <!--end::Content Row-->

            <!--begin::Resep Obat-->
            <div class="card card-flush" v-if="kunjungan.resep && kunjungan.resep.length > 0">
                <div class="card-header pt-7">
                    <h3 class="card-title">
                        <span class="card-label fw-bold text-gray-900">Resep Obat</span>
                    </h3>
                </div>
                <div class="card-body py-4">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th>No.</th>
                                <th>Nama Obat</th>
                                <th>Dosis</th>
                                <th>Jumlah</th>
                                <th>Aturan Pakai</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            <tr v-for="(obat, i) in kunjungan.resep" :key="i">
                                <td class="text-gray-500">{{ i + 1 }}</td>
                                <td><span class="text-gray-800 fw-bold">{{ obat.nama_obat }}</span></td>
                                <td>{{ obat.dosis ?? '-' }}</td>
                                <td><span class="badge badge-light">{{ obat.jumlah ?? '-' }}</span></td>
                                <td>{{ obat.aturan_pakai ?? '-' }}</td>
                                <td>{{ obat.keterangan ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Resep-->

            <!--begin::Rujukan-->
            <div class="card card-flush border border-warning" v-if="kunjungan.rujukan">
                <div class="card-header pt-7">
                    <h3 class="card-title">
                        <i class="ki-outline ki-information-5 text-warning me-2 fs-3"></i>
                        <span class="card-label fw-bold text-gray-900">Surat Rujukan</span>
                    </h3>
                </div>
                <div class="card-body py-6">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <label class="text-gray-500 fw-semibold fs-7">Dirujuk ke</label>
                            <p class="text-gray-800 fw-bold fs-6 mt-1">{{ kunjungan.rujukan.tujuan ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-gray-500 fw-semibold fs-7">Spesialisasi</label>
                            <p class="text-gray-800 fw-bold fs-6 mt-1">{{ kunjungan.rujukan.spesialisasi ?? '-' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-gray-500 fw-semibold fs-7">Alasan Rujukan</label>
                            <p class="text-gray-800 fs-6 mt-1">{{ kunjungan.rujukan.alasan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Rujukan-->

        </template>

    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route     = useRoute()
const isLoading = ref(true)
const kunjungan = ref<any>(null)

function formatTanggal(tgl: string) {
    return new Date(tgl).toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    })
}

async function loadData() {
    isLoading.value = true
    try {
        const res = await api.get(`/pasien/riwayat/${route.params.id}`)
        kunjungan.value = res.data?.data ?? null
    } catch {
        kunjungan.value = null
    } finally {
        isLoading.value = false
    }
}

onMounted(loadData)
</script>
