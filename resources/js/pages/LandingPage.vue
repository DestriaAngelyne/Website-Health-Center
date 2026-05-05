<template>
  <div class="landing" :class="{ scrolled: isScrolled }">

    <!-- ═══ NAVBAR ═══ -->
    <nav class="navbar">
      <div class="nav-inner">
        <div class="nav-brand">
          <svg class="brand-logo" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="50" fill="#1a6aad"/>
            <circle cx="50" cy="50" r="44" fill="none" stroke="white" stroke-width="1.5"/>
            <defs>
              <path id="gm-top" d="M 15,50 A 35,35 0 0,1 85,50"/>
              <path id="gm-bot" d="M 23,63 A 29,29 0 0,0 77,63"/>
            </defs>
            <text font-family="Arial,sans-serif" font-weight="800" font-size="9.8" fill="white" letter-spacing="2">
              <textPath href="#gm-top" startOffset="50%" text-anchor="middle">GRAHA MEDIKA</textPath>
            </text>
            <text font-family="Arial,sans-serif" font-weight="600" font-size="8.5" fill="white" letter-spacing="1.2">
              <textPath href="#gm-bot" startOffset="50%" text-anchor="middle">Hospital</textPath>
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
          <div class="brand-text">
            <span class="brand-name">Graha Medika Prima</span>
          </div>
        </div>
        <div class="nav-links">
          <a href="#tentang"   class="nav-link" :class="{ active: activeNav === 'tentang' }"   @click="setNav('tentang')">Tentang</a>
          <a href="#fitur"     class="nav-link" :class="{ active: activeNav === 'fitur' }"     @click="setNav('fitur')">Fitur</a>
          <a href="#cara"      class="nav-link" :class="{ active: activeNav === 'cara' }"      @click="setNav('cara')">Cara Pakai</a>
          <a href="#poli"      class="nav-link" :class="{ active: activeNav === 'poli' }"      @click="setNav('poli')">Poli</a>
          <a href="#kontak"    class="nav-link" :class="{ active: activeNav === 'kontak' }"    @click="setNav('kontak')">Kontak</a>
        </div>
        <div class="nav-right">
          <a href="/pendaftaran-poli" class="btn-daftar-nav">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 8h8M8 4v8"/></svg>
            Daftar Poli
          </a>
          <template v-if="!authStore.isLoggedIn">
            <a href="/login" class="btn-ghost">Masuk</a>
            <a href="/register" class="btn-solid">Daftar Akun</a>
          </template>
          <template v-else>
            <div class="user-pill" @click.stop="dropOpen = !dropOpen">
              <div class="pill-avatar">
                <img v-if="authStore.fotoUrl" :src="authStore.fotoUrl" style="width:100%;height:100%;object-fit:cover;display:block;" />
                <template v-else>{{ userInitial }}</template>
              </div>
              <div class="pill-info">
                <span class="pill-name">{{ authStore.user?.name }}</span>
                <span class="pill-role">{{ roleLabel }}</span>
              </div>
              <svg class="pill-chevron" :class="{ open: dropOpen }" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 4.5l3 3 3-3"/></svg>
              <div class="pill-drop" v-if="dropOpen">
                <router-link :to="dashRoute" class="drop-item" @click="dropOpen=false">
                  <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 6l6-4 6 4v8a1 1 0 01-1 1H3a1 1 0 01-1-1V6z"/></svg>
                  Dashboard
                </router-link>
                <div class="drop-sep"></div>
                <a class="drop-item danger" @click.prevent="doLogout">
                  <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 12l4-4-4-4M14 8H6M6 2H3a1 1 0 00-1 1v10a1 1 0 001 1h3"/></svg>
                  Keluar
                </a>
              </div>
            </div>
          </template>
        </div>
        <button class="hamburger" @click="mobileOpen = !mobileOpen" :class="{ active: mobileOpen }">
          <span></span><span></span><span></span>
        </button>
      </div>
      <div class="mobile-drawer" :class="{ open: mobileOpen }">
        <a href="#tentang"        @click="mobileOpen=false">Tentang</a>
        <a href="#fitur"          @click="mobileOpen=false">Fitur</a>
        <a href="#cara"           @click="mobileOpen=false">Cara Pakai</a>
        <a href="#poli"           @click="mobileOpen=false">Poli</a>
        <a href="#kontak"         @click="mobileOpen=false">Kontak</a>
        <a href="/pendaftaran-poli" @click="mobileOpen=false" class="drawer-cta">Daftar Poli Online</a>
        <template v-if="!authStore.isLoggedIn">
          <a href="/login"    @click="mobileOpen=false">Masuk</a>
          <a href="/register" @click="mobileOpen=false" class="drawer-solid">Daftar Akun</a>
        </template>
        <template v-else>
          <router-link :to="dashRoute" @click="mobileOpen=false">Dashboard</router-link>
          <a @click.prevent="doLogout" class="drawer-danger">Keluar</a>
        </template>
      </div>
      <div class="scroll-progress">
        <div class="scroll-bar" :style="{ width: scrollPct + '%' }"></div>
      </div>
    </nav>

    <!-- ═══ HERO ═══ -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hb-grid"></div>
        <div class="hb-orb hb-orb-1"></div>
        <div class="hb-orb hb-orb-2"></div>
        <div class="hb-orb hb-orb-3"></div>
      </div>
      <div class="hero-body">
        <div class="hero-center">
          <h1 class="hero-h1">
            Layanan Antrean<br>
            <span class="h1-accent">Lebih Mudah &amp; Cepat</span>
          </h1>
          <p class="hero-p">
            Dengan pendaftaran online Graha Medika Prima, masyarakat dapat mengakses layanan kesehatan dengan lebih mudah dan terencana. Kami hadir untuk memastikan setiap pasien mendapatkan pelayanan yang tepat waktu dan berkualitas.
          </p>
          <div class="hero-features">
            <div class="hf-item">
              <div class="hf-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="20" height="20"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
              </div>
              <div class="hf-text">
                <span class="hf-title">Daftar Online</span>
                <span class="hf-desc">Kapan saja, dari mana saja</span>
              </div>
            </div>
            <div class="hf-sep"></div>
            <div class="hf-item">
              <div class="hf-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="20" height="20"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              </div>
              <div class="hf-text">
                <span class="hf-title">Pantau Jadwal</span>
                <span class="hf-desc">Informasi waktu langsung</span>
              </div>
            </div>
            <div class="hf-sep"></div>
            <div class="hf-item">
              <div class="hf-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="20" height="20"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              <div class="hf-text">
                <span class="hf-title">Dokter Lengkap</span>
                <span class="hf-desc">10+ dokter berpengalaman</span>
              </div>
            </div>
            <div class="hf-sep"></div>
            <div class="hf-item">
              <div class="hf-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="20" height="20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div class="hf-text">
                <span class="hf-title">Terpercaya</span>
                <span class="hf-desc">98% kepuasan pasien</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <a href="#tentang" class="scroll-hint">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
      </a>
    </section>

    <!-- ═══ TENTANG PUSKESMAS ═══ -->
    <section class="tentang-sec" id="tentang">
      <div class="section-wrap">
        <div class="tentang-single reveal">
          <div class="sec-eyebrow" style="display:inline-flex;">Tentang Puskesmas</div>
          <h2 class="sec-h2 dark">Puskesmas Graha Medika Prima</h2>

          <p class="tentang-desc">
            Puskesmas Graha Medika Prima berdiri sejak tahun <strong>2010</strong> sebagai fasilitas kesehatan tingkat pertama yang melayani masyarakat dengan sepenuh hati. Berlokasi strategis di pusat kota, kami hadir untuk memastikan setiap warga mendapatkan akses layanan kesehatan yang terjangkau, mudah dijangkau, dan berkualitas tinggi.
          </p>
          <p class="tentang-desc">
            Dengan visi <em>"Menjadi Puskesmas unggulan yang mampu memberikan pelayanan kesehatan paripurna bagi seluruh lapisan masyarakat"</em>, kami terus berinovasi dan meningkatkan mutu layanan — dari bayi hingga lansia, dari promotif hingga rehabilitatif.
          </p>
          <p class="tentang-desc">
            Selama lebih dari <strong>14 tahun</strong> beroperasi, Graha Medika Prima telah melayani ribuan pasien setiap bulannya dengan dedikasi penuh dari tim tenaga kesehatan yang berpengalaman dan terlatih. Kami percaya bahwa kesehatan adalah hak setiap warga, sehingga seluruh layanan kami dirancang untuk dapat diakses oleh semua kalangan masyarakat tanpa terkecuali.
          </p>
          <p class="tentang-desc">
            Sebagai fasilitas kesehatan tingkat pertama yang telah mendapatkan akreditasi <strong>PARIPURNA</strong>, kami berkomitmen untuk terus meningkatkan standar pelayanan, mengembangkan kompetensi tenaga medis, serta mengadopsi teknologi terkini — termasuk sistem pendaftaran online ini — demi memberikan pengalaman layanan kesehatan yang lebih nyaman, efisien, dan terdigitalisasi bagi seluruh pasien.
          </p>

          <div class="tentang-layanan-title">Layanan yang Kami Sediakan</div>
          <div class="tentang-points">
            <div class="tp-item" v-for="p in tentangPoints" :key="p.text">
              <div class="tp-icon">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5" width="12"><path d="M2 8l4 4 8-8"/></svg>
              </div>
              <span>{{ p.text }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ FITUR UTAMA ═══ -->
    <section class="fitur-sec" id="fitur">
      <div class="section-wrap">
        <div class="sec-head center">
          <div class="sec-eyebrow">Fitur Utama</div>
          <h2 class="sec-h2 dark">Apa Saja yang Bisa Anda Lakukan?</h2>
          <p class="sec-sub center-text">Sistem kami hadir dengan berbagai fitur lengkap untuk memudahkan pelayanan kesehatan</p>
        </div>
        <div class="fitur-grid">
          <div class="fitur-card reveal" v-for="f in fiturList" :key="f.title">
            <div class="fitur-icon" :style="`background:${f.bg};color:${f.color}`">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" v-html="f.icon"></svg>
            </div>
            <div class="fitur-num">{{ String(fiturList.indexOf(f)+1).padStart(2,'0') }}</div>
            <h3>{{ f.title }}</h3>
            <p>{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ CARA PAKAI ═══ -->
    <section class="cara-sec" id="cara">
      <div class="cara-inner">
        <div class="sec-head center">
          <div class="sec-eyebrow light">Cara Menggunakan</div>
          <h2 class="sec-h2" style="color:white">Mudah dalam 5 Langkah</h2>
          <p class="sec-sub center-text" style="color:rgba(255,255,255,.82)">Ikuti langkah berikut untuk mendapatkan nomor antrean di Graha Medika Prima</p>
        </div>
        <div class="cara-steps">
          <div class="cara-step reveal" v-for="(s, i) in caraList" :key="s.title">
            <div class="cs-left">
              <div class="cs-num">{{ i + 1 }}</div>
              <div class="cs-line" v-if="i < caraList.length - 1"></div>
            </div>
            <div class="cs-content">
              <div class="cs-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" v-html="s.icon"></svg>
              </div>
              <div class="cs-text">
                <h3>{{ s.title }}</h3>
                <p>{{ s.desc }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ INFORMASI POLI ═══ -->
    <section class="poli-sec" id="poli">
      <div class="section-wrap">
        <div class="sec-head center">
          <div class="sec-eyebrow">Informasi Poli</div>
          <h2 class="sec-h2 dark">Poli yang Tersedia</h2>
          <p class="sec-sub center-text">Pilih poli sesuai kebutuhan kesehatan Anda</p>
        </div>
        <div class="poli-grid">
          <div class="poli-card reveal" v-for="p in poliList" :key="p.nama">
            <div class="poli-top">
              <div class="poli-icon" :style="{ background: p.bg, color: p.color }">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" v-html="p.icon"></svg>
              </div>
              <div class="poli-jadwal-wrap">
                <span class="poli-day" v-for="d in p.jadwal" :key="d">{{ d }}</span>
              </div>
            </div>
            <h3>{{ p.nama }}</h3>
            <p>{{ p.desc }}</p>
            <div class="poli-footer">
              <div class="poli-docs">
                <div class="doc-avs">
                  <div class="doc-av" v-for="(d,i) in p.dokter" :key="i" :title="d">{{ d.charAt(0) }}</div>
                </div>
                <span>{{ p.dokter.length }} Dokter</span>
              </div>
              <a href="/pendaftaran-poli" class="poli-cta">
                Daftar
                <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h8M7 3l4 4-4 4"/></svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ KONTAK ═══ -->
    <section class="kontak-sec" id="kontak">
      <div class="section-wrap">
        <div class="sec-head center">
          <div class="sec-eyebrow">Hubungi Kami</div>
          <h2 class="sec-h2 dark">Kontak &amp; Lokasi</h2>
          <p class="sec-sub center-text">Tim kami siap melayani Anda selama jam operasional puskesmas</p>
        </div>
        <div class="kontak-grid">
          <div class="kontak-cards">
            <div class="kcard">
              <div class="kcard-icon" style="background:#dbeafe;color:#1d4ed8">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
              </div>
              <div class="kcard-body">
                <div class="kcard-label">Telepon</div>
                <a href="tel:02112345678" class="kcard-val">(021) 1234-5678</a>
                <div class="kcard-hint">Senin–Sabtu jam kerja</div>
              </div>
            </div>
            <div class="kcard">
              <div class="kcard-icon" style="background:#dcfce7;color:#15803d">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              <div class="kcard-body">
                <div class="kcard-label">Alamat</div>
                <div class="kcard-val">Jl. Kesehatan No. 1</div>
                <div class="kcard-hint">Kota, Provinsi 12345</div>
              </div>
            </div>
            <div class="kcard">
              <div class="kcard-icon" style="background:#fce7f3;color:#be185d">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <div class="kcard-body">
                <div class="kcard-label">Email</div>
                <a href="mailto:info@puskesmas.go.id" class="kcard-val">info@puskesmas.go.id</a>
                <div class="kcard-hint">Respon dalam 1x24 jam</div>
              </div>
            </div>
            <div class="kcard jam-card">
              <div class="kcard-icon" style="background:#fef3c7;color:#b45309">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              </div>
              <div class="kcard-body">
                <div class="kcard-label">Jam Layanan</div>
                <div class="jam-list">
                  <div class="jam-row"><span>Senin – Jumat</span><span class="jam-time">07.00 – 16.00</span></div>
                  <div class="jam-row"><span>Sabtu</span><span class="jam-time">07.00 – 12.00</span></div>
                  <div class="jam-row"><span>Minggu</span><span class="jam-time closed">Tutup</span></div>
                </div>
              </div>
            </div>
          </div>
          <div class="kontak-map">
            <div class="map-wrap">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195614!3d-6.194741!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764b12d%3A0x3d2ad6e1e0e9bcc8!2sJakarta!5e0!3m2!1sid!2sid!4v1620000000000!5m2!1sid!2sid"
                width="100%" height="100%" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <a href="https://maps.google.com" target="_blank" rel="noopener" class="map-cta">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="15" height="15"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Buka di Google Maps
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer class="footer">
      <div class="footer-inner">
        <div class="footer-top">
          <div class="footer-brand">
            <div class="footer-logo">
              <svg class="brand-logo-footer" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="50" fill="#1a6aad"/>
                <circle cx="50" cy="50" r="44" fill="none" stroke="white" stroke-width="1.5"/>
                <defs>
                  <path id="gm-top-f" d="M 15,50 A 35,35 0 0,1 85,50"/>
                  <path id="gm-bot-f" d="M 23,63 A 29,29 0 0,0 77,63"/>
                </defs>
                <text font-family="Arial,sans-serif" font-weight="800" font-size="9.8" fill="white" letter-spacing="2">
                  <textPath href="#gm-top-f" startOffset="50%" text-anchor="middle">GRAHA MEDIKA</textPath>
                </text>
                <text font-family="Arial,sans-serif" font-weight="600" font-size="8.5" fill="white" letter-spacing="1.2">
                  <textPath href="#gm-bot-f" startOffset="50%" text-anchor="middle">Hospital</textPath>
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
              <div>
                <div class="brand-name">Graha Medika Prima</div>
                <div class="brand-tagline">Pelayanan Kesehatan Prima</div>
              </div>
            </div>
            <p class="footer-desc">Melayani masyarakat dengan sepenuh hati untuk Indonesia yang lebih sehat.</p>
            <div class="footer-socials">
              <a href="#" class="social-btn" title="Facebook">
                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
              </a>
              <a href="#" class="social-btn" title="Instagram">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
              </a>
              <a href="#" class="social-btn" title="WhatsApp">
                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
              </a>
            </div>
          </div>
          <div class="fl-col">
            <h4>Layanan Poli</h4>
            <a href="#poli">Poli Umum</a>
            <a href="#poli">Poli Gigi</a>
            <a href="#poli">Poli KIA</a>
            <a href="#poli">Poli Anak</a>
            <a href="#poli">Poli Lansia</a>
          </div>
          <div class="fl-col">
            <h4>Akun</h4>
            <a href="/register">Daftar Pasien</a>
            <a href="/login">Masuk</a>
            <a href="/pendaftaran-poli">Daftar Poli Online</a>
            <a href="/pasien/dashboard">Cek Jadwal</a>
          </div>
          <div class="fl-col">
            <h4>Jam Layanan</h4>
            <div class="fl-schedule">
              <div class="fl-sch-row">
                <span>Senin – Jumat</span>
                <span class="fl-sch-time">07.00 – 16.00</span>
              </div>
              <div class="fl-sch-row">
                <span>Sabtu</span>
                <span class="fl-sch-time">07.00 – 12.00</span>
              </div>
              <div class="fl-sch-row">
                <span>Minggu</span>
                <span class="fl-sch-time closed">Tutup</span>
              </div>
            </div>
          </div>
        </div>
        <div class="footer-divider"></div>
        <div class="footer-bar">
          <span>© 2024 Puskesmas Graha Medika Prima · Sistem Informasi Antrean Digital. All Rights Reserved.</span>
        </div>
      </div>
    </footer>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth.store'

const authStore  = useAuthStore()
const isScrolled = ref(false)
const mobileOpen = ref(false)
const dropOpen   = ref(false)
const scrollPct  = ref(0)
const activeNav  = ref('')

const setNav = (key: string) => { activeNav.value = key }

onMounted(async () => {
  if (authStore.token && !authStore.user) await authStore.fetchUser()
  document.addEventListener('click', closeDropOutside)
  window.addEventListener('scroll', onScroll)
  revealOnScroll()
})
onUnmounted(() => {
  document.removeEventListener('click', closeDropOutside)
  window.removeEventListener('scroll', onScroll)
})

const closeDropOutside = (e: MouseEvent) => {
  if (!(e.target as HTMLElement).closest('.user-pill')) dropOpen.value = false
}
const onScroll = () => {
  isScrolled.value = window.scrollY > 40
  const scrollTop = window.scrollY || document.documentElement.scrollTop
  const docH = document.documentElement.scrollHeight - window.innerHeight
  scrollPct.value = docH > 0 ? Math.min((scrollTop / docH) * 100, 100) : 0
  const sections = ['kontak','poli','cara','fitur','tentang']
  for (const id of sections) {
    const el = document.getElementById(id)
    if (el && window.scrollY >= el.offsetTop - 120) { activeNav.value = id; break }
  }
  if (window.scrollY < 200) activeNav.value = ''
}

const userInitial = computed(() => authStore.user?.name?.charAt(0).toUpperCase() ?? 'U')
const roleLabel   = computed(() => {
  const m: Record<string,string> = { admin:'Admin', loket:'Loket', perawat:'Perawat', dokter:'Dokter', pasien:'Pasien' }
  return m[authStore.user?.role ?? ''] ?? '-'
})
const dashRoute = computed(() => {
  const m: Record<string,string> = { admin:'/admin/dashboard', loket:'/loket/dashboard', perawat:'/perawat/dashboard', dokter:'/dokter/dashboard', pasien:'/pasien/dashboard' }
  return m[authStore.user?.role ?? ''] ?? '/'
})
const doLogout = async () => { dropOpen.value = false; mobileOpen.value = false; await authStore.logout() }

const revealOnScroll = () => {
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { (e.target as HTMLElement).classList.add('in'); io.unobserve(e.target) } })
  }, { threshold: 0.1 })
  document.querySelectorAll('.reveal').forEach(el => io.observe(el))
}

