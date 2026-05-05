<template>
  <div class="pendaftaran-page">

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="nav-container">
        <div class="nav-brand">
          <div class="brand-icon">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:34px;height:34px">
              <circle cx="50" cy="50" r="50" fill="#1a6aad"/>
              <circle cx="50" cy="50" r="44" fill="none" stroke="white" stroke-width="1.5"/>
              <defs>
                <path id="pp-top" d="M 15,50 A 35,35 0 0,1 85,50"/>
                <path id="pp-bot" d="M 23,63 A 29,29 0 0,0 77,63"/>
              </defs>
              <text font-family="Arial,sans-serif" font-weight="800" font-size="9.8" fill="white" letter-spacing="2">
                <textPath href="#pp-top" startOffset="50%" text-anchor="middle">GRAHA MEDIKA</textPath>
              </text>
              <text font-family="Arial,sans-serif" font-weight="600" font-size="8.5" fill="white" letter-spacing="1.2">
                <textPath href="#pp-bot" startOffset="50%" text-anchor="middle">Hospital</textPath>
              </text>
              <polyline points="29,47 50,29 71,47" stroke="white" stroke-width="3" stroke-linejoin="round" fill="none"/>
              <rect x="58" y="28" width="5" height="10" fill="white" rx="0.5"/>
              <line x1="32" y1="47" x2="32" y2="68" stroke="white" stroke-width="3" stroke-linecap="round"/>
              <line x1="68" y1="47" x2="68" y2="68" stroke="white" stroke-width="3" stroke-linecap="round"/>
              <line x1="30" y1="68" x2="70" y2="68" stroke="white" stroke-width="3" stroke-linecap="round"/>
              <rect x="37" y="36" width="5.5" height="5.5" fill="none" stroke="white" stroke-width="1.4" rx="0.5"/>
              <line x1="39.75" y1="36" x2="39.75" y2="41.5" stroke="white" stroke-width="1"/>
              <line x1="37" y1="38.75" x2="42.5" y2="38.75" stroke="white" stroke-width="1"/>
              <path d="M50,61 C46,57 38,52 38,47 C38,43.7 40.7,41 44,41 C46.2,41 48.2,42.2 50,44.5 C51.8,42.2 53.8,41 56,41 C59.3,41 62,43.7 62,47 C62,52 54,57 50,61 Z" fill="none" stroke="white" stroke-width="2" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="brand-text">
            <div class="brand-name">Puskesmas</div>
            <div class="brand-sub">Graha Medika Prima</div>
          </div>
        </div>

        <div class="nav-actions">
          <template v-if="!authStore.isLoggedIn">
            <a href="/login" class="btn-outline">Masuk</a>
            <a href="/register" class="btn-primary-nav">Daftar Akun</a>
          </template>
          <template v-else>
            <div class="user-menu" @click.stop="userDropdownOpen = !userDropdownOpen">
              <div class="user-avatar">
                <img v-if="authStore.fotoUrl" :src="authStore.fotoUrl"
                  style="width:100%;height:100%;object-fit:cover;border-radius:7px;display:block;" />
                <template v-else>{{ userInitial }}</template>
              </div>
              <div class="user-info">
                <span class="user-name">{{ authStore.user?.name }}</span>
                <span class="user-role">{{ userRoleLabel }}</span>
              </div>
              <svg class="chevron" :class="{ open: userDropdownOpen }"
                viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 6l4 4 4-4"/>
              </svg>
              <div class="user-dropdown" v-if="userDropdownOpen">
                <router-link :to="dashboardRoute" class="dropdown-item" @click="userDropdownOpen = false">
                  <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 9l7-6 7 6v9a1 1 0 01-1 1H4a1 1 0 01-1-1V9z"/>
                  </svg>
                  Dashboard
                </router-link>
                <div class="dropdown-divider"></div>
                <a @click.prevent="handleLogout" class="dropdown-item danger">
                  <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M13 15l4-5-4-5M17 10H7M7 3H4a1 1 0 00-1 1v12a1 1 0 001 1h3"/>
                  </svg>
                  Keluar
                </a>
              </div>
            </div>
          </template>
        </div>
      </div>
    </nav>

    <div class="page-wrapper">
      <div class="page-content">

        <!-- LOGIN WARNING -->
        <div v-if="!authStore.isLoggedIn" class="login-warning" style="margin-bottom:20px">
          <svg viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;flex-shrink:0">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <span>Anda belum login. Login terlebih dahulu untuk mendaftar antrian.</span>
          <a href="/login" class="warning-link">Login Sekarang →</a>
        </div>

        <!-- ERROR ALERT -->
        <div v-if="errorMsg" class="error-alert">
          <svg viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;flex-shrink:0">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <span>{{ errorMsg }}</span>
          <button @click="errorMsg = ''" style="background:none;border:none;cursor:pointer;color:inherit;margin-left:auto;font-size:18px;line-height:1">×</button>
        </div>

        <!-- STEP 1 -->
        <div v-if="step === 1" class="step-content">
          <div class="step-header">
            <h2 class="step-title">Pilih Poliklinik</h2>
            <p class="step-desc">Pilih poli sesuai kebutuhan pemeriksaan Anda</p>
          </div>

          <div v-if="loadingPoli" class="loading-center"><span class="spinner spinner-dark"></span></div>

          <template v-else>
            <div class="kategori-block" v-if="poliUmum.length > 0">
              <div class="kategori-header">
                <div class="kategori-icon-wrap kategori-umum">
                  <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" style="width:14px;height:14px"><circle cx="10" cy="10" r="8"/><path d="M10 6v4M10 13v.5"/></svg>
                </div>
                <div>
                  <div class="kategori-title">Poli Umum &amp; Dasar</div>
                  <div class="kategori-sub">Pelayanan kesehatan primer dan dasar</div>
                </div>
              </div>
              <div class="poli-grid">
                <div v-for="poli in poliUmum" :key="poli.id"
                  class="poli-card"
                  :class="{ selected: form.poli_id === poli.id }"
                  @click="selectPoli(poli)">
                  <div class="poli-icon" :style="getPoliUmumStyle(poli.nama).iconStyle">{{ poliInitial(poli.nama) }}</div>
                  <div class="poli-name">{{ poli.nama }}</div>
                  <div class="poli-desc">{{ poli.keterangan || 'Pelayanan poliklinik' }}</div>
                  <div class="poli-tag" :style="getPoliUmumStyle(poli.nama).iconStyle">{{ getPoliUmumStyle(poli.nama).tag }}</div>
                  <div v-if="form.poli_id === poli.id" class="poli-check">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8l3 3 7-7"/></svg>
                  </div>
                </div>
              </div>
            </div>

            <div class="kategori-block">
              <div class="kategori-header">
                <div class="kategori-icon-wrap kategori-spesialis">
                  <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" style="width:14px;height:14px"><path d="M9 3H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V9M15 3l2 2-7 7H8v-2l7-7z"/></svg>
                </div>
                <div>
                  <div class="kategori-title">Poli Spesialis &amp; Sub-Spesialis</div>
                  <div class="kategori-sub">Penanganan penyakit oleh dokter spesialis</div>
                </div>
              </div>
              <div class="poli-grid">
                <div v-for="sp in SPESIALIS_STATIS" :key="sp.key"
                  class="poli-card"
                  :class="{ selected: form.poli_id !== null && (form.poli_id === spGetId(sp) || (spGetId(sp) === null && form.poli_id === -(SPESIALIS_STATIS.findIndex(s => s.key === sp.key)+1))) }"
                  @click="pilihPoliSpesialis(sp)">
                  <div class="poli-icon" :style="sp.iconStyle">{{ sp.initial }}</div>
                  <div class="poli-name">{{ sp.nama_poli }}</div>
                  <div class="poli-desc">{{ sp.keterangan }}</div>
                  <div class="poli-tag">{{ sp.tag }}</div>
                  <div v-if="form.poli_id !== null && form.poli_id === spGetId(sp)" class="poli-check">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8l3 3 7-7"/></svg>
                  </div>
                </div>
              </div>
            </div>
          </template>

          <!-- STEP 1 NAV -->
          <div class="step-nav">
            <a href="/" class="btn-back">
              <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px">
                <path d="M2 6l6-4 6 4v8a1 1 0 01-1 1H3a1 1 0 01-1-1V6z"/>
              </svg>
              Kembali ke Beranda
            </a>
            <button class="btn-next" @click="nextToStep2" :disabled="!form.poli_id || form.poli_id === 0">
              Lanjut — Pilih Jadwal
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12M10 4l6 6-6 6"/></svg>
            </button>
          </div>
        </div>

        <!-- STEP 2 -->
        <div v-if="step === 2" class="step-content">
          <div class="step-header">
            <h2 class="step-title">Pilih Dokter &amp; Jadwal</h2>
            <p class="step-desc">Pilih dokter dan tanggal kunjungan</p>
          </div>

          <div class="selected-poli-badge">
            <span>{{ selectedPoli?.nama ?? selectedPoli?.nama_poli }}</span>
            <button @click="resetStep1">Ganti Poli</button>
          </div>

          <div class="form-group">
            <label class="form-label">Pilih Dokter</label>
            <div v-if="loadingDokter" class="loading-center" style="padding:20px 0"><span class="spinner spinner-dark"></span></div>
            <div v-else-if="dokterList.length === 0" class="empty-inline">
              <template v-if="form.poli_id < 0">Poli spesialis ini belum tersedia di puskesmas ini.</template>
              <template v-else>Tidak ada dokter tersedia untuk poli ini.</template>
            </div>
            <div v-else class="dokter-list">
              <div v-for="d in dokterList" :key="d.id"
                class="dokter-card" :class="{ selected: form.dokter_id === d.id }"
                @click="pilihDokter(d)">
                <div class="dokter-avatar">{{ (d.nama_dokter ?? d.nama)?.charAt(0)?.toUpperCase() }}</div>
                <div class="dokter-info">
                  <div class="dokter-name">{{ d.nama_dokter ?? d.nama }}</div>
                  <div class="dokter-spesialis">{{ d.spesialis || (selectedPoli?.nama ?? selectedPoli?.nama_poli) }}</div>
                </div>
                <div v-if="form.dokter_id === d.id" class="dokter-check">
                  <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8l3 3 7-7"/></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Jenis Jaminan</label>
            <div class="jaminan-list">
              <div class="jaminan-card" :class="{ selected: form.jaminan === 'BPJS' }" @click="form.jaminan = 'BPJS'">
                <div class="jaminan-icon-wrap" :class="{ selected: form.jaminan === 'BPJS' }">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="jaminan-name">BPJS Kesehatan</div>
              </div>
              <div class="jaminan-card" :class="{ selected: form.jaminan === 'Umum' }" @click="form.jaminan = 'Umum'">
                <div class="jaminan-icon-wrap" :class="{ selected: form.jaminan === 'Umum' }">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                </div>
                <div class="jaminan-name">Umum</div>
              </div>
            </div>
          </div>

          <!-- TANGGAL PICKER — 5 hari ke depan mulai hari ini -->
          <div class="form-group" v-if="form.dokter_id">
            <label class="form-label">Pilih Tanggal</label>
            <div class="tanggal-grid">
              <div v-for="tgl in limahariKedepan" :key="tgl.tanggal"
                class="tanggal-card"
                :class="{ selected: form.tanggal_dipilih === tgl.tanggal }"
                @click="pilihTanggal(tgl.tanggal)">
                <div class="tanggal-hari">{{ tgl.hari }}</div>
                <div class="tanggal-angka">{{ tgl.tgl }}</div>
                <div class="tanggal-bulan">{{ tgl.bulan_tahun }}</div>
              </div>
            </div>
          </div>

          <div v-else class="sesi-placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="width:32px;height:32px"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            <span>Pilih dokter terlebih dahulu untuk melihat jadwal</span>
          </div>

          <div class="step-nav">
            <button class="btn-back" @click="step = 1">
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 10H4M10 16l-6-6 6-6"/></svg>
              Kembali
            </button>
            <button class="btn-next" @click="nextToStep3" :disabled="!form.dokter_id || !form.tanggal_dipilih || !form.jaminan">
              Lanjut — Data Diri
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12M10 4l6 6-6 6"/></svg>
            </button>
          </div>
        </div>

        <!-- STEP 3 -->
        <div v-if="step === 3" class="step-content">
          <h2 class="step-title">Data Diri Pasien</h2>
          <div class="form-card">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">Nama Lengkap</label>
                <input v-model="form.nama" type="text" class="form-input" placeholder="Sesuai KTP"/>
              </div>
              <div class="form-group">
                <label class="form-label required">NIK</label>
                <input v-model="form.nik" type="text" class="form-input" placeholder="16 digit NIK" maxlength="16" @input="form.nik = form.nik.replace(/\D/g, '')"/>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">Tanggal Lahir</label>
                <input v-model="form.tgl_lahir" type="date" class="form-input" :max="today"/>
              </div>
              <div class="form-group">
                <label class="form-label required">Jenis Kelamin</label>
                <div class="radio-group">
                  <label class="radio-card" :class="{ selected: form.jk === 'L' }">
                    <input type="radio" v-model="form.jk" value="L" style="display:none"/>
                    Laki-laki
                  </label>
                  <label class="radio-card" :class="{ selected: form.jk === 'P' }">
                    <input type="radio" v-model="form.jk" value="P" style="display:none"/>
                    Perempuan
                  </label>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">No. Telepon</label>
                <input v-model="form.telepon" type="text" class="form-input" placeholder="08xxxxxxxxxx" @input="form.telepon = form.telepon.replace(/\D/g, '')"/>
              </div>
              <div class="form-group" v-if="form.jaminan === 'BPJS'">
                <label class="form-label required">No. BPJS</label>
                <input v-model="form.no_bpjs" type="text" class="form-input" placeholder="Nomor BPJS" @input="form.no_bpjs = form.no_bpjs.replace(/\D/g, '')"/>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Keluhan Utama</label>
              <textarea v-model="form.keluhan" class="form-input" rows="3" placeholder="Tuliskan keluhan Anda (opsional)"></textarea>
            </div>
          </div>
          <div class="step-nav">
            <button class="btn-back" @click="step = 2">
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 10H4M10 16l-6-6 6-6"/></svg>
              Kembali
            </button>
            <button class="btn-next" @click="step = 4" :disabled="!form.nama || !form.nik || !form.tgl_lahir || !form.jk || !form.telepon">
              Lanjut — Konfirmasi
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12M10 4l6 6-6 6"/></svg>
            </button>
          </div>
        </div>

        <!-- STEP 4 -->
        <div v-if="step === 4 && !submitted" class="step-content">
          <h2 class="step-title">Konfirmasi Pendaftaran</h2>
          <div class="konfirmasi-card">
            <div class="konfirmasi-header">
              <div class="konfirmasi-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="1.5">
                  <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div>
                <div class="konfirmasi-title">Ringkasan Pendaftaran</div>
                <div class="konfirmasi-sub">Periksa kembali data Anda sebelum mendaftar</div>
              </div>
            </div>
            <div class="konfirmasi-separator"></div>
            <div class="konfirmasi-grid">
              <div class="konfirmasi-item"><span class="k-label">Poliklinik</span><span class="k-value">{{ selectedPoli?.nama_poli ?? selectedPoli?.nama }}</span></div>
              <div class="konfirmasi-item"><span class="k-label">Dokter</span><span class="k-value">{{ selectedDokter?.nama_dokter ?? selectedDokter?.nama }}</span></div>
              <div class="konfirmasi-item"><span class="k-label">Jaminan</span><span class="k-value"><span class="badge-jaminan">{{ form.jaminan }}</span></span></div>
              <div class="konfirmasi-item"><span class="k-label">Tanggal</span><span class="k-value">{{ tanggalDipilihFmt }}</span></div>
              <div class="konfirmasi-separator" style="grid-column:span 2"></div>
              <div class="konfirmasi-item"><span class="k-label">Nama</span><span class="k-value">{{ form.nama }}</span></div>
              <div class="konfirmasi-item"><span class="k-label">NIK</span><span class="k-value">{{ form.nik }}</span></div>
              <div class="konfirmasi-item"><span class="k-label">Telepon</span><span class="k-value">{{ form.telepon }}</span></div>
              <div v-if="form.no_bpjs" class="konfirmasi-item"><span class="k-label">No. BPJS</span><span class="k-value">{{ form.no_bpjs }}</span></div>
              <div v-if="form.keluhan" class="konfirmasi-item full"><span class="k-label">Keluhan</span><span class="k-value">{{ form.keluhan }}</span></div>
            </div>
          </div>
          <div class="step-nav">
            <button class="btn-back" @click="step = 3">
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 10H4M10 16l-6-6 6-6"/></svg>
              Kembali
            </button>
            <button class="btn-submit" @click="submit" :disabled="loading">
              <span v-if="!loading">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;display:inline;vertical-align:middle;margin-right:6px"><path d="M3 10l4 4 10-10"/></svg>
                Daftar Sekarang
              </span>
              <span v-else class="loading-row"><span class="spinner"></span> Mendaftarkan...</span>
            </button>
          </div>
        </div>

        <!-- SUKSES -->
        <div v-if="submitted" class="step-content sukses">
          <div class="sukses-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h2 class="sukses-title">Pendaftaran Berhasil!</h2>
          <p class="sukses-desc">Anda telah terdaftar di <strong>{{ selectedPoli?.nama_poli ?? selectedPoli?.nama }}</strong>. Silakan datang sesuai jadwal.</p>

          <div class="tiket-card">
            <div class="tiket-header">
              <div class="tiket-poli">{{ selectedPoli?.nama_poli ?? selectedPoli?.nama }}</div>
              <div class="tiket-status">Terdaftar</div>
            </div>

            <div class="tiket-nomor-label">Nomor Antrian Anda</div>
            <div class="tiket-nomor">{{ nomorAntrian }}</div>

            <!-- QR CODE -->
            <div class="tiket-qr-wrap">
              <div ref="qrContainer" class="tiket-qr-box"></div>
              <div class="tiket-qr-label">Scan untuk cek status antrian</div>
            </div>

            <div class="tiket-info">
              <div class="tiket-row"><span>Dokter</span><span>{{ selectedDokter?.nama_dokter ?? selectedDokter?.nama }}</span></div>
              <div class="tiket-row"><span>Tanggal</span><span>{{ tanggalDipilihFmt }}</span></div>
              <div class="tiket-row"><span>Jaminan</span><span>{{ form.jaminan }}</span></div>
              <div class="tiket-row"><span>Nama</span><span>{{ form.nama }}</span></div>
            </div>
            <div class="tiket-footer">Tunjukkan nomor ini saat tiba di puskesmas</div>
          </div>

          <div class="sukses-actions">
            <button class="btn-download" @click="downloadTiket" :disabled="loadingPdf">
              <span v-if="!loadingPdf">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;display:inline;vertical-align:middle;margin-right:6px"><path d="M10 3v10M6 9l4 4 4-4M3 17h14"/></svg>
                Unduh Struk PDF
              </span>
              <span v-else class="loading-row"><span class="spinner spinner-dark"></span> Membuat PDF...</span>
            </button>
            <router-link v-if="authStore.isLoggedIn && authStore.user?.role === 'pasien'" to="/pasien/antrian/status" class="btn-next-link">
              Lihat Status Antrian
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12M10 4l6 6-6 6"/></svg>
            </router-link>
            <a v-else-if="!authStore.isLoggedIn" href="/login" class="btn-next-link">
              Masuk untuk Pantau Antrian
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12M10 4l6 6-6 6"/></svg>
            </a>
            <router-link v-else :to="dashboardRoute" class="btn-next-link">
              Ke Dashboard
              <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12M10 4l6 6-6 6"/></svg>
            </router-link>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'

