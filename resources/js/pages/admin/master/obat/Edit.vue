<template>
  <div class="page">
    <div class="page-header">
      <div>
        <div class="breadcrumb">
          <router-link to="/admin/master/obat" class="bc-link">Master Obat</router-link>
          <span class="bc-sep">›</span>
          <span>Edit Obat</span>
        </div>
        <h1 class="page-title">Edit Obat</h1>
        <p v-if="form.kode_obat" class="page-sub">{{ form.kode_obat }}</p>
      </div>
    </div>

    <div v-if="loadingData" class="loading-state">
      <div class="spinner"></div>
      <span>Memuat data obat...</span>
    </div>

    <div v-else class="form-card">
      <div v-if="alertMsg" class="alert" :class="alertType">
        {{ alertMsg }}
        <button @click="alertMsg = ''" class="alert-close">×</button>
      </div>

      <div class="form-grid">
        <div class="form-group full">
          <label class="form-label required">Nama Obat</label>
          <input v-model="form.nama_obat" type="text" class="form-input" :class="{ error: errors.nama_obat }" placeholder="Contoh: Paracetamol 500mg"/>
          <span v-if="errors.nama_obat" class="err-msg">{{ errors.nama_obat[0] }}</span>
        </div>

        <div class="form-group">
          <label class="form-label">Kategori</label>
          <input v-model="form.kategori" type="text" class="form-input" list="kategori-list" placeholder="Tablet, Sirup, Kapsul..."/>
          <datalist id="kategori-list">
            <option v-for="k in kategoriSuggest" :key="k" :value="k"/>
          </datalist>
        </div>

        <div class="form-group">
          <label class="form-label required">Satuan</label>
          <select v-model="form.satuan" class="form-input" :class="{ error: errors.satuan }">
            <option value="">-- Pilih Satuan --</option>
            <option v-for="s in satuanList" :key="s" :value="s">{{ s }}</option>
          </select>
          <span v-if="errors.satuan" class="err-msg">{{ errors.satuan[0] }}</span>
        </div>

        <div class="form-group">
          <label class="form-label required">Stok</label>
          <input v-model.number="form.stok" type="number" min="0" class="form-input" :class="{ error: errors.stok }"/>
          <span class="field-hint" :class="{ kritis: form.stok <= form.stok_minimum && form.stok_minimum > 0 }">
            {{ form.stok <= form.stok_minimum && form.stok_minimum > 0 ? '⚠ Stok kritis!' : `Minimum: ${form.stok_minimum}` }}
          </span>
          <span v-if="errors.stok" class="err-msg">{{ errors.stok[0] }}</span>
        </div>

        <div class="form-group">
          <label class="form-label required">Stok Minimum</label>
          <input v-model.number="form.stok_minimum" type="number" min="0" class="form-input" :class="{ error: errors.stok_minimum }"/>
          <span v-if="errors.stok_minimum" class="err-msg">{{ errors.stok_minimum[0] }}</span>
        </div>

        <div class="form-group">
          <label class="form-label required">Harga (Rp)</label>
          <div class="input-prefix">
            <span class="prefix">Rp</span>
            <input v-model.number="form.harga" type="number" min="0" class="form-input prefixed" :class="{ error: errors.harga }"/>
          </div>
          <span class="harga-preview" v-if="form.harga > 0">{{ formatHarga(form.harga) }}</span>
          <span v-if="errors.harga" class="err-msg">{{ errors.harga[0] }}</span>
        </div>

        <div class="form-group">
          <label class="form-label">Status</label>
          <div class="toggle-group">
            <button type="button" class="toggle-opt" :class="{ active: form.is_active }" @click="form.is_active = true">Aktif</button>
            <button type="button" class="toggle-opt" :class="{ active: !form.is_active }" @click="form.is_active = false">Nonaktif</button>
          </div>
        </div>

        <div class="form-group full">
          <label class="form-label">Deskripsi / Keterangan</label>
          <textarea v-model="form.deskripsi" class="form-input" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
        </div>
      </div>

      <div class="form-actions">
        <router-link to="/admin/master/obat" class="btn-cancel">Batal</router-link>
        <button @click="submit" class="btn-submit" :disabled="loading">
          <svg v-if="!loading" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px"><path d="M3 10l4 4 10-10"/></svg>
          <span class="spinner-sm" v-else></span>
          {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

const router      = useRouter()
const route       = useRoute()
const id          = route.params.id
const loading     = ref(false)
const loadingData = ref(true)
const alertMsg    = ref('')
const alertType   = ref('error')
const errors      = ref({})

const form = ref({
  kode_obat: '', nama_obat: '', kategori: '', satuan: '',
  stok: 0, stok_minimum: 10, harga: 0, deskripsi: '', is_active: true,
})

const satuanList      = ['tablet','kapsul','sirup','sachet','ampul','vial','tube','botol','pcs','ml','mg']
const kategoriSuggest = ['Analgesik','Antibiotik','Antasida','Antihistamin','Vitamin','Antidiabetik','Antihipertensi','Antiseptik','Kortikosteroid','Lainnya']

async function loadObat() {
  loadingData.value = true
  try {
    const res = await api.get(`/admin/master/obat/${id}`)
    const d = res.data.data
    form.value = { kode_obat: d.kode_obat, nama_obat: d.nama_obat, kategori: d.kategori ?? '', satuan: d.satuan, stok: d.stok, stok_minimum: d.stok_minimum, harga: parseFloat(d.harga), deskripsi: d.deskripsi ?? '', is_active: d.is_active }
  } catch { alertMsg.value = 'Gagal memuat data obat.'; alertType.value = 'error' }
  finally { loadingData.value = false }
}

async function submit() {
  errors.value = {}; alertMsg.value = ''; loading.value = true
  try {
    await api.put(`/admin/master/obat/${id}`, form.value)
    router.push({ path: '/admin/master/obat', query: { success: 'Obat berhasil diperbarui.' } })
  } catch (e) {
    if (e.response?.status === 422) { errors.value = e.response.data.errors ?? {}; alertMsg.value = 'Periksa kembali isian form.'; alertType.value = 'error' }
    else { alertMsg.value = 'Gagal menyimpan perubahan. Coba lagi.'; alertType.value = 'error' }
  } finally { loading.value = false }
}

function formatHarga(h) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(h)
}

