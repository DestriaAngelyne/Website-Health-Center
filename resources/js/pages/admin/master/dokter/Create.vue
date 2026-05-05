<template>
  <div class="page">
    <div class="topbar">
      <div class="topbar-left">
        <router-link to="/admin/master/dokter" class="back-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg></router-link>
        <div><h1 class="page-title">Tambah Dokter</h1><p class="page-sub">Tambah dokter baru ke sistem</p></div>
      </div>

    </div>
    <div class="card">
      <form @submit.prevent="submit">
        <div class="section">
          <p class="sec-title">Data Dokter</p>
          <div class="grid2">
            <div class="field"><label class="label">NIP</label><input v-model="form.nip" type="text" class="input" placeholder="Nomor Induk Pegawai" /></div>
            <div class="field"><label class="label">STR Number</label><input v-model="form.str_number" type="text" class="input" placeholder="Nomor STR" /></div>
          </div>
          <div class="grid2">
            <div class="field"><label class="label">Nama Lengkap <span class="req">*</span></label><input v-model="form.nama" type="text" class="input" :class="{ err: errors.nama }" placeholder="dr. Nama, Sp.X" /><p v-if="errors.nama" class="err-msg">{{ errors.nama[0] }}</p></div>
            <div class="field"><label class="label">Spesialis</label><input v-model="form.spesialis" type="text" class="input" placeholder="Umum / Anak / Gigi" /></div>
          </div>
          <div class="grid2">
            <div class="field"><label class="label">Poli <span class="req">*</span></label><select v-model="form.poli_id" class="input" :class="{ err: errors.poli_id }"><option value="">— Pilih Poli —</option><option v-for="p in poliList" :key="p.id" :value="p.id">{{ p.nama_poli }}</option></select><p v-if="errors.poli_id" class="err-msg">{{ errors.poli_id[0] }}</p></div>
            <div class="field"><label class="label">No HP</label><input v-model="form.no_hp" type="text" class="input" placeholder="08xx-xxxx-xxxx" /></div>
          </div>
          <div class="field" style="max-width:200px"><label class="label">Status</label><select v-model="form.is_active" class="input"><option :value="true">Aktif</option><option :value="false">Nonaktif</option></select></div>
        </div>
        <div class="section">
          <p class="sec-title">Akun Login Dokter</p>
          <div class="grid2">
            <div class="field"><label class="label">Username <span class="req">*</span></label><input v-model="form.username" type="text" class="input" :class="{ err: errors.username }" placeholder="username" /><p v-if="errors.username" class="err-msg">{{ errors.username[0] }}</p></div>
            <div class="field"><label class="label">Email <span class="req">*</span></label><input v-model="form.email" type="email" class="input" :class="{ err: errors.email }" placeholder="email@domain.com" /><p v-if="errors.email" class="err-msg">{{ errors.email[0] }}</p></div>
          </div>
          <div class="field" style="max-width:360px"><label class="label">Password <span class="req">*</span></label><input v-model="form.password" type="password" class="input" :class="{ err: errors.password }" placeholder="Min. 8 karakter" /><p v-if="errors.password" class="err-msg">{{ errors.password[0] }}</p></div>
        </div>
        <div class="footer">
          <router-link to="/admin/master/dokter" class="btn-ghost">Batal</router-link>
          <button type="submit" class="btn-save" :disabled="saving"><span v-if="saving" class="btn-spin"></span>{{ saving ? 'Menyimpan...' : 'Simpan Dokter' }}</button>
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
const form = ref({ nip: '', nama: '', spesialis: '', poli_id: '', no_hp: '', str_number: '', is_active: true, username: '', email: '', password: '' })
const errors = ref({}), saving = ref(false), poliList = ref([])
onMounted(async () => { const r = await api.get('/admin/poli', { params: { per_page: 100 } }); poliList.value = r.data.data })
async function submit() {
  saving.value = true; errors.value = {}
  try { await api.post('/admin/dokter', form.value); router.push('/admin/master/dokter') }
  catch (e) { if (e.response?.status === 422) errors.value = e.response.data.errors; else alert(e.response?.data?.message ?? 'Terjadi kesalahan') }
  finally { saving.value = false }
}
</script>
<style scoped>@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");
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
.page-title{font-size:1.75rem;font-weight:800;letter-spacing:-.035em;margin:0}.page-sub{font-size:.9rem;color:var(--text2);margin:.3rem 0 0}
.icon-btn{width:42px;height:42px;border-radius:10px;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .18s;flex-shrink:0}
.icon-btn svg{width:17px;height:17px}.icon-btn:hover{border-color:#14b8a6;color:#14b8a6}
.card{background:var(--surface);border:1.5px solid var(--border);border-radius:14px;box-shadow:var(--shadow);overflow:hidden;max-width:900px;transition:background .2s}
.section{padding:2rem}
.section+.section{border-top:1px solid var(--border)}
.sec-title{font-size:.72rem;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.1em;margin:0 0 1.5rem}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem}
.grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1rem}
@media(max-width:600px){.grid2,.grid3{grid-template-columns:1fr}}
.field{display:flex;flex-direction:column;gap:.3rem;margin-bottom:1rem}.field:last-child{margin-bottom:0}
.label{font-size:.875rem;font-weight:700;color:var(--text)}.req{color:#ef4444}.hint{font-weight:400;color:var(--text3);font-size:.74rem}
.input{padding:.7rem .95rem;border:1.5px solid var(--border);border-radius:10px;font-size:.9rem;font-family:inherit;background:var(--surface2);color:var(--text);outline:none;transition:all .18s;width:100%;box-sizing:border-box}
.input:focus{border-color:#14b8a6;background:var(--surface);box-shadow:0 0 0 3px rgba(20,184,166,.1)}.input::placeholder{color:var(--text3)}.input.err{border-color:#ef4444}
.textarea{resize:vertical;min-height:80px}.err-msg{font-size:.8rem;color:#ef4444;margin:0;font-weight:500}
.loading-state{display:flex;align-items:center;gap:.75rem;padding:3rem;color:var(--text3)}
.spinner{width:20px;height:20px;border:2px solid var(--border);border-top-color:#14b8a6;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}
.footer{display:flex;align-items:center;justify-content:flex-end;gap:.65rem;padding:1.25rem 2rem;border-top:1px solid var(--border);background:var(--surface2)}
.btn-ghost{display:inline-flex;align-items:center;height:42px;padding:0 1.25rem;border-radius:10px;border:1.5px solid var(--border);background:transparent;color:var(--text2);font-size:.9rem;font-weight:600;font-family:inherit;text-decoration:none;cursor:pointer;transition:all .18s}
.btn-ghost:hover{border-color:var(--text2);color:var(--text)}
.btn-save{display:inline-flex;align-items:center;gap:.5rem;height:42px;padding:0 1.5rem;border-radius:10px;background:#0ea5e9;color:#fff;font-size:.9rem;font-weight:700;box-shadow:0 4px 14px rgba(14,165,233,.28);font-family:inherit;border:none;cursor:pointer;transition:all .18s}
.btn-save:hover:not(:disabled){background:#0284c7;transform:translateY(-1px)}.btn-save:disabled{opacity:.55;cursor:not-allowed}

.btn-spin{width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}

@keyframes spin{to{transform:rotate(360deg)}}</style>