const router    = useRouter()
const authStore = useAuthStore()

const userDropdownOpen = ref(false)
const handleClickOutside = (e: MouseEvent) => {
  if (!(e.target as HTMLElement).closest('.user-menu')) userDropdownOpen.value = false
}

onMounted(async () => {
  document.addEventListener('click', handleClickOutside)
  if (authStore.token && !authStore.user) await authStore.fetchUser()
  await loadPoli()
  if (authStore.isLoggedIn && authStore.user?.role === 'pasien') {
    try {
      const res = await api.get('/pasien/profil')
      const p = res.data.data
      form.value.nama      = p.nama_lengkap ?? p.nama ?? ''
      form.value.nik       = p.nik ?? ''
      form.value.tgl_lahir = p.tanggal_lahir ?? ''
      form.value.jk        = p.jenis_kelamin ?? ''
      form.value.telepon   = p.no_telepon ?? p.no_hp ?? ''
      form.value.no_bpjs   = p.no_bpjs ?? ''
    } catch { /* silent */ }
  }
})
onUnmounted(() => document.removeEventListener('click', handleClickOutside))

const userInitial   = computed(() => authStore.user?.name?.charAt(0).toUpperCase() ?? 'U')
const userRoleLabel = computed(() => {
  const map: Record<string, string> = { admin: 'Administrator', loket: 'Petugas Loket', perawat: 'Perawat', dokter: 'Dokter', pasien: 'Pasien' }
  return map[authStore.user?.role ?? ''] ?? '-'
})
const dashboardRoute = computed(() => {
  const map: Record<string, string> = { admin: '/admin/dashboard', loket: '/loket/dashboard', perawat: '/perawat/dashboard', dokter: '/dokter/dashboard', pasien: '/pasien/dashboard' }
  return map[authStore.user?.role ?? ''] ?? '/'
})
const handleLogout = async () => {
  userDropdownOpen.value = false
  await authStore.logout()
}

