<template>
  <div class="d-flex flex-column gap-7" style="max-width: 700px;">
    <div class="d-flex align-items-center gap-3">
      <router-link to="/admin/master/obat" class="btn btn-icon btn-light btn-sm">
        <i class="ki-outline ki-arrow-left fs-4"></i>
      </router-link>
      <div>
        <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">{{ isEdit ? 'Edit Obat' : 'Tambah Obat' }}</h1>
        <span class="text-gray-500 fw-semibold fs-7">{{ isEdit ? 'Perbarui data obat' : 'Tambah obat baru ke sistem' }}</span>
      </div>
    </div>

    <div v-if="errors.general" class="alert alert-danger py-3">{{ errors.general }}</div>

    <div class="card card-flush">
      <div class="card-body py-7">
        <div class="row g-5">

          <div class="col-md-4">
            <label class="form-label fw-semibold required">Kode Obat</label>
            <input v-model="form.kode" type="text" class="form-control form-control-solid" :class="{ 'is-invalid': errors.kode }" placeholder="Contoh: OBT001" style="text-transform:uppercase"/>
            <div class="invalid-feedback">{{ errors.kode }}</div>
          </div>

          <div class="col-md-8">
            <label class="form-label fw-semibold required">Nama Obat</label>
            <input v-model="form.nama" type="text" class="form-control form-control-solid" :class="{ 'is-invalid': errors.nama }" placeholder="Nama obat"/>
            <div class="invalid-feedback">{{ errors.nama }}</div>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Nama Generik</label>
            <input v-model="form.generik" type="text" class="form-control form-control-solid" placeholder="Nama generik (opsional)"/>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold required">Satuan</label>
            <select v-model="form.satuan" class="form-select form-select-solid" :class="{ 'is-invalid': errors.satuan }">
              <option value="">Pilih</option>
              <option v-for="s in satuanList" :key="s" :value="s">{{ s }}</option>
            </select>
            <div class="invalid-feedback">{{ errors.satuan }}</div>
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold required">Kategori</label>
            <select v-model="form.kategori" class="form-select form-select-solid" :class="{ 'is-invalid': errors.kategori }">
              <option value="">Pilih</option>
              <option v-for="k in kategoriList" :key="k" :value="k">{{ k }}</option>
            </select>
            <div class="invalid-feedback">{{ errors.kategori }}</div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold required">Stok Saat Ini</label>
            <input v-model.number="form.stok" type="number" min="0" class="form-control form-control-solid" :class="{ 'is-invalid': errors.stok }"/>
            <div class="invalid-feedback">{{ errors.stok }}</div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold required">Stok Minimum</label>
            <input v-model.number="form.stok_minimum" type="number" min="0" class="form-control form-control-solid"/>
            <div class="text-muted fs-8 mt-1">Batas peringatan stok menipis</div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold required">Harga (Rp)</label>
            <input v-model.number="form.harga" type="number" min="0" class="form-control form-control-solid" :class="{ 'is-invalid': errors.harga }"/>
            <div class="invalid-feedback">{{ errors.harga }}</div>
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold">Keterangan</label>
            <textarea v-model="form.keterangan" class="form-control form-control-solid" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
          </div>

          <div class="col-12">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" v-model="form.is_active" id="activeSwitch"/>
              <label class="form-check-label fw-semibold" for="activeSwitch">Obat Aktif</label>
            </div>
          </div>

        </div>
      </div>
      <div class="card-footer d-flex justify-content-end gap-3 py-4">
        <router-link to="/admin/master/obat" class="btn btn-light btn-active-light-primary">Batal</router-link>
        <button @click="submit" class="btn btn-primary" :disabled="loading">
          <span v-if="!loading"><i class="ki-outline ki-check me-1"></i>{{ isEdit ? 'Simpan Perubahan' : 'Tambah Obat' }}</span>
          <span v-else><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route  = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)
const loading = ref(false)
const errors  = ref<any>({})

const satuanList   = ['Tablet', 'Kapsul', 'Botol', 'Ampul', 'Vial', 'Tube', 'Sachet', 'ml', 'mg']
const kategoriList = ['Antibiotik', 'Analgesik', 'Antasida', 'Vitamin', 'Antihistamin', 'Antihipertensi', 'Antidiabetik', 'Lainnya']

const form = ref({
  kode: '', nama: '', generik: '', satuan: '', kategori: '',
  stok: 0, stok_minimum: 5, harga: 0, keterangan: '', is_active: true,
})

onMounted(async () => {
  if (isEdit.value) {
    const res = await api.get(`/admin/obat/${route.params.id}`)
    Object.assign(form.value, res.data.data)
  }
})

async function submit() {
  errors.value = {}
  loading.value = true
  try {
    if (isEdit.value) {
      await api.put(`/admin/obat/${route.params.id}`, form.value)
    } else {
      await api.post('/admin/obat', form.value)
    }
    router.push('/admin/master/obat')
  } catch (e: any) {
    if (e.response?.data?.errors) errors.value = e.response.data.errors
    else errors.value.general = e.response?.data?.message ?? 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>
