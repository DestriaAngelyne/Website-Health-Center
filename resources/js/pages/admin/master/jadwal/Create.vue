<template>
  <div class="page">
    <div class="topbar">
      <div class="topbar-left">
        <router-link to="/admin/master/jadwal" class="back-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg></router-link>
        <div><h1 class="page-title">Tambah Jadwal</h1><p class="page-sub">Tambah jadwal praktik dokter</p></div>
      </div>
    </div>
    <div class="card">
      <form @submit.prevent="submit">
        <div class="section">
          <p class="sec-title">Informasi Jadwal</p>
          <div class="grid2">
            <div class="field"><label class="label">Dokter <span class="req">*</span></label><select v-model="form.dokter_id" class="input" :class="{ err: errors.dokter_id }"><option value="">— Pilih Dokter —</option><option v-for="d in dokterList" :key="d.id" :value="d.id">{{ d.nama }} ({{ d.poli?.nama_poli }})</option></select><p v-if="errors.dokter_id" class="err-msg">{{ errors.dokter_id[0] }}</p></div>
            <div class="field"><label class="label">Hari <span class="req">*</span></label><select v-model="form.hari" class="input" :class="{ err: errors.hari }"><option value="">— Pilih Hari —</option><option v-for="h in hariList" :key="h" :value="h">{{ h }}</option></select><p v-if="errors.hari" class="err-msg">{{ errors.hari[0] }}</p></div>
          </div>
          <div class="grid3">
            <div class="field"><label class="label">Jam Buka <span class="req">*</span></label><input v-model="form.jam_buka" type="time" class="input" :class="{ err: errors.jam_buka }" /><p v-if="errors.jam_buka" class="err-msg">{{ errors.jam_buka[0] }}</p></div>
            <div class="field"><label class="label">Jam Tutup <span class="req">*</span></label><input v-model="form.jam_tutup" type="time" class="input" :class="{ err: errors.jam_tutup }" /><p v-if="errors.jam_tutup" class="err-msg">{{ errors.jam_tutup[0] }}</p></div>
            <div class="field"><label class="label">Kuota <span class="req">*</span></label><input v-model.number="form.kuota" type="number" min="1" max="200" class="input" :class="{ err: errors.kuota }" placeholder="20" /><p v-if="errors.kuota" class="err-msg">{{ errors.kuota[0] }}</p></div>
          </div>
          <div class="field" style="max-width:200px"><label class="label">Status</label><select v-model="form.is_active" class="input"><option :value="true">Aktif</option><option :value="false">Nonaktif</option></select></div>
        </div>
        <div class="footer">
          <router-link to="/admin/master/jadwal" class="btn-ghost">Batal</router-link>
          <button type="submit" class="btn-save" :disabled="saving"><span v-if="saving" class="btn-spin"></span>{{ saving ? 'Menyimpan...' : 'Simpan Jadwal' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
const router = useRouter()
const hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']
const form = ref({ dokter_id: '', hari: '', jam_buka: '08:00', jam_tutup: '12:00', kuota: 20, is_active: true })
const errors = ref({}), saving = ref(false), dokterList = ref([])
onMounted(async () => { const r = await api.get('/admin/dokter', { params: { per_page: 100 } }); dokterList.value = r.data.data })
async function submit() {
  saving.value = true; errors.value = {}
  try { await api.post('/admin/jadwal', form.value); router.push('/admin/master/jadwal') }
  catch (e) { if (e.response?.status === 422) errors.value = e.response.data.errors; else alert(e.response?.data?.message ?? 'Terjadi kesalahan') }
  finally { saving.value = false }
}
</script>
<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap");
:root,[data-theme="light"]{--bg:#f1f5f9;--surface:#fff;--surface2:#f8fafc;--border:#e2e8f0;--text:#0f172a;--text2:#475569;--text3:#94a3b8;--shadow:0 1px 3px rgba(0,0,0,.07)}
[data-theme="dark"]{--bg:#0f172a;--surface:#1e293b;--surface2:#162032;--border:#334155;--text:#f1f5f9;--text2:#94a3b8;--text3:#475569;--shadow:0 1px 4px rgba(0,0,0,.4)}
*{box-sizing:border-box}
.page{padding:1.75rem 2rem;background:var(--bg);min-height:100vh;font-family:"DM Sans",system-ui,sans-serif;color:var(--text)}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;gap:1rem}
.topbar-left{display:flex;align-items:center;gap:.75rem}
.back-btn{width:36px;height:36px;border-radius:.55rem;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);display:flex;align-items:center;justify-content:center;text-decoration:none;cursor:pointer;transition:all .18s;flex-shrink:0}
.back-btn svg{width:16px;height:16px}
.back-btn:hover{border-color:#14b8a6;color:#14b8a6}
.page-title{font-size:1.5rem;font-weight:800;letter-spacing:-.03em;margin:0;color:var(--text)}
.page-sub{font-size:.85rem;color:var(--text2);margin:.15rem 0 0;font-weight:500}
.card{background:var(--surface);border:1.5px solid var(--border);border-radius:.875rem;box-shadow:var(--shadow);overflow:hidden}
.section{padding:1.75rem}
.section+.section{border-top:1px solid var(--border)}
.sec-title{font-size:.72rem;font-weight:800;color:var(--text3);text-transform:uppercase;letter-spacing:.09em;margin:0 0 1.2rem}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem}
.grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem}
@media(max-width:600px){.grid2,.grid3{grid-template-columns:1fr}}
.field{display:flex;flex-direction:column;gap:.3rem;margin-bottom:1rem}
.field:last-child{margin-bottom:0}
.label{font-size:.85rem;font-weight:700;color:var(--text)}
.req{color:#ef4444}
.input{padding:.6rem .85rem;border:1.5px solid var(--border);border-radius:.55rem;font-size:.88rem;font-weight:500;font-family:inherit;background:var(--surface2);color:var(--text);outline:none;transition:all .18s;width:100%;box-sizing:border-box}
.input:focus{border-color:#14b8a6;background:var(--surface);box-shadow:0 0 0 3px rgba(20,184,166,.1)}
.input::placeholder{color:var(--text3)}
.input.err{border-color:#ef4444}
.err-msg{font-size:.76rem;color:#ef4444;margin:0;font-weight:500}
.footer{display:flex;align-items:center;justify-content:flex-end;gap:.65rem;padding:1.1rem 1.75rem;border-top:1px solid var(--border);background:var(--surface2)}
.btn-ghost{display:inline-flex;align-items:center;padding:.55rem 1.15rem;border-radius:.55rem;border:1.5px solid var(--border);background:transparent;color:var(--text2);font-size:.88rem;font-weight:600;font-family:inherit;text-decoration:none;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:var(--text2);color:var(--text)}
.btn-save{display:inline-flex;align-items:center;gap:.45rem;padding:.55rem 1.35rem;border-radius:.55rem;background:#0f172a;color:#fff;font-size:.88rem;font-weight:700;font-family:inherit;border:none;cursor:pointer;transition:all .18s}
.btn-save:hover:not(:disabled){background:#1e293b;transform:translateY(-1px)}
.btn-save:disabled{opacity:.55;cursor:not-allowed}
[data-theme="dark"] .btn-save{background:#f1f5f9;color:#0f172a}
[data-theme="dark"] .btn-save:hover:not(:disabled){background:#e2e8f0}
.btn-spin{width:13px;height:13px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}
[data-theme="dark"] .btn-spin{border-color:rgba(0,0,0,.2);border-top-color:#0f172a}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