// ── State ──────────────────────────────────────────────────────────────────
const step         = ref(1)
const loading      = ref(false)
const loadingPdf   = ref(false)
const submitted    = ref(false)
const errorMsg     = ref('')
const nomorAntrian = ref('')
const qrContainer  = ref<HTMLElement | null>(null)
const today        = new Date().toISOString().split('T')[0]

const loadingPoli   = ref(false)
const loadingDokter = ref(false)
const loadingSesi   = ref(false)
const poliList   = ref<any[]>([])
const dokterList = ref<any[]>([])
const sesiList   = ref<any[]>([])

const form = ref({
  poli_id         : null as number | null,
  dokter_id       : null as number | null,
  sesi_id         : null as number | null,
  tanggal_dipilih : '' as string,
  jaminan         : '',
  nama            : '',
  nik             : '',
  tgl_lahir       : '',
  jk              : '',
  telepon         : '',
  no_bpjs         : '',
  keluhan         : '',
})

// ── Spesialis statis ───────────────────────────────────────────────────────
const SPESIALIS_STATIS = [
  { key:'mata',         initial:'M',  nama_poli:'Poli Mata',            tag:'Oftalmologi',         keterangan:'Kesehatan mata & gangguan penglihatan',        iconStyle:'background:#dbeafe;color:#1d4ed8;border-color:#bfdbfe' },
  { key:'tht',          initial:'T',  nama_poli:'Poli THT',             tag:'THT-KL',              keterangan:'Telinga, hidung & tenggorokan',                iconStyle:'background:#e0f2fe;color:#0369a1;border-color:#bae6fd' },
  { key:'kulit',        initial:'K',  nama_poli:'Poli Kulit & Kelamin', tag:'Dermatologi',         keterangan:'Penyakit kulit, dermatitis & kelamin',         iconStyle:'background:#fce7f3;color:#be185d;border-color:#fbcfe8' },
  { key:'jantung',      initial:'J',  nama_poli:'Poli Jantung',         tag:'Kardiologi',          keterangan:'Penyakit jantung & pembuluh darah',            iconStyle:'background:#fee2e2;color:#dc2626;border-color:#fca5a5' },
  { key:'paru',         initial:'P',  nama_poli:'Poli Paru',            tag:'Pulmonologi',         keterangan:'Penyakit paru & saluran pernapasan',           iconStyle:'background:#dcfce7;color:#15803d;border-color:#bbf7d0' },
  { key:'saraf',        initial:'N',  nama_poli:'Poli Saraf',           tag:'Neurologi',           keterangan:'Gangguan saraf, stroke & neurologi',           iconStyle:'background:#ede9fe;color:#6d28d9;border-color:#ddd6fe' },
  { key:'bedah',        initial:'B',  nama_poli:'Poli Bedah',           tag:'Bedah Umum',          keterangan:'Konsultasi & tindakan bedah',                  iconStyle:'background:#ffedd5;color:#c2410c;border-color:#fed7aa' },
  { key:'ortopedi',     initial:'O',  nama_poli:'Poli Ortopedi',        tag:'Ortopedi',            keterangan:'Tulang, sendi & muskuloskeletal',              iconStyle:'background:#f0fdf4;color:#166534;border-color:#bbf7d0' },
  { key:'endokrin',     initial:'E',  nama_poli:'Poli Endokrin',        tag:'Endokrinologi',       keterangan:'Diabetes, tiroid & gangguan hormon',           iconStyle:'background:#fff7ed;color:#c2410c;border-color:#fed7aa' },
  { key:'rehabilitasi', initial:'R',  nama_poli:'Poli Rehabilitasi',    tag:'Rehab Medik',         keterangan:'Fisioterapi & rehabilitasi medik',             iconStyle:'background:#f0fdfa;color:#0f766e;border-color:#99f6e4' },
  { key:'jiwa',         initial:'Ps', nama_poli:'Poli Jiwa',            tag:'Psikiatri',           keterangan:'Kesehatan jiwa, psikiatri & konseling',        iconStyle:'background:#fef9c3;color:#854d0e;border-color:#fde68a' },
  { key:'kandungan',    initial:'Kd', nama_poli:'Poli Kandungan',       tag:'Obstetri-Ginekologi', keterangan:'Kandungan, kehamilan & ginekologi',            iconStyle:'background:#fce7f3;color:#be185d;border-color:#fbcfe8' },
]

