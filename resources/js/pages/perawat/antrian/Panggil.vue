<template>
  <div class="card shadow-sm">
    <div class="card-body">
      <div v-if="loading" class="text-center py-20">
        <div class="spinner-border text-primary" role="status"></div>
        <h4 class="text-gray-600 mt-5">Memuat Data Pasien...</h4>
      </div>

      <div v-else-if="antrian" class="row">
        <div class="col-md-7 text-center border-end">
          <h1 class="display-1 fw-bolder text-primary mb-0">
            {{ antrian.kode_antrian }}-{{ String(antrian.nomor_antrian).padStart(3, '0') }}
          </h1>
          <h2 class="fw-bold mt-5 text-uppercase">{{ antrian.pasien?.nama }}</h2>
          <div class="badge badge-lg badge-light-dark fs-4 mt-2">
            RM: {{ antrian.pasien?.no_rm }}
          </div>

          <div class="d-flex justify-content-center gap-3 mt-10">
            <button @click="ulangiSuara" class="btn btn-outline btn-outline-primary">
              <i class="bi bi-volume-up-fill fs-3"></i> Ulangi Panggilan
            </button>
            <button @click="handleMulaiPeriksa" class="btn btn-success">
              Mulai Periksa
            </button>
          </div>
        </div>

        <div class="col-md-5 ps-lg-10">
          <h3 class="fw-bold mb-5">Informasi Kunjungan</h3>
          <div class="mb-5">
            <label class="text-muted d-block">Jenis Pasien</label>
            <span class="badge badge-light-primary text-uppercase">{{ antrian.jenis_pasien }}</span>
          </div>
          <div class="mb-5">
            <label class="text-muted d-block">Keluhan Utama</label>
            <div class="bg-light p-4 rounded border border-dashed">
              {{ antrian.catatan || 'Tidak ada keluhan tertulis.' }}
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-20">
        <h3 class="text-danger">Data pasien tidak ditemukan.</h3>
        <button @click="$router.back()" class="btn btn-secondary mt-5">Kembali</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()
const antrian = ref(null)
const loading = ref(true)

// Fungsi Utama untuk mengambil data
const getDetailPasien = async () => {
  try {
    loading.value = true
    antrian.value = null // Reset data agar tidak tumpang tindih

    // LOGIKA PENTING:
    // Jika ada ID di URL (dari tabel), gunakan ID itu.
    // Jika TIDAK ADA ID (klik dari sidebar), kirim string kosong agar Laravel cari antrean pertama.
    const id = route.params.id ? route.params.id : '';

    const response = await api.get(`/perawat/antrian/panggil/${id}`)
    antrian.value = response.data.data
  } catch (error) {
    console.error("Error Detail:", error)
    // Jangan munculkan Swal Error jika hanya karena antrean memang habis/kosong
  } finally {
    loading.value = false
  }
}

// Menangani jika user klik menu sidebar saat masih di halaman panggil (agar data refresh)
watch(() => route.params.id, () => {
  getDetailPasien()
})

const handleMulaiPeriksa = async () => {
  try {
    await api.post(`/perawat/antrian/mulai/${antrian.value.id}`)
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: 'Pasien siap dilakukan skrining.',
      timer: 1500
    })
    router.push('/perawat/antrian')
  } catch (error) {
    Swal.fire('Gagal', 'Gagal memperbarui status', 'error')
  }
}

const ulangiSuara = () => {
  if (!antrian.value) return
  const text = `Nomor antrian ${antrian.value.kode_antrian} ${antrian.value.nomor_antrian}, atas nama ${antrian.value.pasien.nama}, silahkan menuju poli.`
  const msg = new SpeechSynthesisUtterance(text)
  msg.lang = 'id-ID'
  window.speechSynthesis.speak(msg)
}

onMounted(getDetailPasien)
</script>
