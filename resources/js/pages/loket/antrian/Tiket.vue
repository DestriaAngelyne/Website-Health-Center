<template>
  <div class="d-flex flex-column gap-7">

    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <router-link to="/loket/antrian" class="btn btn-icon btn-light btn-sm">
          <i class="ki-outline ki-arrow-left fs-4"></i>
        </router-link>
        <div>
          <h1 class="page-heading fw-bold text-gray-900 fs-3 my-0">Cetak Tiket Antrian</h1>
          <span class="text-gray-500 fw-semibold fs-7">Pratinjau dan cetak tiket pasien</span>
        </div>
      </div>
      <button class="btn btn-primary" @click="cetakTiket" :disabled="!antrian">
        <i class="ki-outline ki-printer me-1"></i>Cetak
      </button>
    </div>

    <div v-if="loading" class="d-flex justify-content-center py-15">
      <span class="spinner-border text-primary"></span>
    </div>

    <div v-else-if="!antrian" class="card card-flush">
      <div class="card-body text-center py-15 text-gray-400">
        <i class="ki-outline ki-notepad-edit fs-5x mb-3"></i>
        <p class="fw-semibold">Antrian tidak ditemukan</p>
        <router-link to="/loket/antrian" class="btn btn-light-primary btn-sm mt-2">
          Kembali ke Antrian
        </router-link>
      </div>
    </div>

    <!-- Pratinjau Tiket -->
    <div v-else class="d-flex justify-content-center">
      <div id="tiket-print" class="tiket-wrap">
        <!-- Header -->
        <div class="tiket-header">
          <div class="tiket-logo">
            <svg viewBox="0 0 32 32" fill="none" style="width:32px;height:32px">
              <rect width="32" height="32" rx="8" fill="white" fill-opacity="0.2"/>
              <path d="M16 6v20M6 16h20" stroke="white" stroke-width="3" stroke-linecap="round"/>
            </svg>
          </div>
          <div>
            <div class="tiket-faskes">PUSKESMAS</div>
            <div class="tiket-sub">Pelayanan Kesehatan Prima</div>
          </div>
        </div>

        <!-- Nomor Antrian -->
        <div class="tiket-body">
          <div class="tiket-label-kecil">NOMOR ANTRIAN</div>
          <div class="tiket-nomor">{{ antrian.nomor_display }}</div>
          <div class="tiket-poli-nama">{{ antrian.poli }}</div>
        </div>

        <!-- Garis putus -->
        <div class="tiket-dashed"></div>

        <!-- Info -->
        <div class="tiket-info">
          <div class="tiket-row-info">
            <span class="tiket-key">Pasien</span>
            <span class="tiket-val">{{ antrian.pasien }}</span>
          </div>
          <div class="tiket-row-info">
            <span class="tiket-key">No. RM</span>
            <span class="tiket-val">{{ antrian.no_rm }}</span>
          </div>
          <div class="tiket-row-info">
            <span class="tiket-key">Dokter</span>
            <span class="tiket-val">{{ antrian.dokter }}</span>
          </div>
          <div class="tiket-row-info">
            <span class="tiket-key">Sesi</span>
            <span class="tiket-val">{{ antrian.jam_buka }} – {{ antrian.jam_tutup }}</span>
          </div>
          <div class="tiket-row-info">
            <span class="tiket-key">Jaminan</span>
            <span class="tiket-val tiket-badge" :class="antrian.jenis_pasien === 'bpjs' ? 'bpjs' : 'umum'">
              {{ antrian.jenis_pasien?.toUpperCase() }}
            </span>
          </div>
          <div class="tiket-row-info">
            <span class="tiket-key">Tanggal</span>
            <span class="tiket-val">{{ antrian.tanggal_fmt }}</span>
          </div>
          <div class="tiket-row-info">
            <span class="tiket-key">Waktu Daftar</span>
            <span class="tiket-val">{{ antrian.waktu_daftar }}</span>
          </div>
        </div>

        <!-- Footer -->
        <div class="tiket-footer">
          Harap hadir 10 menit sebelum sesi dimulai
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route   = useRoute()
const loading = ref(false)
const antrian = ref<any>(null)

async function fetchAntrian() {
  const id = route.query.id
  if (!id) return
  loading.value = true
  try {
    const res = await api.get(`/loket/antrian/${id}`)
    antrian.value = res.data.data
  } catch {
    antrian.value = null
  } finally {
    loading.value = false
  }
}

function cetakTiket() {
  const el = document.getElementById('tiket-print')
  if (!el) return
  const win = window.open('', '_blank', 'width=400,height=600')
  if (!win) return
  win.document.write(`
    <html>
    <head>
      <title>Tiket Antrian</title>
      <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: white; display: flex; justify-content: center; padding: 20px; }
        ${document.querySelector('style')?.textContent ?? ''}
      </style>
    </head>
    <body>${el.outerHTML}</body>
    </html>
  `)
  win.document.close()
  win.focus()
  setTimeout(() => { win.print(); win.close() }, 500)
}

onMounted(fetchAntrian)
</script>

<style scoped>
.tiket-wrap {
  width: 300px;
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 40px rgba(0,0,0,0.12);
  border: 1px solid #e2e8f0;
}
.tiket-header {
  background: linear-gradient(135deg, #009ef7, #0078d4);
  padding: 20px 20px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.tiket-faskes { font-size: 16px; font-weight: 800; color: white; letter-spacing: 1px; }
.tiket-sub    { font-size: 10px; color: rgba(255,255,255,0.75); margin-top: 2px; }
.tiket-body   { text-align: center; padding: 24px 20px 16px; }
.tiket-label-kecil { font-size: 10px; font-weight: 700; letter-spacing: 2px; color: #94a3b8; text-transform: uppercase; }
.tiket-nomor  { font-size: 72px; font-weight: 800; color: #009ef7; line-height: 1; margin: 4px 0 8px; font-family: serif; }
.tiket-poli-nama { font-size: 13px; font-weight: 700; color: #374151; background: #f0f9ff; display: inline-block; padding: 4px 14px; border-radius: 100px; }
.tiket-dashed { border-top: 2px dashed #e2e8f0; margin: 0 12px; position: relative; }
.tiket-dashed::before,
.tiket-dashed::after {
  content: '';
  position: absolute;
  top: -10px;
  width: 20px; height: 20px;
  background: #f8fafc;
  border-radius: 50%;
}
.tiket-dashed::before { left: -22px; }
.tiket-dashed::after  { right: -22px; }
.tiket-info     { padding: 16px 20px; }
.tiket-row-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 13px; }
.tiket-key  { color: #94a3b8; font-weight: 500; }
.tiket-val  { font-weight: 700; color: #1e293b; text-align: right; max-width: 180px; }
.tiket-badge { display: inline-block; padding: 2px 10px; border-radius: 100px; font-size: 11px; }
.tiket-badge.bpjs { background: #dcfce7; color: #16a34a; }
.tiket-badge.umum { background: #e0f2fe; color: #0284c7; }
.tiket-footer {
  text-align: center;
  font-size: 11px;
  color: #94a3b8;
  padding: 12px 16px 16px;
  border-top: 1px solid #f1f5f9;
}
</style>