const spGetId = (sp: any): number | null =>
  poliList.value.find((p: any) => (p.nama ?? '').toLowerCase().includes(sp.key))?.id ?? null

const POLI_UMUM_STYLE: Record<string, { iconStyle: string; tag: string }> = {
  'umum'      : { iconStyle: 'background:#dbeafe;color:#1d4ed8;border-color:#bfdbfe', tag: 'Umum' },
  'gigi'      : { iconStyle: 'background:#fce7f3;color:#be185d;border-color:#fbcfe8', tag: 'Gigi & Mulut' },
  'anak'      : { iconStyle: 'background:#dcfce7;color:#15803d;border-color:#bbf7d0', tag: 'Pediatri' },
  'kia'       : { iconStyle: 'background:#fef9c3;color:#854d0e;border-color:#fde68a', tag: 'KIA' },
  'kb'        : { iconStyle: 'background:#fce7f3;color:#be185d;border-color:#fbcfe8', tag: 'KB' },
  'lansia'    : { iconStyle: 'background:#f0fdf4;color:#166534;border-color:#bbf7d0', tag: 'Geriatri' },
  'gizi'      : { iconStyle: 'background:#fff7ed;color:#c2410c;border-color:#fed7aa', tag: 'Gizi' },
  'ptm'       : { iconStyle: 'background:#ede9fe;color:#6d28d9;border-color:#ddd6fe', tag: 'PTM' },
  'tb'        : { iconStyle: 'background:#fee2e2;color:#dc2626;border-color:#fca5a5', tag: 'TB' },
  'imunisasi' : { iconStyle: 'background:#e0f2fe;color:#0369a1;border-color:#bae6fd', tag: 'Imunisasi' },
  'mtbs'      : { iconStyle: 'background:#dcfce7;color:#15803d;border-color:#bbf7d0', tag: 'MTBS' },
}

function getPoliUmumStyle(nama: string) {
  const n = nama.toLowerCase()
  for (const [key, style] of Object.entries(POLI_UMUM_STYLE)) {
    if (n.includes(key)) return style
  }
  return { iconStyle: 'background:#f3f4f6;color:#374151;border-color:#e5e7eb', tag: 'Umum' }
}

const KATA_SPESIALIS = SPESIALIS_STATIS.map(s => s.key)
const poliUmum = computed(() =>
  poliList.value.filter((p: any) => {
    const kode = (p.kode ?? '').toUpperCase()
    return !kode.startsWith('PLI-')
  })
)

const pilihPoliSpesialis = (sp: any) => {
  errorMsg.value = ''
  const id = spGetId(sp)
  form.value.dokter_id       = null
  form.value.sesi_id         = null
  form.value.tanggal_dipilih = ''
  dokterList.value           = []
  sesiList.value             = []
  form.value.poli_id         = id ?? -1 * (SPESIALIS_STATIS.findIndex(s => s.key === sp.key) + 1)
}

// ── Computed ───────────────────────────────────────────────────────────────
const selectedPoli = computed(() => {
  if (!form.value.poli_id) return null
  const fromDB = poliList.value.find((p: any) => p.id === form.value.poli_id)
  if (fromDB) return fromDB
  const idx = form.value.poli_id < 0 ? (-form.value.poli_id - 1) : -1
  if (idx >= 0 && idx < SPESIALIS_STATIS.length) return { id: form.value.poli_id, nama: SPESIALIS_STATIS[idx].nama_poli }
  return null
})
const selectedDokter = computed(() => dokterList.value.find(d => d.id === form.value.dokter_id))
const selectedSesi   = computed(() => sesiList.value.find(s => s.id === form.value.sesi_id))

const poliInitial = (nama: string) => {
  if (!nama) return '?'
  const parts = nama.split(' ')
  return parts.length > 1 ? parts[parts.length - 1].charAt(0).toUpperCase() : nama.charAt(0).toUpperCase()
}

const dayNames_   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']
const monthNames_ = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
function getDayName(tanggal: string | Date) { const d = new Date(tanggal); return dayNames_[d.getDay()] }
function formatTanggal(tanggal: string | Date) { const d = new Date(tanggal); return `${dayNames_[d.getDay()]}, ${d.getDate()} ${monthNames_[d.getMonth()]} ${d.getFullYear()}` }

// ── 5 Hari ke Depan (mulai hari ini) ──────────────────────────────────────
const limahariKedepan = computed(() => {
  const result: { tanggal: string; hari: string; tgl: string; bulan_tahun: string }[] = []
  let offset = 0
  while (result.length < 5) {
    const d = new Date()
    d.setDate(d.getDate() + offset)
    offset++
    if (d.getDay() === 0) continue // skip Minggu
    const yyyy = d.getFullYear()
    const mm   = String(d.getMonth() + 1).padStart(2, '0')
    const dd   = String(d.getDate()).padStart(2, '0')
    result.push({
      tanggal    : `${yyyy}-${mm}-${dd}`,
      hari       : dayNames_[d.getDay()],
      tgl        : String(d.getDate()),
      bulan_tahun: monthNames_[d.getMonth()] + ' ' + d.getFullYear(),
    })
  }
  return result
})

// Format tanggal yang dipilih untuk ditampilkan di konfirmasi & tiket
const tanggalDipilihFmt = computed(() => {
  if (!form.value.tanggal_dipilih) return '-'
  return formatTanggal(form.value.tanggal_dipilih)
})

const pilihTanggal = (tanggal: string) => {
  form.value.tanggal_dipilih = tanggal
  form.value.sesi_id         = null
}

const sesiGrouped = computed(() => {
  const groups: Record<string, any> = {}
  for (const s of sesiList.value) {
    const key = s.tanggal ?? 'hari-ini'
    if (!groups[key]) {
      groups[key] = { tanggal: s.tanggal, tanggal_fmt: s.tanggal_fmt ?? (s.tanggal ? formatTanggal(s.tanggal) : 'Hari Ini'), hari: s.hari ?? (s.tanggal ? getDayName(s.tanggal) : 'Hari Ini'), slots: [] }
    }
    groups[key].slots.push(s)
  }
  return Object.values(groups)
})

