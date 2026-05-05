<template>
  <div class="page">
    <div class="topbar">
      <div class="topbar-left">
        <router-link to="/admin/users" class="back-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
        </router-link>
        <div>
          <h1 class="page-title">Tambah User</h1>
          <p class="page-sub">Tambah pengguna sistem baru</p>
        </div>
      </div>
    </div>

    <div class="card">
      <form @submit.prevent="submit">
        <div class="section">
          <p class="sec-title">Informasi Akun</p>
          <div class="grid2">
            <div class="field">
              <label class="label">Nama Lengkap <span class="req">*</span></label>
              <input v-model="form.name" type="text" class="input" :class="{ err: errors.name }" placeholder="Nama lengkap pengguna" />
              <p v-if="errors.name" class="err-msg">{{ errors.name[0] }}</p>
            </div>
            <div class="field">
              <label class="label">Role <span class="req">*</span></label>
              <select v-model="form.role" class="input" :class="{ err: errors.role }">
                <option value="">— Pilih Role —</option>
                <option value="admin">Admin</option>
                <option value="loket">Loket</option>
                <option value="perawat">Perawat</option>
                <option value="dokter">Dokter</option>
                <option value="pasien">Pasien</option>
              </select>
              <p v-if="errors.role" class="err-msg">{{ errors.role[0] }}</p>
            </div>
          </div>
          <div class="grid2">
            <div class="field">
              <label class="label">Username <span class="req">*</span></label>
              <input v-model="form.username" type="text" class="input" :class="{ err: errors.username }" placeholder="Username untuk login" />
              <p v-if="errors.username" class="err-msg">{{ errors.username[0] }}</p>
            </div>
            <div class="field">
              <label class="label">Email <span class="req">*</span></label>
              <input v-model="form.email" type="email" class="input" :class="{ err: errors.email }" placeholder="email@domain.com" />
              <p v-if="errors.email" class="err-msg">{{ errors.email[0] }}</p>
            </div>
          </div>
          <div class="grid2">
            <div class="field">
              <label class="label">Password <span class="req">*</span></label>
              <input v-model="form.password" type="password" class="input" :class="{ err: errors.password }" placeholder="Minimal 8 karakter" />
              <p v-if="errors.password" class="err-msg">{{ errors.password[0] }}</p>
            </div>
            <div class="field">
              <label class="label">Status</label>
              <select v-model="form.is_active" class="input">
                <option :value="true">Aktif</option>
                <option :value="false">Nonaktif</option>
              </select>
            </div>
          </div>
        </div>

        <div class="footer">
          <router-link to="/admin/users" class="btn-ghost">Batal</router-link>
          <button type="submit" class="btn-save" :disabled="saving">
            <span v-if="saving" class="btn-spin"></span>
            {{ saving ? 'Menyimpan...' : 'Simpan User' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
const router = useRouter()
const form   = ref({ name: '', username: '', email: '', password: '', role: '', is_active: true })
const errors = ref({})
const saving = ref(false)

async function submit() {
  saving.value = true; errors.value = {}
  try {
    await api.post('/admin/users', form.value)
    router.push('/admin/users')
  } catch (e) {
    if (e.response?.status === 422) errors.value = e.response.data.errors
    else alert(e.response?.data?.message ?? 'Terjadi kesalahan')
  } finally { saving.value = false }
}
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

:root {
  --bg:        #0b1222;
  --surface:   #152033;
  --surface2:  #1a2a40;
  --border:    #243450;
  --text:      #e8f0f8;
  --text2:     #8ba3bb;
  --text3:     #4a6380;
  --shadow:    0 1px 4px rgba(0,0,0,.5);
  --shadow-md: 0 4px 16px rgba(0,0,0,.4);
  --shadow-lg: 0 8px 28px rgba(0,0,0,.5);
}

*,*::before,*::after{box-sizing:border-box;}
.page{padding:2rem 2.25rem;background:var(--bg);min-height:100vh;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:var(--text);}

/* Topbar */
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;gap:1rem;}
.topbar-left{display:flex;align-items:center;gap:.875rem;}
.topbar-right{display:flex;align-items:center;gap:.625rem;flex-shrink:0;}
.page-title{font-size:1.75rem;font-weight:800;letter-spacing:-.035em;margin:0;line-height:1.15;}
.page-sub{font-size:.9rem;color:var(--text2);margin:.3rem 0 0;line-height:1.4;}

/* Buttons */
.back-btn{width:42px;height:42px;border-radius:10px;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);display:flex;align-items:center;justify-content:center;text-decoration:none;cursor:pointer;transition:all .18s;box-shadow:var(--shadow);}
.back-btn svg{width:18px;height:18px;}.back-btn:hover{border-color:#14b8a6;color:#14b8a6;background:rgba(20,184,166,.06);}

.icon-btn{width:42px;height:42px;border-radius:10px;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .18s;flex-shrink:0;box-shadow:var(--shadow);}
.icon-btn svg{width:17px;height:17px;}.icon-btn:hover{border-color:#14b8a6;color:#14b8a6;background:rgba(20,184,166,.06);}

.btn-primary{display:inline-flex;align-items:center;gap:.5rem;height:42px;padding:0 1.25rem;border-radius:10px;background:#0ea5e9;color:#fff;font-size:.9rem;font-weight:700;text-decoration:none;font-family:inherit;border:none;cursor:pointer;transition:all .2s;box-shadow:0 4px 14px rgba(14,165,233,.3);}
.btn-primary svg{width:15px;height:15px;}.btn-primary:hover{background:#0284c7;transform:translateY(-1px);box-shadow:0 6px 20px rgba(14,165,233,.4);}

.btn-ghost{display:inline-flex;align-items:center;height:42px;padding:0 1.25rem;border-radius:10px;border:1.5px solid var(--border);background:transparent;color:var(--text2);font-size:.9rem;font-weight:600;font-family:inherit;text-decoration:none;cursor:pointer;transition:all .18s;}
.btn-ghost:hover{border-color:var(--text2);color:var(--text);}

.btn-save{display:inline-flex;align-items:center;gap:.5rem;height:42px;padding:0 1.5rem;border-radius:10px;background:#0ea5e9;color:#fff;font-size:.9rem;font-weight:700;font-family:inherit;border:none;cursor:pointer;transition:all .2s;box-shadow:0 4px 14px rgba(14,165,233,.3);}
.btn-save:hover:not(:disabled){background:#0284c7;transform:translateY(-1px);}.btn-save:disabled{opacity:.55;cursor:not-allowed;}
.btn-spin{width:14px;height:14px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0;}

/* Filter bar */
.filter-bar{display:flex;align-items:center;gap:.75rem;margin-bottom:1.1rem;flex-wrap:wrap;}
.search-box{position:relative;flex:1;min-width:200px;max-width:380px;}
.search-box svg{position:absolute;left:.8rem;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--text3);}
.search-box input{width:100%;padding:.65rem .9rem .65rem 2.4rem;border:1.5px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text);font-size:.9rem;font-family:inherit;outline:none;transition:border-color .18s;box-shadow:var(--shadow);}
.search-box input:focus{border-color:#14b8a6;box-shadow:0 0 0 3px rgba(20,184,166,.12);}.search-box input::placeholder{color:var(--text3);}
.filter-select{height:42px;padding:0 1rem;border:1.5px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text);font-size:.9rem;font-family:inherit;outline:none;cursor:pointer;box-shadow:var(--shadow);}
.filter-select:focus{border-color:#14b8a6;}
.count-tag{font-size:.85rem;color:var(--text3);font-weight:600;}

/* Card */
.card{background:var(--surface);border:1.5px solid var(--border);border-radius:14px;box-shadow:var(--shadow);overflow:hidden;transition:background .25s;}

/* Form sections */
.section{padding:2rem;}.section+.section{border-top:1px solid var(--border);}
.sec-title{font-size:.72rem;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.1em;margin:0 0 1.5rem;}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:1.1rem;margin-bottom:1.1rem;}
@media(max-width:640px){.grid2{grid-template-columns:1fr;}}
.field{display:flex;flex-direction:column;gap:.4rem;margin-bottom:1.1rem;}.field:last-child{margin-bottom:0;}
.label{font-size:.875rem;font-weight:700;color:var(--text);}.req{color:#ef4444;}.hint{font-weight:400;color:var(--text3);font-size:.8rem;}
.input{padding:.7rem .95rem;border:1.5px solid var(--border);border-radius:10px;font-size:.9rem;font-family:inherit;background:var(--surface2);color:var(--text);outline:none;transition:all .18s;width:100%;}
.input:focus{border-color:#14b8a6;background:var(--surface);box-shadow:0 0 0 3px rgba(20,184,166,.12);}.input::placeholder{color:var(--text3);}.input.err{border-color:#ef4444;}
.err-msg{font-size:.8rem;color:#ef4444;margin:0;font-weight:500;}

/* Table */
.table-wrap{overflow-x:auto;}
.loading-row{display:flex;align-items:center;justify-content:center;gap:.875rem;padding:4rem;color:var(--text3);font-size:.95rem;}
.spinner{width:24px;height:24px;border:2.5px solid var(--border);border-top-color:#14b8a6;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0;}
.empty{display:flex;flex-direction:column;align-items:center;gap:.75rem;padding:4rem;color:var(--text3);}
.empty svg{opacity:.4;}.empty p{margin:0;font-size:.95rem;}

.tbl{width:100%;border-collapse:collapse;}
.tbl th{background:var(--surface2);padding:.95rem 1.25rem;text-align:left;font-size:.75rem;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;border-bottom:1px solid var(--border);white-space:nowrap;}
.tbl td{padding:.95rem 1.25rem;border-bottom:1px solid var(--border);font-size:.9rem;}
.tbl tbody tr:last-child td{border-bottom:none;}.tbl tbody tr{transition:background .12s;}.tbl tbody tr:hover td{background:var(--surface2);}
.center{text-align:center;}.bold{font-weight:700;}.muted{color:var(--text2);}.sm{font-size:.85rem;}

/* Badges */
.pill{padding:.28rem .75rem;border-radius:7px;font-size:.78rem;font-weight:700;}
.pill-green{background:rgba(34,197,94,.12);color:#16a34a;}.pill-red{background:rgba(239,68,68,.12);color:#dc2626;}
.av{width:34px;height:34px;border-radius:50%;color:#fff;font-size:.8rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.av-admin{background:#ef4444;}.av-loket{background:#3b82f6;}.av-dokter{background:#22c55e;}.av-perawat{background:#f59e0b;}.av-pasien{background:#a855f7;}
.code-tag{background:var(--surface2);border:1px solid var(--border);padding:.22rem .6rem;border-radius:7px;font-family:"SF Mono","Fira Code",monospace;font-size:.8rem;font-weight:600;}
.role-tag{padding:.28rem .75rem;border-radius:7px;font-size:.78rem;font-weight:700;text-transform:capitalize;}
.r-admin{background:rgba(239,68,68,.12);color:#dc2626;}.r-loket{background:rgba(59,130,246,.12);color:#2563eb;}
.r-dokter{background:rgba(34,197,94,.12);color:#16a34a;}.r-perawat{background:rgba(245,158,11,.12);color:#d97706;}.r-pasien{background:rgba(168,85,247,.12);color:#7c3aed;}

/* Action buttons */
.actions{display:flex;gap:.4rem;flex-wrap:wrap;}
.act{padding:.32rem .75rem;border-radius:7px;border:none;cursor:pointer;font-size:.8rem;font-weight:700;text-decoration:none;font-family:inherit;transition:all .15s;display:inline-flex;align-items:center;gap:.3rem;}
.act-yellow{background:rgba(245,158,11,.12);color:#d97706;}.act-yellow:hover{background:rgba(245,158,11,.22);}
.act-gray{background:var(--surface2);color:var(--text2);border:1px solid var(--border);}.act-gray:hover{border-color:var(--text2);}
.act-green{background:rgba(34,197,94,.12);color:#16a34a;}.act-green:hover{background:rgba(34,197,94,.22);}
.act-red{background:rgba(239,68,68,.12);color:#dc2626;}.act-red:hover{background:rgba(239,68,68,.22);}
.act-blue{background:rgba(59,130,246,.12);color:#2563eb;}.act-blue:hover{background:rgba(59,130,246,.22);}

/* Pagination */
.pager{display:flex;align-items:center;justify-content:center;gap:.4rem;padding:1.1rem;border-top:1px solid var(--border);}
.page-btn{min-width:36px;height:36px;padding:0 .7rem;border-radius:8px;border:1.5px solid var(--border);background:var(--surface);font-size:.85rem;font-weight:600;cursor:pointer;color:var(--text2);font-family:inherit;transition:all .15s;display:flex;align-items:center;justify-content:center;}
.page-btn:hover:not(:disabled){border-color:#14b8a6;color:#14b8a6;}.page-btn:disabled{opacity:.35;cursor:not-allowed;}
.page-btn.active{background:#0ea5e9;color:#fff;border-color:#0ea5e9;}

/* Footer form */
.footer{display:flex;align-items:center;justify-content:flex-end;gap:.75rem;padding:1.25rem 2rem;border-top:1px solid var(--border);background:var(--surface2);}

/* Modal */
.modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;z-index:1000;backdrop-filter:blur(4px);}
.modal{background:var(--surface);border:1.5px solid var(--border);border-radius:14px;width:100%;max-width:440px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.28);}
.modal-head{display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.5rem;border-bottom:1px solid var(--border);}
.modal-head h3{font-size:1rem;font-weight:700;margin:0;}
.modal-close{width:32px;height:32px;border-radius:8px;border:none;background:transparent;color:var(--text2);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;}
.modal-close:hover{background:var(--surface2);color:var(--text);}.modal-close svg{width:15px;height:15px;}
.modal-body{padding:1.5rem;}
.modal-foot{display:flex;align-items:center;justify-content:flex-end;gap:.75rem;padding:1.1rem 1.5rem;border-top:1px solid var(--border);background:var(--surface2);}
.modal-desc{font-size:.9rem;color:var(--text2);margin:0 0 1.25rem;line-height:1.6;}

/* Loading state standalone */
.loading-state{display:flex;align-items:center;gap:.875rem;padding:3rem;color:var(--text3);font-size:.95rem;}

@keyframes spin{to{transform:rotate(360deg);}}
</style>