// ── TENTANG: Layanan Unggulan Puskesmas ──
const tentangPoints = [
  { text: 'Pelayanan kesehatan dasar untuk seluruh lapisan masyarakat tanpa terkecuali' },
  { text: 'Pemeriksaan kehamilan (ANC), persalinan, dan kesehatan ibu & anak (KIA)' },
  { text: 'Program imunisasi lengkap dan posyandu aktif setiap bulan' },
  { text: 'Prolanis: penanganan berkelanjutan untuk pasien hipertensi & diabetes' },
  { text: 'Pemeriksaan laboratorium sederhana tersedia langsung di tempat' },
  { text: 'Sistem rujukan terkoordinasi ke rumah sakit untuk penanganan lanjutan' },
  { text: 'Layanan gigi dan mulut oleh dokter gigi berpengalaman' },
  { text: 'Konsultasi gizi dan tumbuh kembang anak oleh tenaga ahli' },
  { text: 'Pelayanan lansia terpadu dengan pendekatan komprehensif dan penuh empati' },
  { text: 'Penyuluhan kesehatan masyarakat dan program preventif berkala' },
  { text: 'Pelayanan KB (Keluarga Berencana) lengkap untuk pasangan usia subur' },
  { text: 'Pemantauan kesehatan lingkungan dan sanitasi wilayah kerja puskesmas' },
]