onMounted(() => loadObat())
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap");
:root,[data-theme="light"]{--bg:#f1f5f9;--surface:#fff;--surface2:#f8fafc;--border:#e2e8f0;--text:#0f172a;--text2:#475569;--text3:#94a3b8}
[data-theme="dark"]{--bg:#0f172a;--surface:#1e293b;--surface2:#162032;--border:#334155;--text:#f1f5f9;--text2:#94a3b8;--text3:#475569}
*{box-sizing:border-box}
.page{padding:1.75rem 2rem;background:var(--bg);min-height:100vh;font-family:"DM Sans",system-ui,sans-serif;color:var(--text)}
.page-header{margin-bottom:1.5rem}
.page-title{font-size:1.5rem;font-weight:800;margin:.25rem 0 0;letter-spacing:-.03em;color:var(--text)}
.page-sub{font-size:.85rem;color:#818cf8;font-family:monospace;font-weight:700;margin:4px 0 0}
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:.85rem;color:var(--text2);margin-bottom:4px;font-weight:500}
.bc-link{color:#818cf8;text-decoration:none;font-weight:600}
.bc-link:hover{text-decoration:underline}
.bc-sep{color:var(--text3)}
.loading-state{display:flex;align-items:center;gap:.75rem;padding:3rem;color:var(--text3);font-size:.9rem;font-weight:500}
.spinner{width:20px;height:20px;border:2.5px solid var(--border);border-top-color:#6366f1;border-radius:50%;animation:spin .7s linear infinite}
.form-card{background:var(--surface);border:1.5px solid var(--border);border-radius:.875rem;padding:2rem;max-width:760px}
.alert{display:flex;align-items:center;gap:8px;padding:.75rem 1rem;border-radius:.6rem;margin-bottom:1.25rem;font-size:.88rem;font-weight:500}
.alert.error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#f87171}
.alert.success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#4ade80}
.alert-close{margin-left:auto;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;color:inherit}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem 1.5rem}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group.full{grid-column:span 2}
.form-label{font-size:.85rem;font-weight:700;color:var(--text)}
.form-label.required::after{content:' *';color:#f87171}
.form-input{padding:.6rem .9rem;border:1.5px solid var(--border);border-radius:.55rem;font-size:.88rem;font-weight:500;font-family:inherit;background:var(--surface2);color:var(--text);outline:none;transition:border-color .18s;width:100%;box-sizing:border-box}
.form-input:focus{border-color:#6366f1;background:var(--surface)}
.form-input.error{border-color:#ef4444}
.form-input::placeholder{color:var(--text3)}
.err-msg{font-size:.77rem;color:#f87171;font-weight:500}
.field-hint{font-size:.77rem;color:var(--text3);font-weight:500}
.field-hint.kritis{color:#f87171;font-weight:700}
.harga-preview{font-size:.82rem;color:#34d399;font-weight:700}
.input-prefix{position:relative;display:flex;align-items:center}
.prefix{position:absolute;left:10px;font-size:.85rem;color:var(--text3);font-weight:700;pointer-events:none}
.form-input.prefixed{padding-left:2.2rem}
.toggle-group{display:flex;border:1.5px solid var(--border);border-radius:.55rem;overflow:hidden}
.toggle-opt{flex:1;padding:.55rem;font-size:.88rem;font-weight:700;border:none;background:var(--surface2);color:var(--text2);cursor:pointer;font-family:inherit;transition:all .15s}
.toggle-opt.active{background:#6366f1;color:white}
.form-actions{display:flex;gap:.75rem;justify-content:flex-end;margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border)}
.btn-cancel{padding:.6rem 1.4rem;border-radius:.55rem;border:1.5px solid var(--border);background:var(--surface);color:var(--text2);font-size:.88rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;font-family:inherit;cursor:pointer;transition:all .18s}
.btn-cancel:hover{border-color:var(--text3)}
.btn-submit{display:inline-flex;align-items:center;gap:6px;padding:.6rem 1.4rem;border-radius:.55rem;border:none;background:#6366f1;color:white;font-size:.88rem;font-weight:700;cursor:pointer;font-family:inherit;transition:all .18s}
.btn-submit:hover:not(:disabled){background:#4f46e5}
.btn-submit:disabled{opacity:.5;cursor:not-allowed}
.spinner-sm{width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:white;border-radius:50%;animation:spin .7s linear infinite;flex-shrink:0}
@keyframes spin{to{transform:rotate(360deg)}}
@media(max-width:600px){.form-grid{grid-template-columns:1fr}.form-group.full{grid-column:span 1}}
</style>
