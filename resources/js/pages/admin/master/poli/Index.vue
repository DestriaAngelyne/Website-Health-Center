<template>
  <div class="page">
    <div class="topbar">
      <div class="topbar-left">
        <h1 class="page-title">Master Poli</h1>
        <p class="page-sub">Kelola data poliklinik</p>
      </div>
      <div class="topbar-right">

        <router-link to="/admin/master/poli/create" class="btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Tambah Poli
        </router-link>
      </div>
    </div>

    <div class="filter-bar">
      <div class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input v-model="search" type="text" placeholder="Cari nama atau kode poli..." @input="onSearch" />
      </div>
      <span class="count-tag" v-if="!loading">{{ data.length }} data</span>
    </div>

    <div class="card">
      <div v-if="loading" class="loading-row"><div class="spinner"></div><span>Memuat data...</span></div>
      <div v-else-if="data.length === 0" class="empty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        <p>Belum ada data poli</p>
      </div>
      <div v-else class="table-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th style="width:48px">No</th>
              <th style="width:100px">Kode</th>
              <th>Nama Poli</th>
              <th style="width:120px">Singkatan</th>
              <th>Keterangan</th>
              <th style="width:90px">Status</th>
              <th style="width:210px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, i) in data" :key="item.id">
              <td class="center muted">{{ (page - 1) * perPage + i + 1 }}</td>
              <td><span class="code-tag">{{ item.kode }}</span></td>
              <td class="bold">{{ item.nama_poli }}</td>
              <td class="muted">{{ item.singkatan ?? '—' }}</td>
              <td class="muted sm">{{ item.keterangan ?? '—' }}</td>
              <td><span class="pill" :class="item.is_active ? 'pill-green' : 'pill-red'">{{ item.is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
              <td>
                <div class="actions">
                  <router-link :to="`/admin/master/poli/${item.id}/edit`" class="act act-edit" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                  </router-link>
                  <button @click="toggleActive(item)" class="act act-neutral" :title="item.is_active ? 'Nonaktifkan' : 'Aktifkan'">
                    <svg v-if="item.is_active" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.9" y1="4.9" x2="19.1" y2="19.1"/></svg>
                    <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                  </button>
                  <button @click="hapus(item)" class="act act-danger" title="Hapus">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="lastPage > 1" class="pager">
        <button @click="goPage(page - 1)" :disabled="page === 1" class="page-btn">Prev</button>
        <button v-for="p in pages" :key="p" @click="goPage(p)" class="page-btn" :class="{ active: p === page }">{{ p }}</button>
        <button @click="goPage(page + 1)" :disabled="page === lastPage" class="page-btn">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const data = ref([]), loading = ref(false), search = ref('')
const page = ref(1), lastPage = ref(1), perPage = ref(15)
let t = null

const pages = computed(() => {
  const r = []
  for (let i = Math.max(1, page.value - 2); i <= Math.min(lastPage.value, page.value + 2); i++) r.push(i)
  return r
})

async function loadData() {
  loading.value = true
  try {
    const res = await api.get('/admin/poli', { params: { search: search.value, page: page.value } })
    data.value = res.data.data
    lastPage.value = res.data.meta?.last_page ?? 1
    perPage.value  = res.data.meta?.per_page  ?? 15
  } catch (e) { console.error(e) } finally { loading.value = false }
}

function onSearch() { clearTimeout(t); t = setTimeout(() => { page.value = 1; loadData() }, 400) }

async function toggleActive(item) {
  try { await api.patch(`/admin/poli/${item.id}/toggle-active`); item.is_active = !item.is_active }
  catch { alert('Gagal mengubah status') }
}

async function hapus(item) {
  if (!confirm(`Hapus poli "${item.nama_poli}"?`)) return
  try { await api.delete(`/admin/poli/${item.id}`); loadData() }
  catch (e) { alert(e.response?.data?.message ?? 'Gagal menghapus') }
}

function goPage(p) { page.value = p; loadData() }
onMounted(loadData)
</script>

<style scoped>
:root{
  --bg:#0b1222;--surface:#152033;--surface2:#1a2a40;
  --border:#243450;--text:#e8f0f8;--text2:#8ba3bb;--text3:#4a6380;
  --shadow:0 1px 4px rgba(0,0,0,.5);--shadow-md:0 4px 16px rgba(0,0,0,.4);
}

.page { padding: 1.75rem 2rem; background: var(--bg); min-height: 100vh; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: var(--text); ; }
.topbar { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem; }
.topbar-left {} .topbar-right { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; }
.page-title { font-size: 1.75rem; font-weight: 800; letter-spacing: -.025em; margin: 0; }
.page-sub { font-size: .9rem; color: var(--text2); margin: .3rem 0 0; }

.icon-btn { width: 38px; height: 38px; border-radius: 10px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text2); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .18s; }
.icon-btn svg { width: 17px; height: 17px; } .icon-btn:hover { border-color: #14b8a6; color: #14b8a6; }

.btn-primary { display: inline-flex; align-items: center; gap: .4rem; gap:.5rem;height:42px;padding:0 1.25rem;border-radius:10px;background:#0d9488;color:#fff;font-size:.9rem;font-weight:700;text-decoration:none;font-family:inherit;border:none;cursor:pointer;transition:all .2s;box-shadow:0 4px 14px rgba(13,148,136,.28);}
.btn-primary svg { width: 14px; height: 14px; } .btn-primary:hover { background: #0f766e; transform:translateY(-1px);}


.filter-bar { display: flex; align-items: center; gap: .75rem; margin-bottom: 1rem; }
.search-box { position: relative; flex: 1; max-width: 360px; }
.search-box svg { position: absolute; left: .7rem; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: var(--text3); }
.search-box input { width: 100%; box-sizing: border-box; padding: .55rem .75rem .55rem 2.2rem; border: 1.5px solid var(--border); border-radius: 10px; background: var(--surface); color: var(--text); font-size: .9rem; font-family: inherit; outline: none; transition: border-color .18s; }
.search-box input:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.1); }
.search-box input::placeholder { color: var(--text3); }
.count-tag { font-size: .85rem; color: var(--text3); font-weight: 700; }

.card { background: var(--surface); border: 1.5px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); overflow: hidden; transition: background .2s; }
.loading-row { display: flex; align-items: center; justify-content: center; gap: .75rem; padding: 3rem; color: var(--text3); font-size: .85rem; }
.empty { display: flex; flex-direction: column; align-items: center; gap: .6rem; padding: 3rem; color: var(--text3); }
.empty svg { opacity: .5; } .empty p { margin: 0; font-size: .9rem; }
.spinner { width: 20px; height: 20px; border: 2px solid var(--border); border-top-color: #14b8a6; border-radius: 50%; animation: spin .7s linear infinite; flex-shrink: 0; }
.table-wrap { overflow-x: auto; }

.tbl { width: 100%; border-collapse: collapse; font-size: .9rem; }
.tbl th { background: var(--surface2); padding: .9rem 1.25rem; text-align: left; font-size: .69rem; font-weight: 600; color: var(--text3); text-transform: uppercase; letter-spacing: .07em; border-bottom: 1px solid var(--border); white-space: nowrap; }
.tbl td { padding: .95rem 1.25rem; border-bottom: 1px solid var(--border); }
.tbl tbody tr:last-child td { border-bottom: none; }
.tbl tbody tr:hover td { background: var(--surface2); }
.center { text-align: center; } .bold { font-weight: 700; } .muted { color: var(--text2); } .sm { font-size: .8rem; }

.code-tag { background: var(--surface2); border: 1px solid var(--border); padding: .18rem .5rem; border-radius: 7px; font-family: 'SF Mono', 'Fira Code', monospace; font-size: .8rem; font-weight: 700; }
.pill { padding: .28rem .75rem; border-radius: 7px; font-size: .78rem; font-weight: 700; }
.pill-green { background: rgba(34,197,94,.12); color: #16a34a; } .pill-red { background: rgba(239,68,68,.12); color: #dc2626; }

.actions { display: flex; gap: .35rem; flex-wrap: wrap; }
.act { padding: .27rem .65rem; border-radius: .4rem; border: none; cursor: pointer; font-size: .8rem; font-weight: 700; text-decoration: none; font-family: inherit; transition: all .15s; display: inline-flex; align-items: center; }
.actions{display:flex;gap:.4rem;flex-wrap:nowrap}
.act{width:32px;height:32px;border-radius:8px;border:1.5px solid var(--border);cursor:pointer;font-family:inherit;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;background:var(--surface2);color:var(--text2);text-decoration:none}
.act svg{width:15px;height:15px}
.act-edit:hover{border-color:#0d9488;color:#0d9488;background:rgba(13,148,136,.1)}
.act-neutral:hover{border-color:var(--text2);color:var(--text)}
.act-danger:hover{border-color:#dc2626;color:#dc2626;background:rgba(220,38,38,.1)}

.pager { display: flex; align-items: center; justify-content: center; gap: .4rem; padding: 1rem; border-top: 1px solid var(--border); }
.page-btn { min-width: 32px; height: 32px; padding: 0 .6rem; border-radius: .45rem; border: 1.5px solid var(--border); background: var(--surface); font-size: .8rem; cursor: pointer; color: var(--text2); font-family: inherit; transition: all .15s; display: flex; align-items: center; justify-content: center; }
.page-btn:hover:not(:disabled) { border-color: #14b8a6; color: #14b8a6; }
.page-btn:disabled { opacity: .35; cursor: not-allowed; }
.page-btn.active { background: #0d9488; color: #fff; border-color: #0d9488; }


@keyframes spin { to { transform: rotate(360deg); } }
</style>