const fiturList = [
  { title: 'Pendaftaran Online', desc: 'Pasien dapat mendaftar layanan kesehatan melalui website kapan saja tanpa perlu mengunjungi loket pendaftaran rumah sakit.', bg: '#dbeafe', color: '#1d4ed8', icon: '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>' },
  { title: 'Pemantauan Jadwal', desc: 'Pantau nomor layanan yang sedang dipanggil, estimasi waktu tunggu, dan status dokter secara langsung.', bg: '#dcfce7', color: '#15803d', icon: '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>' },
  { title: 'Jadwal Dokter Lengkap', desc: 'Informasi jadwal praktek dokter spesialis di setiap poli tersedia lengkap untuk membantu pasien memilih waktu kunjungan.', bg: '#fef3c7', color: '#b45309', icon: '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>' },
  { title: 'Dashboard Manajemen', desc: 'Petugas medis dan administrasi dapat mengelola data pasien, jadwal layanan, dan jadwal dokter melalui dashboard terpadu.', bg: '#ede9fe', color: '#6d28d9', icon: '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>' },
  { title: 'Notifikasi Otomatis', desc: 'Sistem mengirimkan notifikasi secara otomatis ketika giliran pemeriksaan Anda hampir tiba, sehingga Anda bisa hadir tepat waktu.', bg: '#ffedd5', color: '#c2410c', icon: '<path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>' },
  { title: 'Riwayat Kunjungan', desc: 'Akses rekam jejak kunjungan, diagnosis, dan resep dokter kapan saja sebagai referensi konsultasi medis lanjutan.', bg: '#fce7f3', color: '#be185d', icon: '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>' },
]

