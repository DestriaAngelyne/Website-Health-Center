<!-- ============================================================ -->
<!-- FILE 1: resources/js/pages/dokter/rekam-medis/Resep.vue    -->
<!-- ============================================================ -->
<template>
  <div class="d-flex flex-column gap-6" style="max-width:820px">

    <div class="d-flex align-items-center gap-3">
      <router-link :to="`/dokter/rekam-medis/${route.params.id}`" class="btn btn-icon btn-light btn-sm">
        <i class="ki-outline ki-arrow-left fs-4"></i>
      </router-link>
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Tulis Resep</h1>
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
        <h3 class="card-title fw-bold fs-6">Cari Obat</h3>
      </div>
      <div class="card-body pt-3">
        <div class="position-relative mb-4">
          <i class="ki-outline ki-magnifier position-absolute fs-4"
            style="left:12px;top:50%;transform:translateY(-50%);color:#99a1b7"></i>
          <input v-model="searchObat" @input="cariObat" type="text"
            class="form-control form-control-solid ps-10"
            placeholder="Cari nama obat..."/>
        </div>
        <div v-if="hasilObat.length > 0" class="d-flex flex-column gap-2">
          <div v-for="o in hasilObat" :key="o.id"
            class="d-flex align-items-center gap-3 p-3 rounded-2 border border-gray-200 bg-light cursor-pointer"
            @click="tambahObat(o)">
            <div class="flex-grow-1">
              <div class="fw-bold text-gray-900 fs-7">{{ o.nama }}</div>
              <div class="text-gray-500 fs-8">{{ o.generik }} · {{ o.satuan }} · Stok: {{ o.stok }}</div>
            </div>
            <button class="btn btn-icon btn-light-primary btn-sm">
              <i class="ki-outline ki-plus fs-4"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Daftar obat yang dipilih -->
    <div class="card card-flush">
      <div class="card-header pt-5 min-h-auto">
        <h3 class="card-title fw-bold fs-6">Daftar Obat ({{ detail.length }})</h3>
      </div>
      <div class="card-body pt-3">
        <div v-if="detail.length === 0" class="text-center py-8 text-gray-400">
          <i class="ki-outline ki-tablet fs-4x mb-3"></i>
          <p class="fw-semibold fs-7">Belum ada obat ditambahkan</p>
        </div>
        <div v-else class="d-flex flex-column gap-4">
          <div v-for="(d, i) in detail" :key="i"
            class="p-4 border border-gray-200 rounded-2 bg-light">
            <div class="d-flex align-items-start gap-3">
              <div class="flex-grow-1">
                <div class="fw-bold text-gray-900 mb-3">{{ d.nama }}</div>
                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label fw-semibold fs-8 required mb-1">Jumlah ({{ d.satuan }})</label>
                    <input v-model.number="d.jumlah" type="number" min="1"
                      class="form-control form-control-sm form-control-solid"/>
                  </div>
                  <div class="col-md-5">
                    <label class="form-label fw-semibold fs-8 required mb-1">Aturan Pakai</label>
                    <input v-model="d.aturan_pakai" type="text"
                      class="form-control form-control-sm form-control-solid"
                      placeholder="3x1 sesudah makan"/>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold fs-8 mb-1">Keterangan</label>
                    <input v-model="d.keterangan" type="text"
                      class="form-control form-control-sm form-control-solid"
                      placeholder="Opsional"/>
                  </div>
                </div>
              </div>
              <button class="btn btn-icon btn-light-danger btn-sm flex-shrink-0" @click="hapusObat(i)">
                <i class="ki-outline ki-trash fs-5"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="mt-5">
          <label class="form-label fw-semibold fs-8 mb-1">Catatan Resep</label>
          <textarea v-model="catatan" class="form-control form-control-solid" rows="2"
            placeholder="Catatan tambahan untuk apoteker..."></textarea>
        </div>
      </div>
      <div class="card-footer d-flex justify-content-end gap-3 py-4">
        <router-link :to="`/dokter/rekam-medis/${route.params.id}`" class="btn btn-light">Kembali</router-link>
        <button class="btn btn-primary" @click="simpan" :disabled="detail.length === 0 || loading">
          <span v-if="!loading"><i class="ki-outline ki-check me-1"></i>Simpan Resep</span>
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

const route      = useRoute()
const loading    = ref(false)
const alertMsg   = ref('')
const alertType  = ref<'success'|'danger'>('success')
const searchObat = ref('')
const hasilObat  = ref<any[]>([])
const detail     = ref<any[]>([])
const catatan    = ref('')
const pasienNama = ref('')
const kunjunganId = ref<number | null>(null)

let obatTimer: any = null
function cariObat() {
  clearTimeout(obatTimer)
  if (searchObat.value.length < 2) { hasilObat.value = []; return }
  obatTimer = setTimeout(async () => {
    const res = await api.get('/admin/obat', { params: { search: searchObat.value, per_page: 8 } })
    hasilObat.value = res.data.data?.data ?? res.data.data ?? []
  }, 400)
}

function tambahObat(o: any) {
  if (detail.value.find(d => d.obat_id === o.id)) return
  detail.value.push({ obat_id: o.id, nama: o.nama, satuan: o.satuan, jumlah: 1, aturan_pakai: '', keterangan: '' })
  searchObat.value = ''
  hasilObat.value  = []
}

function hapusObat(i: number) { detail.value.splice(i, 1) }

async function simpan() {
  if (!kunjunganId.value) return
  loading.value = true
  try {
    await api.post(`/dokter/kunjungan/${kunjunganId.value}/resep`, {
      catatan : catatan.value,
      detail  : detail.value.map(d => ({
        obat_id     : d.obat_id,
        jumlah      : d.jumlah,
        aturan_pakai: d.aturan_pakai,
        keterangan  : d.keterangan,
      })),
    })
    alertMsg.value  = 'Resep berhasil disimpan.'
    alertType.value = 'success'
  } catch (e: any) {
    alertMsg.value  = e.response?.data?.message ?? 'Gagal menyimpan resep.'
    alertType.value = 'danger'
  } finally {
    loading.value = false
  }
}

async function fetchInit() {
  const res = await api.get(`/dokter/kunjungan/${route.params.id}`)
  pasienNama.value  = res.data.pasien?.nama
  kunjunganId.value = res.data.kunjungan?.id
  if (res.data.kunjungan?.resep) {
    catatan.value = res.data.kunjungan.resep.catatan ?? ''
    detail.value  = res.data.kunjungan.resep.detail.map((d: any) => ({
      obat_id     : d.obat_id,
      nama        : d.obat,
      satuan      : d.satuan,
      jumlah      : d.jumlah,
      aturan_pakai: d.aturan,
      keterangan  : d.keterangan,
    }))
  }
}

onMounted(fetchInit)
</script>
