<template>
  <div class="page">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Master Obat</h1>
        <p class="page-sub">Kelola data obat dan stok puskesmas</p>
      </div>
      <router-link to="/admin/master/obat/create" class="btn-primary">
        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 4v12M4 10h12"/></svg>
        Tambah Obat
      </router-link>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-chip">
        <span class="stat-dot" style="background:#6366f1"></span>
        <span class="stat-val">{{ meta.total ?? 0 }}</span>
        <span class="stat-lbl">Total Obat</span>
      </div>
      <div class="stat-chip">
        <span class="stat-dot" style="background:#22c55e"></span>
        <span class="stat-val">{{ jumlahAktif }}</span>
        <span class="stat-lbl">Aktif</span>
      </div>
      <div class="stat-chip">
        <span class="stat-dot" style="background:#ef4444"></span>
        <span class="stat-val">{{ jumlahKritis }}</span>
        <span class="stat-lbl">Stok Kritis</span>
      </div>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
      <div class="search-wrap">
        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="9" r="5"/><path d="M15 15l-3-3"/></svg>
        <input v-model="filters.search" @input="debouncedLoad" type="text" placeholder="Cari nama atau kode obat..." class="search-input"/>
        <button v-if="filters.search" @click="filters.search = ''; loadData()" class="clear-btn">×</button>
      </div>
      <select v-model="filters.kategori" @change="loadData" class="filter-select">
        <option value="">Semua Kategori</option>
        <option v-for="k in kategoriList" :key="k" :value="k">{{ k }}</option>
      </select>
      <select v-model="filters.status" @change="loadData" class="filter-select">
        <option value="">Semua Status</option>
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
      </select>
      <select v-model="filters.stok" @change="loadData" class="filter-select">
        <option value="">Semua Stok</option>
        <option value="kritis">Stok Kritis</option>
      </select>
    </div>

    <!-- Alert -->
    <div v-if="alertMsg" class="alert" :class="alertType">
      <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0">
        <path v-if="alertType==='success'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        <path v-else fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
      </svg>
      {{ alertMsg }}
      <button @click="alertMsg = ''" class="alert-close">×</button>
    </div>

    <!-- Table -->
    <div class="table-card">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <span>Memuat data...</span>
      </div>
      <div v-else-if="obatList.length === 0" class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;color:#cbd5e1">
          <path d="M10.5 20H4a2 2 0 01-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 011.66.9l.82 1.2a2 2 0 001.66.9H20a2 2 0 012 2v3"/>
          <circle cx="18" cy="18" r="3"/><path d="M18 15v3l2 1"/>
        </svg>
        <p>Tidak ada obat ditemukan</p>
        <router-link to="/admin/master/obat/create" class="btn-primary" style="margin-top:8px">Tambah Obat Pertama</router-link>
      </div>
      <table v-else class="tbl">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Obat</th>
            <th>Kategori</th>
            <th>Satuan</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="o in obatList" :key="o.id" :class="{ 'row-kritis': o.stok <= o.stok_minimum }">
            <td><span class="kode-badge">{{ o.kode_obat }}</span></td>
            <td>
              <div class="nama-cell">
                <span class="nama-text">{{ o.nama_obat }}</span>
                <span v-if="o.deskripsi" class="nama-desc">{{ o.deskripsi.slice(0, 50) }}{{ o.deskripsi.length > 50 ? '...' : '' }}</span>
              </div>
            </td>
            <td><span class="kategori-badge">{{ o.kategori || '-' }}</span></td>
            <td class="muted">{{ o.satuan }}</td>
            <td>
              <div class="stok-cell">
                <span class="stok-num" :class="{ kritis: o.stok <= o.stok_minimum }">{{ o.stok }}</span>
                <span v-if="o.stok <= o.stok_minimum" class="kritis-tag">Kritis</span>
              </div>
            </td>
            <td class="harga-cell">{{ formatHarga(o.harga) }}</td>
            <td>
              <button class="toggle-btn" :class="o.is_active ? 'active' : 'inactive'" @click="toggleStatus(o)">
                {{ o.is_active ? 'Aktif' : 'Nonaktif' }}
              </button>
            </td>
            <td>
              <div class="aksi-row">
                <router-link :to="`/admin/master/obat/${o.id}/edit`" class="btn-icon edit" title="Edit">
                  <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </router-link>
                <button class="btn-icon delete" title="Hapus" @click="confirmDelete(o)">
                  <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M8 6V4h8v2"/></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1" class="pagination">
        <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page === 1" class="page-btn">‹</button>
        <button v-for="p in pageRange" :key="p" @click="changePage(p)" class="page-btn" :class="{ active: p === meta.current_page }">{{ p }}</button>
        <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page" class="page-btn">›</button>
        <span class="page-info">{{ meta.total }} data</span>
      </div>
    </div>

    <!-- Modal Hapus -->
    <div v-if="deleteTarget" class="modal-backdrop" @click.self="deleteTarget = null">
      <div class="modal">
        <div class="modal-icon danger">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M8 6V4h8v2"/></svg>
        </div>
        <h3 class="modal-title">Hapus Obat</h3>
        <p class="modal-desc">Yakin ingin menghapus <strong>{{ deleteTarget.nama_obat }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
          <button @click="deleteTarget = null" class="btn-cancel">Batal</button>
          <button @click="doDelete" class="btn-danger" :disabled="deleting">{{ deleting ? 'Menghapus...' : 'Hapus' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const obatList     = ref([])
const meta         = ref({ total: 0, per_page: 15, current_page: 1, last_page: 1 })
const loading      = ref(false)
const deleting     = ref(false)
const alertMsg     = ref('')
const alertType    = ref('success')
const deleteTarget = ref(null)
const kategoriList = ref([])
const filters      = ref({ search: '', kategori: '', status: '', stok: '' })

const jumlahAktif  = computed(() => obatList.value.filter(o => o.is_active).length)
const jumlahKritis = computed(() => obatList.value.filter(o => o.stok <= o.stok_minimum).length)

const pageRange = computed(() => {
  const cur = meta.value.current_page, last = meta.value.last_page, pages = []
  for (let i = Math.max(1, cur - 2); i <= Math.min(last, cur + 2); i++) pages.push(i)
  return pages
})

async function loadData(page = 1) {
  loading.value = true
  try {
    const res = await api.get('/admin/master/obat', { params: { ...filters.value, page, per_page: 15 } })
    obatList.value = res.data.data; meta.value = res.data.meta
  } catch { showAlert('Gagal memuat data obat.', 'error') }
  finally { loading.value = false }
}

async function loadKategori() {
  try { const res = await api.get('/admin/master/obat/kategori-list'); kategoriList.value = res.data.data } catch { /* silent */ }
}

async function toggleStatus(o) {
  try { await api.patch(`/admin/master/obat/${o.id}/toggle`); o.is_active = !o.is_active; showAlert(`Status ${o.nama_obat} diperbarui.`, 'success') }
  catch { showAlert('Gagal mengubah status.', 'error') }
}

function confirmDelete(o) { deleteTarget.value = o }

async function doDelete() {
  deleting.value = true
  try {
    await api.delete(`/admin/master/obat/${deleteTarget.value.id}`)
    showAlert(`${deleteTarget.value.nama_obat} berhasil dihapus.`, 'success')
    deleteTarget.value = null; loadData(meta.value.current_page)
  } catch { showAlert('Gagal menghapus obat.', 'error') }
  finally { deleting.value = false }
}

function changePage(p) { if (p < 1 || p > meta.value.last_page) return; loadData(p) }

function formatHarga(h) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(h)
}

function showAlert(msg, type = 'success') {
  alertMsg.value = msg; alertType.value = type
  setTimeout(() => { alertMsg.value = '' }, 3500)
}

let debounceTimer = null
function debouncedLoad() { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => loadData(1), 400) }

onMounted(() => { loadData(); loadKategori() })
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap");
:root,[data-theme="light"]{--bg:#f1f5f9;--surface:#fff;--surface2:#f8fafc;--border:#e2e8f0;--text:#0f172a;--text2:#475569;--text3:#94a3b8;--shadow:0 1px 3px rgba(0,0,0,.07)}
[data-theme="dark"]{--bg:#0f172a;--surface:#1e293b;--surface2:#162032;--border:#334155;--text:#f1f5f9;--text2:#94a3b8;--text3:#475569;--shadow:0 1px 4px rgba(0,0,0,.4)}
*{box-sizing:border-box}
.page{padding:1.75rem 2rem;background:var(--bg);min-height:100vh;font-family:"DM Sans",system-ui,sans-serif;color:var(--text)}
.page-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;gap:1rem;flex-wrap:wrap}
.page-title{font-size:1.5rem;font-weight:800;margin:0;letter-spacing:-.03em;color:var(--text)}
.page-sub{font-size:.88rem;color:var(--text2);margin:.2rem 0 0;font-weight:500}
.btn-primary{display:inline-flex;align-items:center;gap:6px;background:#6366f1;color:white;text-decoration:none;border:none;border-radius:.6rem;padding:.58rem 1.15rem;font-size:.88rem;font-weight:700;cursor:pointer;font-family:inherit;transition:all .18s}
.btn-primary:hover{background:#4f46e5;transform:translateY(-1px)}
.btn-primary svg{width:14px;height:14px;flex-shrink:0}
.stats-row{display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap}
.stat-chip{display:flex;align-items:center;gap:.5rem;background:var(--surface);border:1.5px solid var(--border);border-radius:.6rem;padding:.5rem 1rem}
.stat-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.stat-val{font-size:1.15rem;font-weight:800;color:var(--text)}
.stat-lbl{font-size:.78rem;color:var(--text2);font-weight:500}
.filter-bar{display:flex;gap:.6rem;margin-bottom:1rem;flex-wrap:wrap}
.search-wrap{position:relative;flex:1;min-width:200px;display:flex;align-items:center}
.search-wrap svg{position:absolute;left:10px;width:15px;height:15px;color:var(--text3);pointer-events:none}
.search-input{width:100%;padding:.55rem 2rem .55rem 2rem;border:1.5px solid var(--border);border-radius:.55rem;font-size:.88rem;font-weight:500;font-family:inherit;background:var(--surface);color:var(--text);outline:none}
.search-input:focus{border-color:#6366f1}
.search-input::placeholder{color:var(--text3)}
.clear-btn{position:absolute;right:8px;background:none;border:none;cursor:pointer;color:var(--text3);font-size:16px;line-height:1}
.filter-select{padding:.55rem .85rem;border:1.5px solid var(--border);border-radius:.55rem;font-size:.88rem;font-weight:500;font-family:inherit;background:var(--surface);color:var(--text);outline:none;cursor:pointer}
.filter-select:focus{border-color:#6366f1}
.alert{display:flex;align-items:center;gap:8px;padding:.75rem 1rem;border-radius:.6rem;margin-bottom:1rem;font-size:.88rem;font-weight:500}
.alert.success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#16a34a}
.alert.error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444}
.alert-close{margin-left:auto;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;color:inherit}
.table-card{background:var(--surface);border:1.5px solid var(--border);border-radius:.875rem;overflow:hidden}
.loading-state{display:flex;align-items:center;justify-content:center;gap:.75rem;padding:3rem;color:var(--text3);font-size:.9rem;font-weight:500}
.spinner{width:20px;height:20px;border:2.5px solid var(--border);border-top-color:#6366f1;border-radius:50%;animation:spin .7s linear infinite}
.empty-state{display:flex;flex-direction:column;align-items:center;gap:8px;padding:3rem;color:var(--text3)}
.empty-state p{margin:0;font-size:.92rem;font-weight:500}
.tbl{width:100%;border-collapse:collapse;font-size:.88rem}
.tbl th{background:var(--surface2);padding:.8rem 1rem;text-align:left;font-size:.72rem;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;border-bottom:1.5px solid var(--border);white-space:nowrap}
.tbl td{padding:.85rem 1rem;border-bottom:1px solid var(--border);vertical-align:middle;font-weight:500}
.tbl tbody tr:last-child td{border-bottom:none}
.tbl tbody tr:hover td{background:var(--surface2)}
.tbl tbody tr.row-kritis td{background:rgba(239,68,68,.05)}
.kode-badge{font-family:monospace;font-size:.8rem;font-weight:700;background:var(--surface2);border:1px solid var(--border);padding:.15rem .5rem;border-radius:.375rem;color:#6366f1}
.nama-cell{display:flex;flex-direction:column;gap:2px}
.nama-text{font-weight:700}
.nama-desc{font-size:.77rem;color:var(--text3)}
.kategori-badge{font-size:.77rem;background:rgba(99,102,241,.12);color:#818cf8;padding:.15rem .6rem;border-radius:999px;font-weight:600}
.muted{color:var(--text2)}
.harga-cell{font-weight:700;font-variant-numeric:tabular-nums;color:#34d399}
.stok-cell{display:flex;align-items:center;gap:6px}
.stok-num{font-weight:700;font-variant-numeric:tabular-nums}
.stok-num.kritis{color:#ef4444}
.kritis-tag{font-size:.7rem;font-weight:700;background:rgba(239,68,68,.15);color:#ef4444;padding:1px 6px;border-radius:999px}
.toggle-btn{font-size:.77rem;font-weight:700;padding:.22rem .75rem;border-radius:999px;border:none;cursor:pointer;font-family:inherit;transition:all .18s}
.toggle-btn.active{background:rgba(34,197,94,.15);color:#16a34a}
.toggle-btn.inactive{background:var(--surface2);color:var(--text3);border:1px solid var(--border)}
.aksi-row{display:flex;gap:6px}
.btn-icon{width:30px;height:30px;border-radius:.4rem;border:1.5px solid var(--border);background:var(--surface);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .18s;text-decoration:none}
.btn-icon svg{width:13px;height:13px}
.btn-icon.edit{color:#818cf8}.btn-icon.edit:hover{background:rgba(99,102,241,.15);border-color:#6366f1}
.btn-icon.delete{color:#f87171}.btn-icon.delete:hover{background:rgba(239,68,68,.15);border-color:#ef4444}
.pagination{display:flex;align-items:center;gap:4px;padding:.875rem 1rem;border-top:1px solid var(--border);flex-wrap:wrap}
.page-btn{min-width:32px;height:32px;border-radius:.4rem;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);font-size:.84rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s}
.page-btn:hover:not(:disabled){border-color:#6366f1;color:#818cf8}
.page-btn.active{background:#6366f1;border-color:#6366f1;color:white}
.page-btn:disabled{opacity:.35;cursor:not-allowed}
.page-info{margin-left:auto;font-size:.8rem;color:var(--text3);font-weight:500}
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;z-index:999;padding:1rem}
.modal{background:var(--surface);border:1px solid var(--border);border-radius:1rem;padding:2rem;max-width:380px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.4)}
.modal-icon{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem}
.modal-icon.danger{background:rgba(239,68,68,.15);color:#ef4444}
.modal-icon svg{width:24px;height:24px}
.modal-title{font-size:1.15rem;font-weight:800;margin:0 0 .5rem;color:var(--text)}
.modal-desc{font-size:.9rem;color:var(--text2);margin:0 0 1.5rem;font-weight:500}
.modal-actions{display:flex;gap:.75rem;justify-content:center}
.btn-cancel{padding:.58rem 1.25rem;border-radius:.55rem;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);font-size:.88rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all .18s}
.btn-cancel:hover{border-color:var(--text3)}
.btn-danger{padding:.58rem 1.25rem;border-radius:.55rem;border:none;background:#ef4444;color:white;font-size:.88rem;font-weight:700;cursor:pointer;font-family:inherit;transition:all .18s}
.btn-danger:hover:not(:disabled){background:#dc2626}
.btn-danger:disabled{opacity:.5;cursor:not-allowed}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
