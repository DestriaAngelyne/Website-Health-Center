<template>
    <div class="d-flex flex-column gap-7">

        <div class="card card-flush">
            <div class="card-body py-5">
                <div class="row g-3 align-items-end">
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold fs-7">Cari</label>
                        <input v-model="filter.search" type="text" class="form-control form-control-sm"
                            placeholder="Cari poli, dokter..." @input="loadData" />
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold fs-7">Dari Tanggal</label>
                        <input v-model="filter.dari" type="date" class="form-control form-control-sm" @change="loadData" />
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold fs-7">Sampai Tanggal</label>
                        <input v-model="filter.sampai" type="date" class="form-control form-control-sm" @change="loadData" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-flush">
            <div class="card-header pt-7">
                <h3 class="card-title">
                    <span class="card-label fw-bold text-gray-900">Daftar Kunjungan ({{ total }})</span>
                </h3>
            </div>
            <div class="card-body py-4">
                <div v-if="isLoading" class="text-center py-15">
                    <span class="spinner-border text-primary"></span>
                </div>
                <div v-else-if="riwayat.length === 0" class="text-center py-15">
                    <i class="ki-outline ki-document fs-5x text-gray-300 mb-4 d-block"></i>
                    <p class="text-gray-500 fw-semibold">Belum ada riwayat kunjungan</p>
                </div>
                <table v-else class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Diagnosa</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        <tr v-for="(item, i) in riwayat" :key="item.id">
                            <td>{{ (page - 1) * perPage + i + 1 }}</td>
                            <td>{{ formatTanggal(item.tanggal) }}</td>
                            <td><span class="badge badge-light-primary">{{ item.poli }}</span></td>
                            <td>{{ item.dokter }}</td>
                            <td>{{ item.diagnosa ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge badge-light-success">Selesai</span>
                            </td>
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

    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const isLoading = ref(true)
const riwayat   = ref<any[]>([])
const total     = ref(0)
const page      = ref(1)
const perPage   = 10
const filter    = ref({ search: '', dari: '', sampai: '' })

const totalPage = computed(() => Math.ceil(total.value / perPage))

function formatTanggal(tgl: string) {
    return new Date(tgl).toLocaleDateString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric'
    })
}

async function loadData() {
    isLoading.value = true
    try {
        const params: any = { page: page.value, limit: perPage }
        if (filter.value.search)  params.search  = filter.value.search
        if (filter.value.dari)    params.dari     = filter.value.dari
        if (filter.value.sampai)  params.sampai   = filter.value.sampai
        const res     = await api.get('/pasien/riwayat', { params })
        riwayat.value = res.data?.data  ?? []
        total.value   = res.data?.total ?? 0
    } catch {
        riwayat.value = []
    } finally {
        isLoading.value = false
    }
}

function changePage(p: number) {
    page.value = p
    loadData()
}

onMounted(loadData)
</script>