const caraList = [
  { title: 'Kunjungi Portal Graha Medika Prima', desc: 'Akses portal layanan kami melalui browser di smartphone atau komputer Anda.', icon: '<path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>' },
  { title: 'Login atau Daftar Akun Pasien', desc: 'Masuk menggunakan akun yang telah terdaftar atau buat akun baru sebagai pasien Graha Medika Prima.', icon: '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>' },
  { title: 'Pilih Layanan Pendaftaran', desc: 'Klik tombol "Daftar Poli" untuk memulai proses pendaftaran kunjungan.', icon: '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>' },
  { title: 'Pilih Poli, Dokter & Jadwal', desc: 'Tentukan poli yang dibutuhkan, pilih dokter spesialis, serta jadwal kunjungan yang tersedia.', icon: '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>' },
  { title: 'Terima Nomor Layanan', desc: 'Nomor layanan Anda akan langsung diterbitkan. Hadir ke puskesmas sesuai estimasi waktu yang tertera.', icon: '<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' },
]

const poliList = [
  { nama:'Poli Umum', bg:'#dbeafe', color:'#1d4ed8', desc:'Pemeriksaan kesehatan umum, konsultasi, dan penanganan penyakit ringan hingga sedang.', dokter:['dr. Budi Santoso','dr. Hendra W.'], jadwal:['Sen','Sel','Rab','Kam','Jum'], icon:'<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>' },
  { nama:'Poli Gigi', bg:'#dcfce7', color:'#15803d', desc:'Perawatan gigi dan mulut, pencabutan, tambal gigi, dan pembersihan karang gigi.', dokter:['dr. Anita Dewi'], jadwal:['Sen','Rab','Jum'], icon:'<path d="M12 2C9 2 7 4 7 7c0 2 .5 3.5 1 5 .6 1.7 1 3 1 4h6c0-1 .4-2.3 1-4 .5-1.5 1-3 1-5 0-3-2-5-5-5z"/><path d="M9 21h6"/>' },
  { nama:'Poli KIA', bg:'#fce7f3', color:'#be185d', desc:'Kesehatan Ibu dan Anak, pemeriksaan kehamilan, imunisasi, dan KB.', dokter:['dr. Sari Indah','dr. Lina M.'], jadwal:['Sel','Kam','Sab'], icon:'<path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>' },
  { nama:'Poli Anak', bg:'#fef3c7', color:'#b45309', desc:'Pemeriksaan dan perawatan kesehatan anak usia 0–18 tahun oleh dokter spesialis anak.', dokter:['dr. Hendra Wijaya'], jadwal:['Sen','Sel','Rab','Kam'], icon:'<path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>' },
  { nama:'Poli Kandungan', bg:'#ede9fe', color:'#6d28d9', desc:'Pemeriksaan kehamilan, USG, persalinan, dan kesehatan reproduksi wanita.', dokter:['dr. Sari Indah'], jadwal:['Sel','Kam'], icon:'<path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>' },
  { nama:'Poli Lansia', bg:'#ffedd5', color:'#c2410c', desc:'Pelayanan khusus lanjut usia dengan penanganan komprehensif dan penuh perhatian.', dokter:['dr. Budi Santoso'], jadwal:['Sel','Kam'], icon:'<path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>' },
]
</script>

