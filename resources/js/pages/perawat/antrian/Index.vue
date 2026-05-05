<template>
  <div class="antrian-wrapper">
    <div class="d-flex align-items-center justify-content-between mb-8">
      <div>
        <h3 class="text-white fw-bold mb-1">Antrian Poli</h3>
        <div class="d-flex align-items-center gap-3">
          <h1 class="text-white fw-bold mb-0">Antrean {{ dataSesi.nama_poli || '...' }}</h1>
          <span class="badge badge-light-success border border-success px-3 py-2 fw-bold">
            {{ dataSesi.sesi || 'Memuat Sesi...' }}
          </span>
        </div>
      </div>
      <div class="d-flex gap-3">
        <button @click="fetchAntrian" class="btn btn-sm btn-light-primary" :disabled="loading">
          <i :class="loading ? 'fa fa-spinner fa-spin' : 'ki-outline ki-arrows-circle'" class="fs-6 me-1"></i> Refresh
        </button>
        <button @click="handleTutupSesi" class="btn btn-sm btn-primary">
          <i class="ki-outline ki-exit-right fs-6 me-1"></i> Tutup Sesi
        </button>
      </div>
    </div>

    <div class="card bg-dark-card border-0 shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-row-dashed table-row-gray-800 align-middle gs-0 gy-6">
            <thead>
              <tr class="fw-bold text-gray-600 border-0">
                <th class="min-w-100px">No. Antrean</th>
                <th class="min-w-250px">Pasien</th>
                <th class="min-w-150px">Waktu Daftar</th>
                <th class="min-w-100px">Status</th>
                <th class="min-w-100px text-end">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="antrian in daftarAntrian" :key="antrian.id">
                <td>
                  <span class="text-white fw-bold fs-4">
                    {{ antrian.kode_antrian }}-{{ antrian.nomor_antrian }}
                  </span>
                </td>

                <td>
                  <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px symbol-circle me-3">
                      <span class="symbol-label bg-light-primary text-primary fw-bold">
                        {{ antrian.pasien?.nama?.charAt(0) || 'P' }}
                      </span>
                    </div>
                    <div class="d-flex flex-column">
                      <span class="text-white fw-bold fs-6">{{ antrian.pasien?.nama || 'Nama Tidak Ada' }}</span>
                      <span class="text-gray-600 fs-7">
                        RM: {{ antrian.pasien?.no_rm || '-' }} | {{ antrian.jenis_pasien }}
                      </span>
                    </div>
                  </div>
                </td>

                <td class="text-gray-500 fs-7">
                  {{ formatJam(antrian.waktu_daftar) }}
                </td>

                <td>
                  <span :class="statusBadge(antrian.status)">
                    {{ antrian.status }}
                  </span>
                </td>

                <td class="text-end">
                  <div class="d-flex justify-content-end gap-2">
                    <router-link
                    :to="`/perawat/antrian/panggil/${antrian.id}`"
                    class="btn btn-icon btn-sm btn-light-dark"
                    >
                    <i class="ki-outline ki-notification-on fs-5"></i>
                    </router-link>
                    <button @click="handleSelesai(antrian.id)" class="btn btn-icon btn-sm btn-light-success" title="Selesai Periksa">
                      <i class="ki-outline ki-check fs-5"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div v-if="daftarAntrian.length === 0" class="text-center py-10 text-gray-600">
            <i class="ki-outline ki-information-5 fs-1 d-block mb-3"></i>
            Belum ada antrean untuk sesi ini.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const daftarAntrian = ref([])
const dataSesi = ref({
  nama_poli: '',
  sesi: ''
})
const loading = ref(false)

const fetchAntrian = async () => {
  loading.value = true
  try {
    const res = await api.get('/perawat/antrian')
    // Data dari backend dimasukkan ke daftarAntrian
    daftarAntrian.value = res.data.data
    dataSesi.value = {
      nama_poli: res.data.poli,
      sesi: res.data.sesi
    }
  } catch (err) {
    console.error("Gagal memuat data antrian:", err)
  } finally {
    loading.value = false
  }
}

const statusBadge = (status) => {
  const base = 'badge fw-bold px-3 py-2 '
  // Menyesuaikan warna berdasarkan status di database
  if (status === 'dipanggil') return base + 'badge-light-warning text-warning'
  if (status === 'menunggu') return base + 'badge-light-primary text-primary'
  if (status === 'selesai') return base + 'badge-light-success text-success'
  return base + 'badge-light-dark text-gray-600'
}

const formatJam = (timeString) => {
  if (!timeString) return '--:--'
  // Karena format di DB adalah timestamp/time, kita ambil jam dan menitnya saja
  const date = new Date(timeString)
  return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

const handleTutupSesi = async () => {
  if (confirm('Apakah Anda yakin ingin menutup sesi poli ini?')) {
    try {
      await api.post('/perawat/antrian/tutup-sesi')
      fetchAntrian()
    } catch (err) {
      alert('Gagal menutup sesi')
    }
  }
}

onMounted(fetchAntrian)
</script>

<style scoped>
.bg-dark-card { background-color: #1e1e2d; }
/* Membuat scrollbar lebih halus untuk table-responsive */
.table-responsive::-webkit-scrollbar {
  height: 6px;
}
.table-responsive::-webkit-scrollbar-thumb {
  background: #2b2b40;
  border-radius: 10px;
}
</style>