function extractArray(data: any): any[] {
  if (Array.isArray(data)) return data
  if (data?.data && Array.isArray(data.data)) return data.data
  if (data?.dokter && Array.isArray(data.dokter)) return data.dokter
  if (data?.items && Array.isArray(data.items)) return data.items
  if (data?.list && Array.isArray(data.list)) return data.list
  return []
}

// ── Load data ──────────────────────────────────────────────────────────────
async function loadPoli() {
  loadingPoli.value = true
  try {
    let hasil: any[] = []
    try {
      const res = await api.get('/pasien/pendaftaran/poli')
      console.log('RAW response:', res.data)
      hasil = extractArray(res.data)
      console.log('hasil setelah extract:', hasil)
    } catch { /* fallback */ }
    if (hasil.length === 0) {
      const res = await api.get('/poli')
      console.log('fallback /poli:', res.data)
      hasil = extractArray(res.data)
    }
    poliList.value = hasil
    console.log('poliList final:', poliList.value)
  } catch {
    errorMsg.value = 'Gagal memuat daftar poli.'
  } finally {
    loadingPoli.value = false
  }
}

const selectPoli = (poli: any) => {
  form.value.poli_id         = poli.id
  form.value.dokter_id       = null
  form.value.sesi_id         = null
  form.value.tanggal_dipilih = ''
  dokterList.value           = []
  sesiList.value             = []
  errorMsg.value             = ''
}

const pilihPoli = async (poli: any) => {
  form.value.poli_id         = poli.id
  form.value.dokter_id       = null
  form.value.sesi_id         = null
  form.value.tanggal_dipilih = ''
  dokterList.value           = []
  sesiList.value             = []
  errorMsg.value             = ''
  loadingDokter.value        = true
  try {
    let hasil: any[] = []
    try {
      const res = await api.get(`/pasien/pendaftaran/dokter/${poli.id}`)
      hasil = extractArray(res.data)
    } catch { /* fallback */ }
    if (hasil.length === 0) {
      try {
        const res = await api.get(`/dokter/${poli.id}`)
        hasil = extractArray(res.data)
      } catch { /* silent */ }
    }
    dokterList.value = hasil
  } catch {
    errorMsg.value = 'Gagal memuat daftar dokter.'
  } finally {
    loadingDokter.value = false
  }
}

 function normalizeSesi(raw: any[]): any[] {
  return raw.map((s: any) => {
    const kuota    = Number(s.kuota ?? 0)
    const terpakai = Number(s.terpakai ?? 0)
    const sisa     = Number(s.sisa ?? Math.max(0, kuota - terpakai))
    return {
      ...s,
      kuota, terpakai, sisa,
      is_full    : sisa <= 0,
      jam_buka   : s.jam_buka   ?? s.jam_mulai   ?? '-',
      jam_tutup  : s.jam_tutup  ?? s.jam_selesai ?? '-',
      tanggal    : s.tanggal    ?? null,
      tanggal_fmt: s.tanggal_fmt ?? (s.tanggal ? formatTanggal(s.tanggal) : null),
      hari       : s.hari       ?? (s.tanggal   ? getDayName(s.tanggal)   : null),
    }
  })
}

const pilihDokter = async (dokter: any) => {
  form.value.dokter_id       = dokter.id
  form.value.sesi_id         = null
  form.value.tanggal_dipilih = ''
  sesiList.value             = []
  errorMsg.value             = ''
  loadingSesi.value          = true
  try {
    let raw: any[] = []
    try {
      const res = await api.get('/pasien/pendaftaran/sesi', {
        params: { dokter_id: dokter.id, poli_id: form.value.poli_id }
      })
      raw = extractArray(res.data)
    } catch { /* fallback */ }
    if (raw.length === 0) {
      try {
        const res = await api.get(`/jadwal/${dokter.id}`)
        raw = extractArray(res.data)
      } catch { /* silent */ }
    }
    sesiList.value = normalizeSesi(raw)
  } catch {
    errorMsg.value = 'Gagal memuat sesi antrian.'
  } finally {
    loadingSesi.value = false
  }
}

const pilihSesi = (s: any) => { if (s.is_full) return; form.value.sesi_id = s.id }

const resetStep1 = () => {
  step.value                 = 1
  form.value.poli_id         = null
  form.value.dokter_id       = null
  form.value.sesi_id         = null
  form.value.tanggal_dipilih = ''
  dokterList.value           = []
  sesiList.value             = []
  errorMsg.value             = ''
}