<style>
body { overflow-x: hidden; }
</style>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --sky:   #0ea5e9;
  --sky-d: #0284c7;
  --ink:   #0c1a2e;
  --bg:    #f1f5f9;
  --white: #ffffff;
  --bdr:   #e2e8f0;
  --shadow-sm: 0 1px 4px rgba(12,26,46,.07);
  --shadow-md: 0 4px 20px rgba(12,26,46,.10);
}

.landing { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: var(--ink); background: var(--bg); }

/* ── LOGO ── */
.brand-logo        { width: 40px; height: 40px; flex-shrink: 0; border-radius: 50%; }
.brand-logo-footer { width: 34px; height: 34px; flex-shrink: 0; border-radius: 50%; }

/* ── SCROLL PROGRESS ── */
.scroll-progress { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: rgba(255,255,255,.06); pointer-events: none; overflow: hidden; }
.scroll-bar { height: 100%; background: linear-gradient(90deg, #38bdf8, #818cf8, #a78bfa); transition: width .05s linear; box-shadow: 0 0 16px #38bdf8; min-width: 0; max-width: 100%; }

/* ── NAVBAR ── */
.navbar { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: rgba(8,20,40,.82); backdrop-filter: blur(20px) saturate(1.8); border-bottom: 1px solid rgba(255,255,255,.07); transition: background .3s, box-shadow .3s; visibility: visible !important; opacity: 1 !important; transform: none !important; pointer-events: auto !important; }
.landing.scrolled .navbar { background: rgba(8,20,40,.97); border-bottom-color: rgba(255,255,255,.13); box-shadow: 0 4px 32px rgba(0,0,0,.4); }
.nav-inner { width: 100%; padding: 0 28px 0 20px; height: 64px; display: flex; align-items: center; gap: 24px; }
.nav-brand { display: flex; align-items: center; gap: 12px; color: white; flex-shrink: 0; }
.brand-name    { font-size: 16px; font-weight: 800; color: white; line-height: 1.2; letter-spacing: -0.02em; }
.brand-tagline { font-size: 10.5px; color: rgba(255,255,255,.82); }
.nav-links { display: flex; gap: 0; flex: 1; justify-content: center; }
.nav-link { text-decoration: none; color: rgba(255,255,255,.82); font-size: 13px; font-weight: 600; padding: 22px 14px 8px; position: relative; transition: color .25s; display: flex; align-items: center; }
.nav-link::before { content: ''; position: absolute; top: 0; left: 14px; right: 14px; height: 2px; background: linear-gradient(90deg, #38bdf8, #818cf8); border-radius: 0 0 3px 3px; transform: scaleX(0); transform-origin: left; transition: transform .28s cubic-bezier(.4,0,.2,1); }
.nav-link:hover { color: white; }
.nav-link:hover::before, .nav-link.active::before { transform: scaleX(1); }
.nav-link.active { color: white; font-weight: 700; }
.nav-right { display: flex; gap: 8px; align-items: center; margin-left: auto; }
.btn-daftar-nav { display: inline-flex; align-items: center; gap: 5px; text-decoration: none; padding: 7px 14px; background: rgba(14,165,233,.18); border: 1.5px solid rgba(14,165,233,.4); color: #7dd3fc; border-radius: 8px; font-size: 13px; font-weight: 600; transition: all .2s; }
.btn-daftar-nav svg { width: 12px; height: 12px; }
.btn-daftar-nav:hover { background: var(--sky); color: white; border-color: var(--sky); }
.btn-ghost { text-decoration: none; padding: 7px 15px; border: 1.5px solid rgba(255,255,255,.22); border-radius: 8px; font-size: 13.5px; font-weight: 600; color: rgba(255,255,255,.88); transition: all .2s; }
.btn-ghost:hover { border-color: rgba(255,255,255,.45); color: white; }
.btn-solid { text-decoration: none; padding: 7px 16px; background: var(--sky); color: white; border-radius: 8px; font-size: 13.5px; font-weight: 600; transition: all .2s; }
.btn-solid:hover { background: var(--sky-d); }
.user-pill { position: relative; display: flex; align-items: center; gap: 9px; cursor: pointer; padding: 5px 12px 5px 5px; border-radius: 100px; border: 1.5px solid rgba(255,255,255,.22); background: rgba(255,255,255,.07); transition: all .2s; user-select: none; }
.user-pill:hover { border-color: rgba(255,255,255,.38); background: rgba(255,255,255,.13); }
.pill-avatar { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, var(--sky), #8b5cf6); color: white; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
.pill-info { display: flex; flex-direction: column; line-height: 1.2; }
.pill-name { font-size: 12px; font-weight: 700; color: white; }
.pill-role { font-size: 10px; color: rgba(255,255,255,.82); }
.pill-chevron { width: 12px; height: 12px; color: rgba(255,255,255,.82); transition: transform .2s; flex-shrink: 0; }
.pill-chevron.open { transform: rotate(180deg); }
.pill-drop { position: absolute; top: calc(100% + 8px); right: 0; background: #1a2744; border: 1px solid rgba(255,255,255,.1); border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,.5); min-width: 176px; z-index: 200; overflow: hidden; animation: dropIn .15s ease; }
@keyframes dropIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:none; } }
.drop-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: 13px; font-weight: 500; color: rgba(255,255,255,.92); text-decoration: none; transition: background .15s; cursor: pointer; }
.drop-item svg { width: 14px; height: 14px; flex-shrink: 0; }
.drop-item:hover { background: rgba(255,255,255,.07); color: white; }
.drop-item.danger { color: #f87171; }
.drop-item.danger:hover { background: rgba(239,68,68,.1); }
.drop-sep { height: 1px; background: rgba(255,255,255,.07); }
.hamburger { display: none; flex-direction: column; gap: 5px; padding: 6px; background: none; border: none; cursor: pointer; margin-left: auto; }
.hamburger span { display: block; width: 22px; height: 2px; background: white; border-radius: 2px; transition: all .25s; transform-origin: center; }
.hamburger.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.hamburger.active span:nth-child(2) { opacity: 0; }
.hamburger.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
.mobile-drawer { display: none; flex-direction: column; gap: 4px; padding: 16px 24px 20px; border-top: 1px solid rgba(255,255,255,.07); background: #0c1829; }
.mobile-drawer.open { display: flex; }
.mobile-drawer a { text-decoration: none; color: rgba(255,255,255,.90); font-size: 15px; font-weight: 500; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.06); }
.drawer-cta { background: rgba(14,165,233,.14); border-radius: 8px; padding: 10px 14px !important; color: #7dd3fc !important; font-weight: 700 !important; border: none !important; }
.drawer-solid { background: var(--sky) !important; color: white !important; border-radius: 8px; padding: 10px 14px !important; border: none !important; margin-top: 4px; }
.drawer-danger { color: #f87171 !important; font-weight: 700 !important; }

/* ── HERO ── */
.hero { min-height: 100vh; padding-top: 64px; display: flex; flex-direction: column; align-items: stretch; background: #040f20; position: relative; overflow: hidden; }
.hero-bg { position: absolute; inset: 0; pointer-events: none; }
.hb-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(14,165,233,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(14,165,233,.04) 1px, transparent 1px); background-size: 44px 44px; }
.hb-orb { position: absolute; border-radius: 50%; filter: blur(120px); }
.hb-orb-1 { width: 600px; height: 600px; background: rgba(14,165,233,.09); top: -200px; right: -100px; animation: od1 14s ease-in-out infinite; }
.hb-orb-2 { width: 400px; height: 400px; background: rgba(99,102,241,.07); bottom: -100px; left: -80px; animation: od2 18s ease-in-out infinite; }
.hb-orb-3 { width: 300px; height: 300px; background: rgba(139,92,246,.05); top: 30%; left: 35%; animation: od1 22s ease-in-out infinite reverse; }
@keyframes od1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(20px,-30px)} }
@keyframes od2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-15px,22px)} }

.hero-body { flex: 1; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; padding: 60px 28px 40px; }
.hero-center { max-width: 760px; width: 100%; text-align: center; display: flex; flex-direction: column; align-items: center; }
.hero-h1 { font-size: clamp(36px, 5vw, 64px); font-weight: 800; line-height: 1.1; letter-spacing: -0.03em; color: #f1f5f9; margin-bottom: 20px; animation: fadeUp .5s .1s ease both; }
.h1-accent { color: white; }
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:none} }
.hero-p { font-size: 16px; color: rgba(255,255,255,.75); line-height: 1.8; max-width: 560px; margin-bottom: 40px; animation: fadeUp .5s .2s ease both; }
.hero-features { display: flex; align-items: center; gap: 0; background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 16px; padding: 20px 28px; animation: fadeUp .5s .3s ease both; flex-wrap: wrap; justify-content: center; width: 100%; }
.hf-item { display: flex; align-items: center; gap: 12px; padding: 8px 20px; }
.hf-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(14,165,233,.15); border: 1px solid rgba(14,165,233,.25); color: #7dd3fc; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.hf-text { display: flex; flex-direction: column; gap: 2px; text-align: left; }
.hf-title { font-size: 13px; font-weight: 700; color: white; }
.hf-desc { font-size: 11px; color: rgba(255,255,255,.55); }
.hf-sep { width: 1px; height: 40px; background: rgba(255,255,255,.1); flex-shrink: 0; }
.scroll-hint { position: absolute; bottom: 28px; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,.2); text-decoration: none; animation: bounceY 2s ease-in-out infinite; z-index: 1; }
.scroll-hint svg { width: 20px; height: 20px; }
@keyframes bounceY { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(7px)} }

