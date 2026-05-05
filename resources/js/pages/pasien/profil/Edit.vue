<template>
  <div class="container-xxl">
    <!--begin::Loading-->
    <div v-if="loadingData" class="d-flex justify-content-center py-20">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <!--end::Loading-->

    <div v-else>
      <!--begin::Page Title-->
      <div class="d-flex align-items-center mb-7">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 my-0">
          Edit Profil Saya
        </h1>
      </div>
      <!--end::Page Title-->

      <div class="row g-5 g-xl-10">
        <!--begin::Col Left - Foto Profil-->
        <div class="col-xl-4">
          <div class="card mb-5">
            <div class="card-body d-flex flex-column align-items-center py-10">

              <!--begin::Avatar — lingkaran sempurna + tombol edit-->
              <div class="av-wrap mb-5">
                <div class="av-circle"
                  :style="(previewPhoto || authStore.fotoUrl) ? 'background:transparent;border-color:transparent;' : ''">
                  <img v-if="previewPhoto" :src="previewPhoto" alt="Foto Profil" class="av-img" />
                  <img v-else-if="authStore.fotoUrl" :src="authStore.fotoUrl" alt="Foto Profil" class="av-img" />
                  <div v-else class="av-initial">
                    {{ form.nama_lengkap?.charAt(0)?.toUpperCase() || '?' }}
                  </div>
                </div>
                <label for="upload_photo" class="av-edit-btn" title="Ganti Foto">
                  <i class="ki-duotone ki-pencil fs-7">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                </label>
                <input id="upload_photo" type="file" accept="image/*" class="d-none" @change="onPhotoChange" />
              </div>
              <!--end::Avatar-->

              <div class="fw-bold fs-4 text-gray-900">{{ form.nama_lengkap }}</div>
              <div class="text-muted fs-6 mb-3">{{ form.email }}</div>
              <span class="badge badge-light-primary fw-semibold px-4 py-2">Pasien</span>

              <div class="separator separator-dashed my-6 w-100"></div>

              <div class="w-100">
                <div class="d-flex justify-content-between mb-3">
                  <span class="text-muted fs-7">No. RM</span>
                  <span class="fw-bold fs-7 text-gray-800">{{ form.no_rm || '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                  <span class="text-muted fs-7">Jaminan</span>
                  <span class="badge badge-light-success fw-semibold">
                    {{ form.jenis_jaminan?.toUpperCase() || '-' }}
                  </span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted fs-7">No. BPJS</span>
                  <span class="fw-bold fs-7 text-gray-800">{{ form.no_bpjs || '-' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--end::Col Left-->

        <!--begin::Col Right - Form-->
        <div class="col-xl-8">

          <!--begin::Alert-->
          <div v-if="alertMsg.text" class="alert d-flex align-items-center mb-6"
            :class="alertMsg.type === 'success' ? 'alert-success' : 'alert-danger'">
            <i class="ki-duotone fs-2hx me-3"
              :class="alertMsg.type === 'success' ? 'ki-shield-tick text-success' : 'ki-shield-cross text-danger'">
              <span class="path1"></span><span class="path2"></span>
            </i>
            <span class="fw-semibold">{{ alertMsg.text }}</span>
          </div>
          <!--end::Alert-->

          <!--begin::Card Data Diri-->
          <div class="card mb-5">
            <div class="card-header">
              <div class="card-title">
                <h3 class="fw-bold m-0">Data Diri</h3>
              </div>
            </div>
            <div class="card-body">

              <div class="row mb-5">
                <div class="col-md-6">
                  <label class="form-label required fw-semibold">Nama Lengkap</label>
                  <input v-model="form.nama_lengkap" type="text" class="form-control form-control-solid"
                    placeholder="Masukkan nama lengkap" :class="{ 'is-invalid': errors.nama_lengkap }" />
                  <div v-if="errors.nama_lengkap" class="invalid-feedback">{{ errors.nama_lengkap }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label required fw-semibold">NIK</label>
                  <input v-model="form.nik" type="text" class="form-control form-control-solid"
                    placeholder="16 digit NIK" maxlength="16" :class="{ 'is-invalid': errors.nik }" />
                  <div v-if="errors.nik" class="invalid-feedback">{{ errors.nik }}</div>
                </div>
              </div>

              <div class="row mb-5">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Tanggal Lahir</label>
                  <input v-model="form.tanggal_lahir" type="date" class="form-control form-control-solid" />
                </div>
                <div class="col-md-6">
                  <label class="form-label required fw-semibold">Jenis Kelamin</label>
                  <div class="d-flex gap-5 mt-3">
                    <div class="form-check form-check-custom form-check-solid">
                      <input v-model="form.jenis_kelamin" class="form-check-input" type="radio" value="L" id="jk_laki" />
                      <label class="form-check-label fw-semibold" for="jk_laki">Laki-laki</label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid">
                      <input v-model="form.jenis_kelamin" class="form-check-input" type="radio" value="P" id="jk_perempuan" />
                      <label class="form-check-label fw-semibold" for="jk_perempuan">Perempuan</label>
                    </div>
                  </div>
                  <div v-if="errors.jenis_kelamin" class="text-danger fs-7 mt-1">{{ errors.jenis_kelamin }}</div>
                </div>
              </div>

              <div class="row mb-5">
                <div class="col-md-6">
                  <label class="form-label required fw-semibold">No. Telepon</label>
                  <input v-model="form.no_telepon" type="text" class="form-control form-control-solid"
                    placeholder="08xxxxxxxxxx" :class="{ 'is-invalid': errors.no_telepon }" />
                  <div v-if="errors.no_telepon" class="invalid-feedback">{{ errors.no_telepon }}</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label required fw-semibold">Email</label>
                  <input v-model="form.email" type="email" class="form-control form-control-solid"
                    placeholder="email@example.com" :class="{ 'is-invalid': errors.email }" />
                  <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
                </div>
              </div>

              <div class="mb-5">
                <label class="form-label required fw-semibold">Alamat Lengkap</label>
                <textarea v-model="form.alamat" class="form-control form-control-solid" rows="3"
                  placeholder="Masukkan alamat lengkap" :class="{ 'is-invalid': errors.alamat }"></textarea>
                <div v-if="errors.alamat" class="invalid-feedback">{{ errors.alamat }}</div>
              </div>

              <div class="row mb-5">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Golongan Darah</label>
                  <select v-model="form.golongan_darah" class="form-select form-select-solid">
                    <option value="">-- Pilih --</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">No. BPJS</label>
                  <input v-model="form.no_bpjs" type="text" class="form-control form-control-solid"
                    placeholder="Nomor BPJS (jika ada)" />
                </div>
              </div>

              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" @click="simpanProfil" :disabled="loadingSimpan">
                  <span v-if="!loadingSimpan">
                    <i class="ki-duotone ki-check fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                    Simpan Perubahan
                  </span>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Menyimpan...
                  </span>
                </button>
              </div>

            </div>
          </div>
          <!--end::Card Data Diri-->

          <!--begin::Card Ganti Password-->
          <div class="card">
            <div class="card-header">
              <div class="card-title">
                <h3 class="fw-bold m-0">Ganti Password</h3>
              </div>
            </div>
            <div class="card-body">

              <div class="mb-5">
                <label class="form-label required fw-semibold">Password Lama</label>
                <div class="position-relative">
                  <input v-model="passwordForm.password_lama" :type="showPasswordLama ? 'text' : 'password'"
                    class="form-control form-control-solid pe-12" placeholder="Masukkan password lama"
                    :class="{ 'is-invalid': passwordErrors.password_lama }" />
                  <button type="button" class="btn btn-icon position-absolute end-0 top-0 h-100"
                    @click="showPasswordLama = !showPasswordLama">
                    <i :class="showPasswordLama ? 'ki-duotone ki-eye-slash' : 'ki-duotone ki-eye'" class="fs-4">
                      <span class="path1"></span><span class="path2"></span>
                      <span class="path3" v-if="!showPasswordLama"></span>
                    </i>
                  </button>
                </div>
                <div v-if="passwordErrors.password_lama" class="text-danger fs-7 mt-1">{{ passwordErrors.password_lama }}</div>
              </div>

              <div class="mb-5">
                <label class="form-label required fw-semibold">Password Baru</label>
                <div class="position-relative">
                  <input v-model="passwordForm.password_baru" :type="showPasswordBaru ? 'text' : 'password'"
                    class="form-control form-control-solid pe-12" placeholder="Minimal 8 karakter"
                    :class="{ 'is-invalid': passwordErrors.password_baru }" />
                  <button type="button" class="btn btn-icon position-absolute end-0 top-0 h-100"
                    @click="showPasswordBaru = !showPasswordBaru">
                    <i :class="showPasswordBaru ? 'ki-duotone ki-eye-slash' : 'ki-duotone ki-eye'" class="fs-4">
                      <span class="path1"></span><span class="path2"></span>
                      <span class="path3" v-if="!showPasswordBaru"></span>
                    </i>
                  </button>
                </div>
                <div v-if="passwordErrors.password_baru" class="text-danger fs-7 mt-1">{{ passwordErrors.password_baru }}</div>
              </div>

              <div class="mb-7">
                <label class="form-label required fw-semibold">Konfirmasi Password Baru</label>
                <div class="position-relative">
                  <input v-model="passwordForm.konfirmasi_password" :type="showKonfirmasi ? 'text' : 'password'"
                    class="form-control form-control-solid pe-12" placeholder="Ulangi password baru"
                    :class="{ 'is-invalid': passwordErrors.konfirmasi_password }" />
                  <button type="button" class="btn btn-icon position-absolute end-0 top-0 h-100"
                    @click="showKonfirmasi = !showKonfirmasi">
                    <i :class="showKonfirmasi ? 'ki-duotone ki-eye-slash' : 'ki-duotone ki-eye'" class="fs-4">
                      <span class="path1"></span><span class="path2"></span>
                      <span class="path3" v-if="!showKonfirmasi"></span>
                    </i>
                  </button>
                </div>
                <div v-if="passwordErrors.konfirmasi_password" class="text-danger fs-7 mt-1">{{ passwordErrors.konfirmasi_password }}</div>
              </div>

              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-danger" @click="gantiPassword" :disabled="loadingPassword">
                  <span v-if="!loadingPassword">
                    <i class="ki-duotone ki-lock fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                    Ganti Password
                  </span>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Memproses...
                  </span>
                </button>
              </div>

            </div>
          </div>
          <!--end::Card Ganti Password-->

        </div>
        <!--end::Col Right-->
      </div>
    </div>

    <!-- ══════════════════════════════════════════ -->
    <!-- MODAL CROP — canvas kiri, kontrol kanan   -->
    <!-- ══════════════════════════════════════════ -->
    <div v-if="showCrop" class="crop-overlay" @click.self="cancelCrop">
      <div class="crop-modal">

        <!-- Header -->
        <div class="crop-hd">
          <span class="crop-hd-title">Sesuaikan Foto Profil</span>
          <button class="crop-hd-close" @click="cancelCrop">×</button>
        </div>

        <!-- Body -->
        <div class="crop-bd">
          <!-- Canvas -->
          <div class="crop-canvas-col">
            <canvas
              ref="cropCanvas"
              class="crop-canvas"
              @mousedown="startDrag" @mousemove="onDrag"
              @mouseup="stopDrag"   @mouseleave="stopDrag"
              @touchstart.prevent="startDragTouch"
              @touchmove.prevent="onDragTouch"
              @touchend="stopDrag"
            ></canvas>
            <p class="crop-hint">Geser foto untuk mengatur posisi</p>
          </div>

          <!-- Controls -->
          <div class="crop-ctrl-col">

            <div class="crop-field">
              <label class="crop-lbl">🔍 Zoom</label>
              <input type="range" class="crop-range"
                v-model.number="cs.scale"
                :min="cs.minScale" :max="cs.minScale * 3" :step="0.01"
                @input="draw" />
              <div class="crop-ends"><span>Min</span><span>Max</span></div>
            </div>

            <div class="crop-field">
              <label class="crop-lbl">🔄 Rotasi <span class="crop-val">{{ cs.rotation }}°</span></label>
              <input type="range" class="crop-range"
                v-model.number="cs.rotation"
                min="-180" max="180" step="1"
                @input="draw" />
              <div class="crop-ends"><span>-180°</span><span>+180°</span></div>
              <div class="crop-rot-btns">
                <button class="crop-rbtn" @click="rotate(-90)">↺ -90°</button>
                <button class="crop-rbtn" @click="cs.rotation=0; draw()">0°</button>
                <button class="crop-rbtn" @click="rotate(90)">+90° ↻</button>
              </div>
            </div>

            <div class="crop-info">Hasil akan dipotong jadi lingkaran</div>

            <div class="crop-actions">
              <button class="btn btn-sm btn-light w-100 mb-2" @click="cancelCrop">Batal</button>
              <button class="btn btn-sm btn-primary w-100" @click="applyCrop">✓ Gunakan Foto</button>
            </div>

          </div>
        </div>

      </div>
    </div>
    <!-- END MODAL CROP -->

  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, onMounted, nextTick } from "vue";
import { PasienService } from "@/services/pasien.service";
import { useAuthStore } from "@/stores/auth.store";

export default defineComponent({
  name: "pasien-edit-profil",
  setup() {
    const authStore = useAuthStore();

    // ── Form (sama persis dengan aslinya) ──────────────────────────
    const form = reactive({
      nama_lengkap  : "",
      nik           : "",
      tanggal_lahir : "",
      jenis_kelamin : "",
      no_telepon    : "",
      email         : "",
      alamat        : "",
      golongan_darah: "",
      no_bpjs       : "",
      no_rm         : "",
      jenis_jaminan : "",
    });

    const errors        = reactive<Record<string, string>>({});
    const alertMsg      = reactive({ text: "", type: "success" });
    const loadingSimpan = ref(false);
    const loadingData   = ref(true);
    const previewPhoto  = ref<string | null>(null);

    const passwordForm = reactive({
      password_lama       : "",
      password_baru       : "",
      konfirmasi_password : "",
    });

    const passwordErrors   = reactive<Record<string, string>>({});
    const loadingPassword  = ref(false);
    const showPasswordLama = ref(false);
    const showPasswordBaru = ref(false);
    const showKonfirmasi   = ref(false);

    // ── Helper alert ──────────────────────────────────────────────
    const showAlert = (text: string, type: "success" | "danger" = "success") => {
      alertMsg.text = text;
      alertMsg.type = type;
      setTimeout(() => { alertMsg.text = ""; }, 4000);
    };

    // ── Load profil (sama persis dengan aslinya) ──────────────────
    onMounted(async () => {
      try {
        const res = await PasienService.getProfil();
        Object.assign(form, res.data.data);
      } catch (e) {
        console.error("Gagal memuat profil", e);
      } finally {
        loadingData.value = false;
      }
      // Baca foto dari localStorage jika ada (hasil crop sebelumnya)
      const key = `puskesmas_foto_${authStore.user?.id ?? 'guest'}`;
      const saved = localStorage.getItem(key);
      if (saved) previewPhoto.value = saved;
    });

    // ══════════════════════════════════════════════════════════════
    // CROP LOGIC
    // ══════════════════════════════════════════════════════════════
    const CANVAS_SIZE = 220;
    const CIRCLE_SIZE = 180;

    const showCrop   = ref(false);
    const cropCanvas = ref<HTMLCanvasElement | null>(null);
    const cropImg    = ref<HTMLImageElement | null>(null);

    const cs = reactive({
      x: 0, y: 0,
      scale: 1, minScale: 1,
      rotation: 0,
      dragging: false, lastX: 0, lastY: 0,
    });

    const rotate = (deg: number) => {
      cs.rotation += deg;
      if (cs.rotation >  180) cs.rotation -= 360;
      if (cs.rotation < -180) cs.rotation += 360;
      draw();
    };

    const draw = () => {
      const c = cropCanvas.value;
      if (!c || !cropImg.value) return;
      const ctx = c.getContext("2d");
      if (!ctx) return;

      c.width = CANVAS_SIZE; c.height = CANVAS_SIZE;

      // Background canvas
      ctx.fillStyle = "#94a3b8";
      ctx.fillRect(0, 0, CANVAS_SIZE, CANVAS_SIZE);

      // Gambar dengan rotasi & scale
      ctx.save();
      ctx.translate(CANVAS_SIZE / 2, CANVAS_SIZE / 2);
      ctx.rotate((cs.rotation * Math.PI) / 180);
      ctx.translate(-CANVAS_SIZE / 2, -CANVAS_SIZE / 2);
      ctx.drawImage(cropImg.value, cs.x, cs.y, cropImg.value.width * cs.scale, cropImg.value.height * cs.scale);
      ctx.restore();

      // Overlay gelap HANYA di luar lingkaran — dalam lingkaran foto tetap cerah
      ctx.save();
      ctx.beginPath();
      // Buat path: persegi panjang penuh DIKURANGI lingkaran (even-odd rule)
      ctx.rect(0, 0, CANVAS_SIZE, CANVAS_SIZE);
      ctx.arc(CANVAS_SIZE / 2, CANVAS_SIZE / 2, CIRCLE_SIZE / 2, 0, Math.PI * 2, true);
      ctx.fillStyle = "rgba(0,0,0,0.55)";
      ctx.fill("evenodd");
      ctx.restore();

      // Border lingkaran
      ctx.save();
      ctx.strokeStyle = "#1B84FF";
      ctx.lineWidth = 2;
      ctx.setLineDash([5, 3]);
      ctx.beginPath();
      ctx.arc(CANVAS_SIZE / 2, CANVAS_SIZE / 2, CIRCLE_SIZE / 2, 0, Math.PI * 2);
      ctx.stroke();
      ctx.restore();
    };

    // ── Saat file dipilih → buka modal crop ───────────────────────
    const onPhotoChange = (e: Event) => {
      const target = e.target as HTMLInputElement;
      if (!target.files || !target.files[0]) return;
      const reader = new FileReader();
      reader.onload = async (ev) => {
        const img = new Image();
        img.onload = async () => {
          cropImg.value = img;
          const minS    = Math.max(CIRCLE_SIZE / img.width, CIRCLE_SIZE / img.height);
          cs.minScale   = minS;
          cs.scale      = minS;
          cs.rotation   = 0;
          cs.x          = (CANVAS_SIZE - img.width  * minS) / 2;
          cs.y          = (CANVAS_SIZE - img.height * minS) / 2;
          showCrop.value = true;
          await nextTick();
          draw();
        };
        img.src = ev.target?.result as string;
      };
      reader.readAsDataURL(target.files[0]);
      target.value = ""; // reset supaya bisa pilih file sama lagi
    };

    // ── Drag ──────────────────────────────────────────────────────
    const startDrag = (e: MouseEvent) => { cs.dragging = true; cs.lastX = e.clientX; cs.lastY = e.clientY; };
    const onDrag    = (e: MouseEvent) => {
      if (!cs.dragging) return;
      cs.x += e.clientX - cs.lastX; cs.y += e.clientY - cs.lastY;
      cs.lastX = e.clientX; cs.lastY = e.clientY;
      draw();
    };
    const stopDrag  = () => { cs.dragging = false; };

    const startDragTouch = (e: TouchEvent) => { cs.dragging = true; cs.lastX = e.touches[0].clientX; cs.lastY = e.touches[0].clientY; };
    const onDragTouch    = (e: TouchEvent) => {
      if (!cs.dragging) return;
      cs.x += e.touches[0].clientX - cs.lastX; cs.y += e.touches[0].clientY - cs.lastY;
      cs.lastX = e.touches[0].clientX; cs.lastY = e.touches[0].clientY;
      draw();
    };

    // ── Apply crop → simpan ke previewPhoto + localStorage + authStore ──
    const applyCrop = () => {
      const c = cropCanvas.value;
      if (!c) return;
      const out    = document.createElement("canvas");
      out.width    = CIRCLE_SIZE;
      out.height   = CIRCLE_SIZE;
      const outCtx = out.getContext("2d");
      if (!outCtx) return;

      // Clip lingkaran
      outCtx.beginPath();
      outCtx.arc(CIRCLE_SIZE / 2, CIRCLE_SIZE / 2, CIRCLE_SIZE / 2, 0, Math.PI * 2);
      outCtx.clip();

      // Ambil area lingkaran dari canvas utama
      const off = (CANVAS_SIZE - CIRCLE_SIZE) / 2;
      outCtx.drawImage(c, off, off, CIRCLE_SIZE, CIRCLE_SIZE, 0, 0, CIRCLE_SIZE, CIRCLE_SIZE);

      const dataUrl = out.toDataURL("image/png");
      previewPhoto.value = dataUrl;

      // Sinkron ke authStore in-memory (langsung tampil di navbar dll)
      if (authStore.user) {
        authStore.user.foto_url = dataUrl;
      }

      // Simpan ke localStorage agar foto tetap ada setelah refresh
      // Key per-user agar tidak bentrok antar akun
      const key = `puskesmas_foto_${authStore.user?.id ?? 'guest'}`;
      localStorage.setItem(key, dataUrl);

      showCrop.value = false;
    };

    const cancelCrop = () => { showCrop.value = false; };

    // ══════════════════════════════════════════════════════════════
    // SIMPAN & GANTI PASSWORD — sama persis dengan aslinya
    // ══════════════════════════════════════════════════════════════
    const simpanProfil = async () => {
      Object.keys(errors).forEach((k) => delete errors[k]);
      loadingSimpan.value = true;
      try {
        const payload = {
          nama_lengkap  : form.nama_lengkap,
          nik           : form.nik,
          jenis_kelamin : form.jenis_kelamin,
          no_telepon    : form.no_telepon,
          email         : form.email,
          alamat        : form.alamat,
          golongan_darah: form.golongan_darah,
          no_bpjs       : form.no_bpjs,
          tanggal_lahir : form.tanggal_lahir,
        };
        await PasienService.updateProfil(payload);
        await authStore.fetchUser();
        showAlert("Profil berhasil diperbarui!");
      } catch (err: any) {
        if (err.response?.data?.errors) {
          const serverErrors = err.response.data.errors;
          Object.keys(serverErrors).forEach((key) => {
            errors[key] = Array.isArray(serverErrors[key]) ? serverErrors[key][0] : serverErrors[key];
          });
        } else {
          showAlert(err.response?.data?.message ?? "Gagal menyimpan profil", "danger");
        }
      } finally {
        loadingSimpan.value = false;
      }
    };

    const gantiPassword = async () => {
      Object.keys(passwordErrors).forEach((k) => delete passwordErrors[k]);
      let valid = true;
      if (!passwordForm.password_lama) { passwordErrors.password_lama = "Password lama wajib diisi"; valid = false; }
      if (!passwordForm.password_baru || passwordForm.password_baru.length < 8) { passwordErrors.password_baru = "Password baru minimal 8 karakter"; valid = false; }
      if (passwordForm.password_baru !== passwordForm.konfirmasi_password) { passwordErrors.konfirmasi_password = "Konfirmasi password tidak cocok"; valid = false; }
      if (!valid) return;

      loadingPassword.value = true;
      try {
        await PasienService.gantiPassword(passwordForm);
        passwordForm.password_lama = "";
        passwordForm.password_baru = "";
        passwordForm.konfirmasi_password = "";
        showAlert("Password berhasil diubah!");
      } catch (err: any) {
        if (err.response?.data?.errors) {
          const serverErrors = err.response.data.errors;
          Object.keys(serverErrors).forEach((key) => {
            passwordErrors[key] = Array.isArray(serverErrors[key]) ? serverErrors[key][0] : serverErrors[key];
          });
        } else if (err.response?.data?.message) {
          passwordErrors.password_lama = err.response.data.message;
        } else {
          showAlert("Gagal mengganti password", "danger");
        }
      } finally {
        loadingPassword.value = false;
      }
    };

    return {
      authStore,
      form, errors, alertMsg, loadingSimpan, loadingData,
      previewPhoto, onPhotoChange, simpanProfil,
      passwordForm, passwordErrors, loadingPassword,
      showPasswordLama, showPasswordBaru, showKonfirmasi, gantiPassword,
      // crop
      showCrop, cropCanvas, cs, draw, rotate,
      startDrag, onDrag, stopDrag,
      startDragTouch, onDragTouch,
      applyCrop, cancelCrop,
    };
  },
});
</script>

<style scoped>
/* ══ AVATAR LINGKARAN ══ */
.av-wrap {
  position: relative;
  width: 110px;
  height: 110px;
}
.av-circle {
  width: 110px;
  height: 110px;
  border-radius: 50%;
  overflow: hidden;
  background: #1B84FF;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(0,0,0,.15);
  border: 3px solid #e9ecef;
}

.av-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
}
.av-initial {
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  user-select: none;
}
.av-edit-btn {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #1B84FF;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 8px rgba(0,0,0,.2);
  transition: background .15s;
}
.av-edit-btn:hover { background: #0d6efd; }

/* ══ MODAL CROP ══ */
.crop-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.65);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  backdrop-filter: blur(3px);
}
.crop-modal {
  background: #fff;
  border-radius: 14px;
  width: 100%;
  max-width: 560px;
  box-shadow: 0 24px 64px rgba(0,0,0,.35);
  overflow: hidden;
  animation: cropIn .18s ease;
}
@keyframes cropIn {
  from { opacity: 0; transform: scale(.95); }
  to   { opacity: 1; transform: none; }
}

/* Header */
.crop-hd {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  border-bottom: 1px solid #f1f3f5;
}
.crop-hd-title { font-size: 14px; font-weight: 700; color: #212529; }
.crop-hd-close {
  background: none; border: none; font-size: 22px;
  color: #adb5bd; cursor: pointer; line-height: 1; padding: 0 3px;
  transition: color .15s;
}
.crop-hd-close:hover { color: #495057; }

/* Body */
.crop-bd {
  display: flex;
  gap: 14px;
  padding: 16px 18px;
  align-items: flex-start;
}

/* Canvas kolom kiri */
.crop-canvas-col {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}
.crop-canvas {
  display: block;
  width: 220px;
  height: 220px;
  border-radius: 10px;
  cursor: grab;
  user-select: none;
}
.crop-canvas:active { cursor: grabbing; }
.crop-hint {
  font-size: 11px;
  color: #adb5bd;
  margin: 0;
  text-align: center;
}

/* Controls kolom kanan */
.crop-ctrl-col {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 12px;
  min-width: 0;
}
.crop-field { display: flex; flex-direction: column; gap: 4px; }
.crop-lbl {
  font-size: 11px;
  font-weight: 700;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: .5px;
}
.crop-val {
  font-size: 11px;
  color: #1B84FF;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0;
}
.crop-range {
  width: 100%;
  accent-color: #1B84FF;
  cursor: pointer;
}
.crop-ends {
  display: flex;
  justify-content: space-between;
  font-size: 10px;
  color: #ced4da;
}
.crop-rot-btns { display: flex; gap: 6px; margin-top: 4px; }
.crop-rbtn {
  flex: 1;
  padding: 4px 6px;
  font-size: 11px;
  font-weight: 600;
  border: 1.5px solid #dee2e6;
  border-radius: 6px;
  background: #f8f9fa;
  color: #495057;
  cursor: pointer;
  white-space: nowrap;
  transition: all .15s;
}
.crop-rbtn:hover { background: #e9ecef; border-color: #adb5bd; }
.crop-info {
  font-size: 11px;
  color: #adb5bd;
  background: #f8f9fa;
  border-radius: 7px;
  padding: 8px 10px;
}
.crop-actions { margin-top: auto; }

/* Responsive */
@media (max-width: 500px) {
  .crop-bd { flex-direction: column; align-items: center; }
  .crop-ctrl-col { width: 100%; }
}
</style>