const nextToStep2 = async () => {
  if (!authStore.isLoggedIn) { sessionStorage.setItem('redirect_after_login', window.location.pathname); router.push('/login'); return }
  if (authStore.user?.role !== 'pasien') { errorMsg.value = 'Hanya pasien yang dapat mendaftar antrian.'; return }
  if (form.value.poli_id && form.value.poli_id > 0) {
    await pilihPoli({ id: form.value.poli_id, nama: selectedPoli.value?.nama ?? '' })
  }
  step.value = 2
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const nextToStep3 = () => {
  if (!authStore.isLoggedIn) { sessionStorage.setItem('redirect_after_login', window.location.pathname); router.push('/login'); return }
  if (authStore.user?.role !== 'pasien') { errorMsg.value = 'Hanya pasien yang dapat mendaftar antrian.'; return }
  step.value = 3
}

const submit = async () => {
  if (!authStore.isLoggedIn) { sessionStorage.setItem('redirect_after_login', window.location.pathname); router.push('/login'); return }
  errorMsg.value = ''; loading.value = true
  try {
    const payload: any = {
      poli_id         : form.value.poli_id,
      dokter_id       : form.value.dokter_id,
      tanggal         : form.value.tanggal_dipilih,
      jaminan         : form.value.jaminan,
      keluhan         : form.value.keluhan,
    }
    if (form.value.jaminan === 'BPJS' && form.value.no_bpjs) payload.no_bpjs = form.value.no_bpjs
    const res     = await api.post('/pasien/pendaftaran/daftar', payload)
    const antrian = res.data?.antrian ?? res.data?.data
    nomorAntrian.value = antrian?.nomor_display ?? (antrian?.kode_antrian + '-' + String(antrian?.nomor_antrian).padStart(3, '0'))
    submitted.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } catch (e: any) {
    errorMsg.value = e.response?.data?.message ?? 'Gagal mendaftar antrian. Coba lagi.'
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally { loading.value = false }
}

// ── QR Code ────────────────────────────────────────────────────────────────
const loadQRLib = (): Promise<any> => {
  return new Promise((resolve) => {
    if ((window as any).QRCode) { setTimeout(() => resolve((window as any).QRCode), 50); return }
    const script   = document.createElement('script')
    script.src     = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js'
    script.onload  = () => setTimeout(() => resolve((window as any).QRCode), 150)
    script.onerror = () => resolve(null)
    document.head.appendChild(script)
  })
}

const generateQR = async () => {
  await nextTick()
  if (!qrContainer.value || !nomorAntrian.value) return
  const QRCode = await loadQRLib()
  if (!QRCode) return
  qrContainer.value.innerHTML = ''
  const qrUrl = `${window.location.origin}/pasien/antrian/status?nomor=${encodeURIComponent(nomorAntrian.value)}`
  new QRCode(qrContainer.value, {
    text        : qrUrl,
    width       : 150,
    height      : 150,
    colorDark   : '#0f172a',
    colorLight  : '#ffffff',
    correctLevel: (window as any).QRCode?.CorrectLevel?.M ?? 0,
  })
}

watch(submitted, async (val) => {
  if (val) {
    await new Promise(r => setTimeout(r, 500))
    await generateQR()
  }
})

// ── Download PDF ───────────────────────────────────────────────────────────
const downloadTiket = async () => {
  loadingPdf.value = true
  await new Promise<void>((resolve, reject) => {
    if ((window as any).jspdf) { resolve(); return }
    const script = document.createElement('script')
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js'
    script.onload = () => resolve()
    script.onerror = () => reject(new Error('Gagal memuat jsPDF'))
    document.head.appendChild(script)
  })
  try {
    const { jsPDF } = (window as any).jspdf
    const doc = new jsPDF({ unit: 'mm', format: [80, 160], orientation: 'portrait' })
    const W = 80; let y = 0

    doc.setFillColor(14, 165, 233); doc.rect(0, 0, W, 30, 'F')
    doc.setTextColor(255,255,255); doc.setFont('helvetica','bold'); doc.setFontSize(12)
    doc.text('PUSKESMAS', W/2, 10, { align:'center' })
    doc.setFont('helvetica','normal'); doc.setFontSize(7)
    doc.text('Pelayanan Kesehatan Prima', W/2, 15.5, { align:'center' })
    doc.setFontSize(6.5); doc.setTextColor(200,235,255)
    doc.text('STRUK NOMOR ANTRIAN', W/2, 21, { align:'center' })
    const tglCetak = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' })
    doc.setFontSize(5.5); doc.setTextColor(180,220,245)
    doc.text('Dicetak: ' + tglCetak, W/2, 26.5, { align:'center' }); y = 34

    doc.setTextColor(15,23,42); doc.setFont('helvetica','normal'); doc.setFontSize(8)
    doc.text('Nomor Antrian Anda', W/2, y, { align:'center' }); y += 4
    doc.setDrawColor(14,165,233); doc.setLineWidth(0.5)
    doc.roundedRect(15, y, W-30, 20, 3, 3, 'S')
    doc.setFont('helvetica','bold'); doc.setFontSize(32); doc.setTextColor(14,165,233)
    doc.text(nomorAntrian.value, W/2, y+14, { align:'center' }); y += 26

    try {
      const qrImg = qrContainer.value?.querySelector('img') as HTMLImageElement | null
      if (qrImg?.src && qrImg.src.startsWith('data:')) {
        doc.setFont('helvetica','normal'); doc.setFontSize(7); doc.setTextColor(100,116,139)
        doc.text('Scan QR untuk cek status antrian', W/2, y, { align:'center' }); y += 4
        const qrSize = 30
        doc.addImage(qrImg.src, 'PNG', W/2 - qrSize/2, y, qrSize, qrSize)
        y += qrSize + 6
      }
    } catch { /* skip QR jika gagal */ }

    doc.setDrawColor(203,213,225); doc.setLineWidth(0.3); doc.setLineDash([1.5,1.5])
    doc.line(6,y,W-6,y); doc.setLineDash([]); y += 7

    doc.setFillColor(241,245,249); doc.rect(6,y-2,W-12,6,'F')
    doc.setFont('helvetica','bold'); doc.setFontSize(6.5); doc.setTextColor(100,116,139)
    doc.text('INFO KUNJUNGAN', 9, y+2.5); y += 8
    const kunjunganRows: [string,string][] = [
      ['Poliklinik', selectedPoli.value?.nama_poli ?? selectedPoli.value?.nama ?? '-'],
      ['Dokter', selectedDokter.value?.nama_dokter ?? selectedDokter.value?.nama ?? '-'],
      ['Tanggal', tanggalDipilihFmt.value],
      ['Jaminan', form.value.jaminan],
    ]
    doc.setFontSize(7)
    for (const [label, value] of kunjunganRows) {
      doc.setFont('helvetica','normal'); doc.setTextColor(100,116,139); doc.text(label, 9, y)
      doc.setFont('helvetica','bold'); doc.setTextColor(15,23,42)
      const lines = doc.splitTextToSize(value, 36)
      doc.text(lines, W-9, y, { align:'right' }); y += lines.length > 1 ? 7.5 : 6
    }

    y += 2; doc.setDrawColor(203,213,225); doc.setLineWidth(0.3); doc.setLineDash([1.5,1.5])
    doc.line(6,y,W-6,y); doc.setLineDash([]); y += 7

    doc.setFillColor(241,245,249); doc.rect(6,y-2,W-12,6,'F')
    doc.setFont('helvetica','bold'); doc.setFontSize(6.5); doc.setTextColor(100,116,139)
    doc.text('DATA PASIEN', 9, y+2.5); y += 8
    const pasienRows: [string,string][] = [
      ['Nama', form.value.nama],
      ['NIK', form.value.nik],
      ['Telepon', form.value.telepon],
    ]
    if (form.value.no_bpjs) pasienRows.push(['No. BPJS', form.value.no_bpjs])
    if (form.value.keluhan) pasienRows.push(['Keluhan', form.value.keluhan])
    doc.setFontSize(7)
    for (const [label, value] of pasienRows) {
      doc.setFont('helvetica','normal'); doc.setTextColor(100,116,139); doc.text(label, 9, y)
      doc.setFont('helvetica','bold'); doc.setTextColor(15,23,42)
      const lines = doc.splitTextToSize(value, 36)
      doc.text(lines, W-9, y, { align:'right' }); y += lines.length > 1 ? 7.5 : 6
    }

    y += 4; doc.setFillColor(248,250,252); doc.rect(0,y,W,22,'F')
    doc.setDrawColor(226,232,240); doc.setLineWidth(0.3); doc.line(0,y,W,y)
    doc.setFont('helvetica','normal'); doc.setFontSize(6.5); doc.setTextColor(100,116,139)
    doc.text('Tunjukkan struk ini saat tiba di puskesmas', W/2, y+6, { align:'center' })
    doc.text('Harap datang 15 menit sebelum sesi dimulai', W/2, y+11, { align:'center' })
    doc.setLineDash([1,1]); doc.line(10,y+14,W-10,y+14); doc.setLineDash([])
    doc.setFont('helvetica','bold'); doc.setFontSize(6); doc.setTextColor(14,165,233)
    doc.text('Puskesmas - Pelayanan Kesehatan Prima', W/2, y+19, { align:'center' })

    doc.save(`struk-antrian-${nomorAntrian.value}.pdf`)
  } catch (e) {
    alert('Gagal membuat PDF. Silakan coba lagi.')
  } finally {
    loadingPdf.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.pendaftaran-page { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #0f172a; background: #ffffff; min-height: 100vh; }

/* NAVBAR */
.navbar { background: #ffffff; border-bottom: 1.5px solid #e5e7eb; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 0 #e5e7eb; }
.nav-container { width: 100%; padding: 0 32px; height: 60px; display: flex; align-items: center; justify-content: space-between; }
.nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: inherit; flex-shrink: 0; }
.brand-icon { width: 34px; height: 34px; flex-shrink: 0; }
.brand-text { display: flex; flex-direction: column; line-height: 1; }
.brand-name { font-size: 15px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em; line-height: 1.2; }
.brand-sub  { font-size: 10.5px; color: #4b5563; font-weight: 500; margin-top: 1px; }
.nav-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0; }
.btn-outline { text-decoration: none; padding: 7px 16px; border: 1.5px solid #d1d5db; border-radius: 7px; font-size: 13px; font-weight: 600; color: #374151; background: #fff; transition: all .15s; font-family: inherit; }
.btn-outline:hover { border-color: #4b5563; color: #111827; }
.btn-primary-nav { text-decoration: none; padding: 7px 16px; background: #0f172a; color: white; border-radius: 7px; font-size: 13px; font-weight: 700; transition: all .15s; font-family: inherit; }
.btn-primary-nav:hover { background: #1e293b; }
.user-menu { position: relative; display: flex; align-items: center; gap: 9px; cursor: pointer; padding: 5px 12px 5px 6px; border-radius: 8px; border: 1.5px solid #e5e7eb; background: white; user-select: none; transition: border-color .15s, background .15s; }
.user-menu:hover { border-color: #d1d5db; background: #f9fafb; }
.user-avatar { width: 30px; height: 30px; border-radius: 7px; background: #0f172a; color: white; font-size: 13px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
.user-info { display: flex; flex-direction: column; line-height: 1.2; text-align: left; }
.user-name { font-size: 13px; font-weight: 700; color: #111827; white-space: nowrap; }
.user-role { font-size: 10.5px; color: #4b5563; font-weight: 500; }
.chevron { width: 12px; height: 12px; color: #4b5563; transition: transform .2s; flex-shrink: 0; }
.chevron.open { transform: rotate(180deg); }
.user-dropdown { position: absolute; top: calc(100% + 6px); right: 0; background: white; border: 1.5px solid #e5e7eb; border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,.08); min-width: 170px; z-index: 200; overflow: hidden; }
.dropdown-item { display: flex; align-items: center; gap: 9px; padding: 10px 14px; font-size: 13px; font-weight: 600; color: #374151; text-decoration: none; cursor: pointer; transition: background .12s; }
.dropdown-item svg { width: 14px; height: 14px; flex-shrink: 0; }
.dropdown-item:hover { background: #f9fafb; }
.dropdown-item.danger { color: #dc2626; }
.dropdown-item.danger:hover { background: #fef2f2; }
.dropdown-divider { height: 1px; background: #f3f4f6; }

/* PAGE */
.page-wrapper { min-height: calc(100vh - 60px); padding-bottom: 80px; background: #ffffff; }
.page-content { max-width: 960px; margin: 0 auto; padding: 36px 28px; background: #ffffff; }
.login-warning { display: inline-flex; align-items: center; gap: 9px; background: #fefce8; border: 1.5px solid #fde047; border-radius: 8px; padding: 10px 16px; font-size: 13px; color: #713f12; font-weight: 600; }
.warning-link { margin-left: 4px; font-weight: 800; color: #ca8a04; text-decoration: none; }
.warning-link:hover { text-decoration: underline; }
.error-alert { display: flex; align-items: center; gap: 10px; background: #fef2f2; border: 1.5px solid #fca5a5; border-left: 3px solid #dc2626; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 13.5px; color: #991b1b; font-weight: 600; }
.step-content { animation: fadeIn .2s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
.step-title { font-size: 22px; font-weight: 900; color: #0f172a; margin-bottom: 20px; letter-spacing: -0.02em; }
.loading-center { display: flex; justify-content: center; padding: 48px 0; }
.empty-inline { text-align: center; padding: 20px; color: #4b5563; font-size: 14px; font-weight: 500; }

/* STEP HEADER */
.step-header { margin-bottom: 24px; }
.step-desc { font-size: 14px; color: #4b5563; font-weight: 500; margin-top: 4px; }

/* KATEGORI */
.kategori-block { margin-bottom: 28px; }
.kategori-header { display: flex; align-items: center; gap: 12px; padding: 13px 18px; background: #f9fafb; border: 1.5px solid #e5e7eb; border-bottom: none; border-radius: 12px 12px 0 0; }
.kategori-icon-wrap { width: 34px; height: 34px; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.kategori-umum { background: #dbeafe; color: #1d4ed8; }
.kategori-spesialis { background: #ede9fe; color: #6d28d9; }
.kategori-title { font-size: 14px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em; }
.kategori-sub { font-size: 12px; color: #4b5563; font-weight: 500; margin-top: 2px; }
.kategori-block .poli-grid { border: 1.5px solid #e5e7eb; border-top: none; border-radius: 0 0 12px 12px; padding: 14px; background: #fff; margin-bottom: 0; }

/* POLI */
.poli-tag { display: inline-block; font-size: 10.5px; font-weight: 700; color: #7c3aed; background: #f3e8ff; border: 1px solid #e9d5ff; border-radius: 4px; padding: 1px 8px; margin-top: 7px; letter-spacing: 0.2px; }
.poli-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 28px; }
.poli-card { background: #fff; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 22px 18px; cursor: pointer; transition: all .18s; position: relative; }
.poli-card:hover { border-color: #4b5563; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.06); }
.poli-card.selected { border-color: #0f172a; border-width: 2px; background: #f9fafb; }
.poli-icon { width: 46px; height: 46px; border-radius: 10px; background: #f3f4f6; color: #0f172a; font-size: 20px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; border: 1.5px solid #e5e7eb; }
.poli-card.selected .poli-icon { background: #0f172a; color: #fff; border-color: #0f172a; }
.poli-name { font-size: 14.5px; font-weight: 800; color: #0f172a; margin-bottom: 4px; line-height: 1.3; letter-spacing: -0.01em; }
.poli-desc { font-size: 12px; color: #4b5563; line-height: 1.5; font-weight: 500; }
.poli-check { position: absolute; top: 12px; right: 12px; width: 22px; height: 22px; background: #0f172a; color: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.poli-check svg { width: 11px; height: 11px; }

.selected-poli-badge { display: inline-flex; align-items: center; gap: 10px; background: #f3f4f6; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 6px 14px; margin-bottom: 22px; }
.selected-poli-badge span { font-size: 13px; font-weight: 700; color: #374151; }
.selected-poli-badge button { font-size: 12px; color: #4b5563; background: none; border: none; cursor: pointer; text-decoration: underline; font-family: inherit; font-weight: 600; }

.form-group { margin-bottom: 20px; }
.form-label { display: block; font-size: 12px; font-weight: 800; color: #374151; margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.5px; }
.form-label.required::after { content: ' *'; color: #dc2626; }
.form-label-hint { font-size: 11px; font-weight: 500; color: #4b5563; margin-left: 8px; text-transform: none; letter-spacing: 0; }
.form-input { width: 100%; padding: 10px 13px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 500; outline: none; transition: all .18s; background: #fff; color: #0f172a; }
.form-input:focus { border-color: #0f172a; box-shadow: 0 0 0 3px rgba(15,23,42,.06); }
textarea.form-input { resize: vertical; }
.form-card { background: #fff; border-radius: 12px; padding: 26px; border: 1.5px solid #e5e7eb; margin-bottom: 24px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.radio-group { display: flex; gap: 8px; }
.radio-card { flex: 1; text-align: center; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; cursor: pointer; font-size: 13.5px; font-weight: 700; color: #4b5563; transition: all .15s; background: #fff; }
.radio-card.selected { border-color: #0f172a; border-width: 2px; background: #f9fafb; color: #0f172a; }

/* DOKTER */
.dokter-list { display: flex; flex-direction: column; gap: 8px; }
.dokter-card { display: flex; align-items: center; gap: 13px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; cursor: pointer; transition: all .18s; position: relative; }
.dokter-card:hover { border-color: #4b5563; background: #f9fafb; }
.dokter-card.selected { border-color: #0f172a; border-width: 2px; background: #f9fafb; }
.dokter-avatar { width: 40px; height: 40px; border-radius: 9px; background: #0f172a; color: white; font-size: 16px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.dokter-info { flex: 1; }
.dokter-name { font-size: 14px; font-weight: 800; color: #0f172a; letter-spacing: -0.01em; }
.dokter-spesialis { font-size: 12px; color: #4b5563; margin-top: 1px; font-weight: 500; }
.dokter-check { width: 22px; height: 22px; background: #0f172a; color: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.dokter-check svg { width: 11px; height: 11px; }

/* JAMINAN */
.jaminan-list { display: flex; gap: 10px; }
.jaminan-card { flex: 1; display: flex; align-items: center; gap: 11px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 13px 15px; cursor: pointer; transition: all .18s; }
.jaminan-card:hover { border-color: #4b5563; }
.jaminan-card.selected { border-color: #0f172a; border-width: 2px; background: #f9fafb; }
.jaminan-icon-wrap { width: 34px; height: 34px; border-radius: 8px; background: #f3f4f6; color: #374151; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .18s; border: 1.5px solid #e5e7eb; }
.jaminan-icon-wrap.selected { background: #0f172a; color: white; border-color: #0f172a; }
.jaminan-icon-wrap svg { width: 16px; height: 16px; }
.jaminan-name { font-size: 14px; font-weight: 800; color: #0f172a; }

/* TANGGAL PICKER */
.tanggal-grid { display: flex; flex-wrap: wrap; gap: 10px; }
.tanggal-card { display: flex; flex-direction: column; align-items: center; justify-content: center; min-width: 90px; padding: 14px 16px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 12px; cursor: pointer; transition: all .18s; text-align: center; }
.tanggal-card:hover { border-color: #4b5563; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.06); }
.tanggal-card.selected { border-color: #0f172a; border-width: 2px; background: #0f172a; }
.tanggal-hari { font-size: 10px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
.tanggal-card.selected .tanggal-hari { color: #94a3b8; }
.tanggal-angka { font-size: 34px; font-weight: 900; color: #0f172a; line-height: 1; letter-spacing: -0.03em; }
.tanggal-card.selected .tanggal-angka { color: #ffffff; }
.tanggal-bulan { font-size: 11px; font-weight: 600; color: #4b5563; margin-top: 4px; }
.tanggal-card.selected .tanggal-bulan { color: #cbd5e1; }

.sesi-placeholder { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 32px; color: #4b5563; font-size: 14px; font-weight: 500; border: 2px dashed #e5e7eb; border-radius: 10px; margin-bottom: 8px; background: #fff; }

/* KONFIRMASI */
.konfirmasi-card { background: #fff; border-radius: 12px; padding: 26px; border: 1.5px solid #e5e7eb; margin-bottom: 24px; }
.konfirmasi-header { display: flex; align-items: center; gap: 13px; margin-bottom: 18px; }
.konfirmasi-icon-wrap { width: 44px; height: 44px; border-radius: 10px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid #e5e7eb; }
.konfirmasi-icon-wrap svg { width: 22px; height: 22px; }
.konfirmasi-title { font-size: 17px; font-weight: 900; color: #0f172a; letter-spacing: -0.02em; }
.konfirmasi-sub { font-size: 13px; color: #4b5563; margin-top: 2px; font-weight: 500; }
.konfirmasi-separator { height: 1px; background: #f3f4f6; margin: 14px 0; }
.konfirmasi-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.konfirmasi-item { display: flex; flex-direction: column; gap: 3px; }
.konfirmasi-item.full { grid-column: span 2; }
.k-label { font-size: 10px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: .8px; }
.k-value { font-size: 14px; font-weight: 700; color: #0f172a; }
.badge-jaminan { display: inline-block; background: #f3f4f6; color: #374151; border: 1.5px solid #e5e7eb; border-radius: 5px; padding: 2px 9px; font-size: 12px; font-weight: 800; }

/* SUKSES */
.sukses { text-align: center; padding: 40px 0; }
.sukses-icon-wrap { width: 72px; height: 72px; border-radius: 16px; background: #f0fdf4; border: 2px solid #86efac; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
.sukses-icon-wrap svg { width: 36px; height: 36px; }
.sukses-title { font-size: 30px; font-weight: 900; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.03em; }
.sukses-desc { font-size: 15px; color: #4b5563; margin-bottom: 28px; font-weight: 500; }

/* TIKET */
.tiket-card { background: #fff; border-radius: 16px; padding: 24px; max-width: 380px; margin: 0 auto 28px; box-shadow: 0 8px 32px rgba(0,0,0,.08); border: 1.5px solid #e5e7eb; }
.tiket-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.tiket-poli { font-size: 12.5px; font-weight: 700; color: #4b5563; }
.tiket-status { font-size: 11px; font-weight: 800; color: #16a34a; background: #f0fdf4; border: 1px solid #86efac; padding: 3px 10px; border-radius: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
.tiket-nomor-label { font-size: 10px; color: #4b5563; text-align: center; margin-bottom: 3px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; }
.tiket-nomor { font-size: 68px; font-weight: 900; color: #0f172a; text-align: center; line-height: 1; margin-bottom: 16px; letter-spacing: -0.04em; }
.tiket-qr-wrap { display: flex; flex-direction: column; align-items: center; margin: 4px 0 16px; }
.tiket-qr-box { padding: 10px; background: white; border: 1.5px solid #e5e7eb; border-radius: 10px; display: inline-block; }
.tiket-qr-label { font-size: 10.5px; color: #6b7280; margin-top: 7px; font-weight: 600; }
.tiket-info { border-top: 1px dashed #e5e7eb; padding-top: 14px; }
.tiket-row { display: flex; justify-content: space-between; font-size: 13px; color: #4b5563; margin-bottom: 9px; font-weight: 500; }
.tiket-row span:last-child { font-weight: 700; color: #0f172a; }
.tiket-footer { text-align: center; font-size: 11px; color: #4b5563; margin-top: 14px; padding-top: 12px; border-top: 1px solid #f3f4f6; font-weight: 500; }
.sukses-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; align-items: center; }

/* BUTTONS */
.step-nav { display: flex; justify-content: space-between; align-items: center; padding-top: 24px; margin-top: 8px; }
.btn-back { display: inline-flex; align-items: center; gap: 6px; background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 9px 18px; font-size: 13.5px; font-weight: 700; color: #374151; cursor: pointer; text-decoration: none; transition: all .15s; font-family: 'Plus Jakarta Sans', sans-serif; }
.btn-back svg { width: 14px; height: 14px; }
.btn-back:hover { border-color: #4b5563; color: #0f172a; }
.btn-next { display: inline-flex; align-items: center; gap: 7px; background: #0f172a; color: white; border: none; border-radius: 8px; padding: 9px 20px; font-size: 13.5px; font-weight: 700; cursor: pointer; transition: all .18s; font-family: 'Plus Jakarta Sans', sans-serif; }
.btn-next:hover:not(:disabled) { background: #1e293b; }
.btn-next:disabled { opacity: 0.35; cursor: not-allowed; }
.btn-next svg { width: 15px; height: 15px; }
.btn-next-link { display: inline-flex; align-items: center; gap: 7px; background: #0f172a; color: white; text-decoration: none; border-radius: 8px; padding: 9px 18px; font-size: 13.5px; font-weight: 700; transition: all .18s; font-family: 'Plus Jakarta Sans', sans-serif; }
.btn-next-link:hover { background: #1e293b; }
.btn-next-link svg { width: 14px; height: 14px; }
.btn-submit { display: inline-flex; align-items: center; gap: 7px; background: #16a34a; color: white; border: none; border-radius: 8px; padding: 9px 22px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .18s; font-family: 'Plus Jakarta Sans', sans-serif; }
.btn-submit:hover:not(:disabled) { background: #15803d; }
.btn-submit:disabled { opacity: 0.4; cursor: not-allowed; }
.btn-download { display: inline-flex; align-items: center; gap: 6px; background: white; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 9px 18px; font-size: 13.5px; font-weight: 700; color: #374151; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: all .18s; }
.btn-download:hover:not(:disabled) { border-color: #4b5563; color: #0f172a; }
.btn-download:disabled { opacity: 0.4; cursor: not-allowed; }

/* LOADING */
.loading-row { display: flex; align-items: center; gap: 8px; }
.spinner { display: inline-block; width: 14px; height: 14px; border: 2.5px solid rgba(255,255,255,0.35); border-top-color: white; border-radius: 50%; animation: spin .65s linear infinite; }
.spinner-dark { border-color: rgba(15,23,42,.1); border-top-color: #0f172a; width: 28px; height: 28px; border-width: 3px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* RESPONSIVE */
@media (max-width: 768px) {
  .poli-grid { grid-template-columns: repeat(2, 1fr); }
  .nav-container { padding: 0 20px; }
}
@media (max-width: 540px) {
  .poli-grid { grid-template-columns: 1fr; }
  .form-row { grid-template-columns: 1fr; }
  .konfirmasi-grid { grid-template-columns: 1fr; }
  .konfirmasi-item.full { grid-column: span 1; }
  .sukses-actions { flex-direction: column; align-items: stretch; }
  .sukses-actions > * { justify-content: center; }
  .step-title { font-size: 20px; }
  .page-content { padding: 20px 16px; }
  .user-info { display: none; }
  .tanggal-grid { gap: 8px; }
  .tanggal-card { min-width: 70px; padding: 12px 10px; }
  .tanggal-angka { font-size: 26px; }
}
</style>
