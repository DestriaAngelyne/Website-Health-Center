<template>
  <div class="page">
    <div class="topbar">
      <div class="topbar-left">
        <router-link to="/admin/master/poli" class="back-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg></router-link>
        <div><h1 class="page-title">Edit Poli</h1><p class="page-sub">Ubah data poliklinik</p></div>
      </div>

    </div>
    <div v-if="loadingData" class="loading-state"><div class="spinner"></div><span>Memuat data...</span></div>
    <div v-else class="card">
      <form @submit.prevent="submit">
        <div class="section">
          <p class="sec-title">Informasi Poli</p>
          <div class="grid2">
            <div class="field"><label class="label">Kode Poli <span class="req">*</span></label><input v-model="form.kode" type="text" class="input" :class="{ err: errors.kode }" /><p v-if="errors.kode" class="err-msg">{{ errors.kode[0] }}</p></div>
            <div class="field"><label class="label">Nama Poli <span class="req">*</span></label><input v-model="form.nama_poli" type="text" class="input" :class="{ err: errors.nama_poli }" /><p v-if="errors.nama_poli" class="err-msg">{{ errors.nama_poli[0] }}</p></div>
          </div>
          <div class="grid2">
            <div class="field"><label class="label">Singkatan</label><input v-model="form.singkatan" type="text" class="input" /></div>
            <div class="field"><label class="label">Status</label><select v-model="form.is_active" class="input"><option :value="true">Aktif</option><option :value="false">Nonaktif</option></select></div>
          </div>
          <div class="field"><label class="label">Keterangan</label><textarea v-model="form.keterangan" class="input textarea" rows="3"></textarea></div>
        </div>
        <div class="footer">
          <router-link to="/admin/master/poli" class="btn-ghost">Batal</router-link>
          <button type="submit" class="btn-save" :disabled="saving"><span v-if="saving" class="btn-spin"></span>{{ saving ? 'Menyimpan...' : 'Update Poli' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
const route = useRoute(), router = useRouter()
const form = ref({ kode: '', nama_poli: '', singkatan: '', keterangan: '', is_active: true })
const errors = ref({}), saving = ref(false), loadingData = ref(true)
onMounted(async () => {
  try { const r = await api.get(`/admin/poli/${route.params.id}`); const d = r.data.data; form.value = { kode: d.kode, nama_poli: d.nama_poli, singkatan: d.singkatan ?? '', keterangan: d.keterangan ?? '', is_active: d.is_active } }
  catch { alert('Gagal memuat data') } finally { loadingData.value = false }
})
async function submit() {
  saving.value = true; errors.value = {}
  try { await api.put(`/admin/poli/${route.params.id}`, form.value); router.push('/admin/master/poli') }
  catch (e) { if (e.response?.status === 422) errors.value = e.response.data.errors; else alert(e.response?.data?.message ?? 'Terjadi kesalahan') }
  finally { saving.value = false }
}
</script>
<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
:root{
  --bg:#0b1222;--surface:#152033;--surface2:#1a2a40;
  --border:#243450;--text:#e8f0f8;--text2:#8ba3bb;--text3:#4a6380;
  --shadow:0 1px 4px rgba(0,0,0,.5);--shadow-md:0 4px 16px rgba(0,0,0,.4);
}
.page{padding:1.75rem 2rem;background:var(--bg);min-height:100vh;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:var(--text);}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;gap:1rem}
.topbar-left{display:flex;align-items:center;gap:.75rem}
.back-btn{width:42px;height:42px;border-radius:10px;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);display:flex;align-items:center;justify-content:center;text-decoration:none;cursor:pointer;transition:all .18s}
.back-btn svg{width:18px;height:18px}.back-btn:hover{border-color:#14b8a6;color:#14b8a6}
.page-title{font-size:1.75rem;font-weight:800;letter-spacing:-.025em;margin:0}.page-sub{font-size:.9rem;color:var(--text2);margin:.15rem 0 0}
.icon-btn{width:38px;height:38px;border-radius:.55rem;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .18s;flex-shrink:0}
.icon-btn svg{width:16px;height:16px}.icon-btn:hover{border-color:#14b8a6;color:#14b8a6}
.loading-state{display:flex;align-items:center;gap:.75rem;padding:3rem;color:var(--text3)}
.spinner{width:20px;height:20px;border:2px solid var(--border);border-top-color:#14b8a6;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}
.card{background:var(--surface);border:1.5px solid var(--border);border-radius:14px;box-shadow:var(--shadow);overflow:hidden;max-width:800px;transition:background .2s}
.section{padding:2rem}.sec-title{font-size:.72rem;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.1em;margin:0 0 1.2rem}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem}
@media(max-width:600px){.grid2{grid-template-columns:1fr}}
.field{display:flex;flex-direction:column;gap:.3rem;margin-bottom:1rem}.field:last-child{margin-bottom:0}
.label{font-size:.875rem;font-weight:700;color:var(--text)}.req{color:#ef4444}
.input{padding:.7rem .95rem;border:1.5px solid var(--border);border-radius:10px;font-size:.9rem;font-family:inherit;background:var(--surface2);color:var(--text);outline:none;transition:all .18s;width:100%;box-sizing:border-box}
.input:focus{border-color:#14b8a6;background:var(--surface);box-shadow:0 0 0 3px rgba(20,184,166,.1)}.input::placeholder{color:var(--text3)}.input.err{border-color:#ef4444}
.textarea{resize:vertical;min-height:80px}.err-msg{font-size:.8rem;color:#ef4444;margin:0}
.footer{display:flex;align-items:center;justify-content:flex-end;gap:.65rem;padding:1.25rem 2rem;border-top:1px solid var(--border);background:var(--surface2)}
.btn-ghost{display:inline-flex;align-items:center;height:42px;padding:0 1.25rem;border-radius:10px;border:1.5px solid var(--border);background:transparent;color:var(--text2);font-size:.9rem;font-weight:600;font-family:inherit;text-decoration:none;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:var(--text2);color:var(--text)}
.btn-save{display:inline-flex;align-items:center;gap:.45rem;height:42px;padding:0 1.5rem;border-radius:10px;background:#0ea5e9;color:#fff;font-size:.9rem;font-weight:700;box-shadow:0 4px 14px rgba(14,165,233,.28);font-family:inherit;border:none;cursor:pointer;transition:all .18s}
.btn-save:hover:not(:disabled){background:#0284c7;transform:translateY(-1px)}.btn-save:disabled{opacity:.55;cursor:not-allowed}

.btn-spin{width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}

@keyframes spin{to{transform:rotate(360deg)}}
</style>