/* ── SHARED ── */
.section-wrap { max-width: 1280px; margin: 0 auto; padding: 88px 28px; }
.sec-head { margin-bottom: 52px; }
.sec-head.center { text-align: center; }
.sec-eyebrow { display: inline-flex; align-items: center; background: #dbeafe; color: #1d4ed8; font-size: 11px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; padding: 4px 13px; border-radius: 100px; margin-bottom: 14px; }
.sec-eyebrow.light { background: rgba(255,255,255,.1); color: rgba(255,255,255,.92); }
.sec-h2 { font-size: clamp(28px, 3.8vw, 44px); font-weight: 800; color: white; margin-bottom: 12px; letter-spacing: -0.03em; }
.sec-h2.dark { color: #000000; }
.sec-sub { font-size: 15px; color: #000000; line-height: 1.75; max-width: 480px; }
.sec-sub.center-text { margin-left: auto; margin-right: auto; }

/* ── TENTANG ── */
.tentang-sec { background: white; }
.tentang-single { width: 100%; }
.tentang-desc { font-size: 15px; color: #000000; line-height: 1.8; margin-bottom: 16px; }
.tentang-desc strong { color: #0284c7; }
.tentang-desc em { font-style: italic; color: #334155; }
.tentang-layanan-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #64748b; margin-bottom: 12px; margin-top: 8px; }
.tentang-points { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px 32px; }
.tp-item { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: #000000; font-weight: 500; line-height: 1.5; }
.tp-icon { width: 22px; height: 22px; background: #dbeafe; color: #1d4ed8; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }

/* ── FITUR ── */
.fitur-sec { background: #f8fafc; }
.fitur-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.fitur-card { background: white; border-radius: 20px; padding: 28px; border: 1px solid var(--bdr); transition: all .3s; position: relative; }
.fitur-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: #bae6fd; }
.fitur-icon { width: 50px; height: 50px; border-radius: 13px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; }
.fitur-icon svg { width: 24px; height: 24px; }
.fitur-num { font-size: 11px; font-weight: 700; color: #6b7280; letter-spacing: 1px; margin-bottom: 10px; }
.fitur-card h3 { font-size: 16px; font-weight: 700; color: #000000; margin-bottom: 8px; }
.fitur-card p  { font-size: 13.5px; color: #000000; line-height: 1.65; }

/* ── CARA ── */
.cara-sec { background: #040e22; }
.cara-inner { max-width: 1280px; margin: 0 auto; padding: 88px 28px; }
.cara-steps { display: flex; flex-direction: column; gap: 0; max-width: 700px; margin: 0 auto 48px; }
.cara-step { display: flex; gap: 20px; }
.cs-left { display: flex; flex-direction: column; align-items: center; gap: 0; width: 48px; flex-shrink: 0; }
.cs-num { width: 48px; height: 48px; border-radius: 50%; background: rgba(14,165,233,.15); border: 2px solid rgba(14,165,233,.35); color: #7dd3fc; font-size: 17px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.cs-line { flex: 1; width: 2px; background: rgba(14,165,233,.15); margin: 8px 0; min-height: 32px; }
.cs-content { display: flex; gap: 18px; padding-bottom: 36px; flex: 1; }
.cs-icon { width: 44px; height: 44px; border-radius: 11px; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.14); color: rgba(255,255,255,.85); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }
.cs-icon svg { width: 20px; height: 20px; }
.cs-text h3 { font-size: 16px; font-weight: 700; color: white; margin-bottom: 6px; margin-top: 8px; }
.cs-text p  { font-size: 13.5px; color: rgba(255,255,255,.82); line-height: 1.65; }

/* ── POLI ── */
.poli-sec { background: white; }
.poli-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px,1fr)); gap: 18px; }
.poli-card { background: var(--bg); border-radius: 20px; padding: 24px; border: 1px solid var(--bdr); transition: all .3s; display: flex; flex-direction: column; gap: 10px; }
.poli-card:hover { background: white; transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: #bae6fd; }
.poli-top { display: flex; justify-content: space-between; align-items: flex-start; }
.poli-icon { width: 50px; height: 50px; border-radius: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.poli-icon svg { width: 24px; height: 24px; }
.poli-jadwal-wrap { display: flex; gap: 4px; flex-wrap: wrap; justify-content: flex-end; }
.poli-day { background: white; border: 1px solid var(--bdr); color: #475569; font-size: 10px; font-weight: 600; padding: 2.5px 7px; border-radius: 6px; }
.poli-card h3 { font-size: 16px; font-weight: 700; color: #000000; }
.poli-card p  { font-size: 13px; color: #000000; line-height: 1.6; flex: 1; }
.poli-footer { display: flex; align-items: center; justify-content: space-between; }
.poli-docs { display: flex; align-items: center; gap: 8px; }
.doc-avs { display: flex; }
.doc-av { width: 26px; height: 26px; border-radius: 50%; background: linear-gradient(135deg, var(--sky), #8b5cf6); color: white; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid white; margin-left: -6px; }
.doc-av:first-child { margin-left: 0; }
.poli-docs span { font-size: 12px; color: #475569; font-weight: 500; }
.poli-cta { display: inline-flex; align-items: center; gap: 5px; text-decoration: none; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; transition: all .2s; flex-shrink: 0; }
.poli-cta svg { width: 11px; height: 11px; }
.poli-cta:hover { background: var(--sky); color: white; border-color: var(--sky); }

/* ── KONTAK ── */
.kontak-sec { background: #f8fafc; }
.kontak-grid { display: grid; grid-template-columns: 1fr 1.4fr; gap: 28px; align-items: start; }
.kontak-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.kcard { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; display: flex; gap: 14px; align-items: flex-start; transition: all .25s; }
.kcard:hover { box-shadow: 0 4px 20px rgba(12,26,46,.1); transform: translateY(-2px); border-color: #bae6fd; }
.jam-card { grid-column: span 2; }
.kcard-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.kcard-body { flex: 1; }
.kcard-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #000000; margin-bottom: 4px; }
.kcard-val { font-size: 15px; font-weight: 700; color: #000000; display: block; text-decoration: none; }
.kcard-val:hover { color: #0284c7; }
.kcard-hint { font-size: 12px; color: #000000; margin-top: 2px; }
.jam-list { display: flex; flex-direction: column; gap: 6px; margin-top: 2px; }
.jam-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #000000; }
.jam-time { font-weight: 700; color: #000000; }
.jam-time.closed { color: #ef4444; }
.kontak-map { display: flex; flex-direction: column; gap: 12px; }
.map-wrap { width: 100%; height: 380px; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(12,26,46,.08); }
.map-cta { display: inline-flex; align-items: center; gap: 8px; align-self: flex-start; background: #0284c7; color: white; text-decoration: none; padding: 10px 18px; border-radius: 9px; font-size: 13.5px; font-weight: 600; transition: all .2s; }
.map-cta:hover { background: #0369a1; transform: translateY(-1px); }

/* ── FOOTER ── */
.footer { background: #040e22; color: white; }
.footer-inner { max-width: 1280px; margin: 0 auto; padding: 72px 28px 0; }
.footer-top { display: grid; grid-template-columns: 1.8fr 1fr 1fr 1.2fr; gap: 48px; padding-bottom: 56px; align-items: start; }
.footer-brand { display: flex; flex-direction: column; gap: 18px; }
.footer-logo  { display: flex; align-items: center; gap: 12px; }
.footer-logo .brand-name    { font-size: 16px; color: white; font-weight: 800; }
.footer-logo .brand-tagline { font-size: 11px; color: rgba(255,255,255,.78); }
.footer-desc { font-size: 13.5px; color: rgba(255,255,255,.75); line-height: 1.75; max-width: 280px; }
.footer-socials { display: flex; gap: 8px; margin-top: 4px; }
.social-btn { width: 34px; height: 34px; border-radius: 9px; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1); color: rgba(255,255,255,.82); display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all .2s; }
.social-btn:hover { background: rgba(255,255,255,.14); color: white; border-color: rgba(255,255,255,.2); }
.fl-col h4 { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.4px; color: rgba(255,255,255,.65); margin-bottom: 20px; }
.fl-col a { display: block; text-decoration: none; font-size: 14px; color: rgba(255,255,255,.82); margin-bottom: 12px; line-height: 1.5; transition: color .2s; }
.fl-col a:hover { color: #38bdf8; }
.fl-schedule { display: flex; flex-direction: column; gap: 10px; }
.fl-sch-row { display: flex; flex-direction: column; gap: 2px; }
.fl-sch-row span { font-size: 13px; color: rgba(255,255,255,.78); }
.fl-sch-time { font-size: 13.5px; font-weight: 600; color: rgba(255,255,255,.95) !important; }
.fl-sch-time.closed { color: #f87171 !important; }
.footer-divider { height: 1px; background: rgba(255,255,255,.08); }
.footer-bar { padding: 22px 0 28px; text-align: center; font-size: 13px; color: rgba(255,255,255,.65); }

/* ── REVEAL ── */
.reveal { opacity: 0; transform: translateY(22px); transition: opacity .55s ease, transform .55s ease; }
.reveal.in { opacity: 1; transform: none; }
.sec-h2, .sec-sub, .sec-eyebrow { opacity: 1 !important; transform: none !important; }
.navbar { opacity: 1 !important; transform: none !important; visibility: visible !important; }

/* ── RESPONSIVE ── */
@media (max-width: 1200px) {
  .fitur-grid { grid-template-columns: 1fr 1fr; }
  .footer-top { grid-template-columns: 1fr 1fr; gap: 32px; }
}
@media (max-width: 1024px) {
  .kontak-grid { grid-template-columns: 1fr; }
  .map-wrap { height: 300px; }
}
@media (max-width: 768px) {
  .nav-links, .nav-right { display: none; }
  .hamburger { display: flex; }
  .hero-body { padding: 48px 20px 32px; }
  .hero-features { padding: 16px; }
  .hf-item { padding: 8px 12px; }
  .hf-sep { display: none; }
  .section-wrap { padding: 60px 20px; }
  .sec-head { margin-bottom: 36px; }
  .fitur-grid { grid-template-columns: 1fr; }
  .poli-grid { grid-template-columns: 1fr; }
  .kontak-cards { grid-template-columns: 1fr; }
  .jam-card { grid-column: span 1; }
  .map-wrap { height: 260px; }
  .footer-inner { padding: 48px 20px 0; }
  .footer-top { grid-template-columns: 1fr 1fr; gap: 28px; padding-bottom: 36px; }
  .cara-inner { padding: 60px 20px; }
  .tentang-points { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 480px) {
  .nav-inner { padding: 0 16px; }
  .hero-body { padding: 40px 16px 28px; }
  .hero-h1 { font-size: 32px; }
  .hero-features { flex-direction: column; align-items: flex-start; }
  .hf-sep { display: none; }
  .section-wrap { padding: 48px 16px; }
  .tentang-points { grid-template-columns: 1fr; }
  .footer-top { grid-template-columns: 1fr; gap: 24px; padding-bottom: 32px; }
  .footer-inner { padding: 40px 16px 0; }
  .footer-bar { padding: 16px; }
}
@media (max-width: 360px) {
  .brand-tagline { display: none; }
}
</style>
