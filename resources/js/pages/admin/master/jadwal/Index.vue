<template>
  <div class="page">
    <div class="topbar">
      <div class="topbar-left"><h1 class="page-title">Jadwal Dokter</h1><p class="page-sub">Kelola jadwal praktik dokter</p></div>
      <div class="topbar-right">
        <router-link to="/admin/master/jadwal/create" class="btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Tambah Jadwal</router-link>
      </div>
    </div>
    <div class="filter-bar">
      <select v-model="dokterFilter" class="filter-select" @change="loadData"><option value="">Semua Dokter</option><option v-for="d in dokterList" :key="d.id" :value="d.id">{{ d.nama }}</option></select>
      <select v-model="poliFilter" class="filter-select" @change="loadData"><option value="">Semua Poli</option><option v-for="p in poliList" :key="p.id" :value="p.id">{{ p.nama_poli }}</option></select>
      <span class="count-tag" v-if="!loading">{{ data.length }} jadwal</span>
    </div>
    <div class="card">
      <div v-if="loading" class="loading-row"><div class="spinner"></div><span>Memuat data...</span></div>
      <div v-else-if="data.length === 0" class="empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="36" height="36"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><p>Belum ada jadwal</p></div>
      <div v-else class="table-wrap">
        <table class="tbl">
          <thead><tr><th style="width:48px">No</th><th>Dokter</th><th>Poli</th><th style="width:100px">Hari</th><th style="width:100px">Jam Buka</th><th style="width:100px">Jam Tutup</th><th style="width:70px">Kuota</th><th style="width:90px">Status</th><th style="width:130px">Aksi</th></tr></thead>
          <tbody>
            <tr v-for="(item, i) in data" :key="item.id">
              <td class="center muted">{{ i + 1 }}</td>
              <td class="bold">{{ item.dokter?.nama ?? '—' }}</td>
              <td class="muted">{{ item.dokter?.poli?.nama_poli ?? '—' }}</td>
              <td><span class="hari-tag" :class="'h-' + (item.hari ?? '').toLowerCase()">{{ item.hari }}</span></td>
              <td><span class="time-tag">{{ item.jam_buka }}</span></td>
              <td><span class="time-tag">{{ item.jam_tutup }}</span></td>
              <td class="center bold">{{ item.kuota }}</td>
              <td><span class="pill" :class="item.is_active ? 'pill-green' : 'pill-red'">{{ item.is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
              <td><div class="actions"><router-link :to="`/admin/master/jadwal/${item.id}/edit`" class="act act-yellow">Edit</router-link><button @click="hapus(item)" class="act act-red">Hapus</button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
const data = ref([]), loading = ref(false), dokterFilter = ref(''), poliFilter = ref(''), dokterList = ref([]), poliList = ref([])
async function loadData() {
  loading.value = true
  try { const r = await api.get('/admin/jadwal', { params: { dokter_id: dokterFilter.value, poli_id: poliFilter.value } }); data.value = r.data.data }
  catch (e) { console.error(e) } finally { loading.value = false }
}
async function hapus(item) {
  if (!confirm(`Hapus jadwal ${item.hari} - ${item.dokter?.nama}?`)) return
  try { await api.delete(`/admin/jadwal/${item.id}`); loadData() } catch (e) { alert(e.response?.data?.message ?? 'Gagal') }
}
onMounted(async () => {
  const [dr, pr] = await Promise.all([api.get('/admin/dokter', { params: { per_page: 100 } }), api.get('/admin/poli', { params: { per_page: 100 } })])
  dokterList.value = dr.data.data; poliList.value = pr.data.data; loadData()
})
</script>
<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap");
:root,[data-theme="light"]{--bg:#f1f5f9;--surface:#fff;--surface2:#f8fafc;--border:#e2e8f0;--text:#0f172a;--text2:#475569;--text3:#94a3b8;--shadow:0 1px 3px rgba(0,0,0,.07)}
[data-theme="dark"]{--bg:#0f172a;--surface:#1e293b;--surface2:#162032;--border:#334155;--text:#f1f5f9;--text2:#94a3b8;--text3:#475569;--shadow:0 1px 4px rgba(0,0,0,.4)}
*{box-sizing:border-box}
.page{padding:1.75rem 2rem;background:var(--bg);min-height:100vh;font-family:"DM Sans",system-ui,sans-serif;color:var(--text)}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;gap:1rem}
.topbar-left{display:flex;align-items:center;gap:.75rem}
.topbar-right{display:flex;align-items:center;gap:.5rem;flex-shrink:0}
.page-title{font-size:1.5rem;font-weight:800;letter-spacing:-.03em;margin:0;color:var(--text)}
.page-sub{font-size:.85rem;color:var(--text2);margin:.15rem 0 0;font-weight:500}
.btn-primary{display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.15rem;border-radius:.55rem;background:#0f172a;color:#fff;font-size:.88rem;font-weight:700;text-decoration:none;font-family:inherit;border:none;cursor:pointer;transition:all .18s}
.btn-primary svg{width:14px;height:14px;flex-shrink:0}
.btn-primary:hover{background:#1e293b;transform:translateY(-1px)}
[data-theme="dark"] .btn-primary{background:#f1f5f9;color:#0f172a}
[data-theme="dark"] .btn-primary:hover{background:#e2e8f0}
.filter-bar{display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;flex-wrap:wrap}
.filter-select{padding:.55rem .9rem;border:1.5px solid var(--border);border-radius:.55rem;background:var(--surface);color:var(--text);font-size:.88rem;font-weight:500;font-family:inherit;outline:none;cursor:pointer}
.filter-select:focus{border-color:#14b8a6}
.count-tag{font-size:.8rem;color:var(--text3);font-weight:600}
.card{background:var(--surface);border:1.5px solid var(--border);border-radius:.875rem;box-shadow:var(--shadow);overflow:hidden}
.loading-row{display:flex;align-items:center;justify-content:center;gap:.75rem;padding:3rem;color:var(--text3);font-size:.9rem;font-weight:500}
.spinner{width:20px;height:20px;border:2px solid var(--border);border-top-color:#14b8a6;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}
.empty{display:flex;flex-direction:column;align-items:center;gap:.6rem;padding:3rem;color:var(--text3)}
.empty svg{opacity:.5}
.empty p{margin:0;font-size:.9rem;font-weight:500}
.table-wrap{overflow-x:auto}
.tbl{width:100%;border-collapse:collapse;font-size:.88rem}
.tbl th{background:var(--surface2);padding:.8rem 1rem;text-align:left;font-size:.72rem;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;border-bottom:1px solid var(--border);white-space:nowrap}
.tbl td{padding:.85rem 1rem;border-bottom:1px solid var(--border);font-weight:500}
.tbl tbody tr:last-child td{border-bottom:none}
.tbl tbody tr:hover td{background:var(--surface2)}
.center{text-align:center}
.bold{font-weight:700}
.muted{color:var(--text2)}
.pill{padding:.22rem .65rem;border-radius:.35rem;font-size:.75rem;font-weight:700}
.pill-green{background:rgba(34,197,94,.12);color:#16a34a}
.pill-red{background:rgba(239,68,68,.12);color:#dc2626}
.actions{display:flex;gap:.35rem;flex-wrap:wrap}
.act{padding:.3rem .7rem;border-radius:.4rem;border:none;cursor:pointer;font-size:.79rem;font-weight:600;text-decoration:none;font-family:inherit;transition:all .15s;display:inline-flex;align-items:center}
.act-yellow{background:rgba(245,158,11,.12);color:#d97706}.act-yellow:hover{background:rgba(245,158,11,.22)}
.act-red{background:rgba(239,68,68,.12);color:#dc2626}.act-red:hover{background:rgba(239,68,68,.22)}
.hari-tag{padding:.22rem .65rem;border-radius:.35rem;font-size:.75rem;font-weight:700}
.h-senin{background:rgba(59,130,246,.12);color:#2563eb}
.h-selasa{background:rgba(34,197,94,.12);color:#16a34a}
.h-rabu{background:rgba(245,158,11,.12);color:#d97706}
.h-kamis{background:rgba(168,85,247,.12);color:#7c3aed}
.h-jumat{background:rgba(239,68,68,.12);color:#dc2626}
.h-sabtu{background:rgba(20,184,166,.12);color:#0d9488}
.time-tag{font-variant-numeric:tabular-nums;font-size:.83rem;font-weight:600;color:var(--text2);background:var(--surface2);padding:.2rem .55rem;border-radius:.35rem;border:1px solid var(--border)}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
