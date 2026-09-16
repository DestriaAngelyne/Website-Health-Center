<template>
  <div class="app-navbar-item ms-1 ms-md-3">
    <div
      class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px position-relative"
      data-kt-menu-trigger="{default:'click', lg: 'hover'}"
      data-kt-menu-attach="parent"
      data-kt-menu-placement="bottom-end"
      @click="onOpen"
    >
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="20" height="20">
        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
      </svg>
      <span
        v-if="unread > 0"
        class="badge badge-circle badge-danger position-absolute"
        style="top:0;right:0;min-width:16px;height:16px;font-size:9px;padding:0;display:flex;align-items:center;justify-content:center;"
      >{{ unread > 9 ? '9+' : unread }}</span>
    </div>

    <div
      class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg fw-semibold py-3 fs-6 w-300px w-md-350px"
      data-kt-menu="true"
    >
      <div class="d-flex align-items-center justify-content-between px-5 pb-2">
        <span class="fw-bold fs-6">Notifikasi</span>
        <a v-if="unread > 0" class="fs-8 text-primary cursor-pointer" @click="tandaiSemua">Tandai semua dibaca</a>
      </div>
      <div class="separator mb-2"></div>

      <div style="max-height:340px;overflow-y:auto;">
        <div v-if="loading" class="text-center py-6 text-muted fs-7">Memuat...</div>
        <div v-else-if="list.length === 0" class="text-center py-6 text-muted fs-7">Belum ada notifikasi</div>

        <router-link
          v-for="n in list"
          :key="n.id"
          :to="n.url || '#'"
          class="menu-item px-3"
          @click="klik(n)"
        >
          <div class="menu-content px-3 py-2 d-flex align-items-start gap-2" :class="{ 'opacity-50': n.dibaca_at }">
            <span class="bullet bullet-dot mt-2" :class="n.dibaca_at ? 'bg-secondary' : 'bg-primary'"></span>
            <div class="flex-grow-1">
              <div class="fw-bold fs-7">{{ n.judul }}</div>
              <div class="fs-8 text-muted">{{ n.pesan }}</div>
              <div class="fs-9 text-muted mt-1">{{ waktu(n.created_at) }}</div>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, onUnmounted } from "vue";
import { NotifikasiService } from "@/services/notifikasi.service";

interface NotifikasiItem {
  id: number;
  judul: string;
  pesan: string;
  url: string | null;
  dibaca_at: string | null;
  created_at: string;
}

export default defineComponent({
  name: "notifikasi-dropdown",
  setup() {
    const list    = ref<NotifikasiItem[]>([]);
    const unread   = ref(0);
    const loading  = ref(false);
    let polling: ReturnType<typeof setInterval> | undefined;

    async function load() {
      try {
        const res = await NotifikasiService.getAll();
        list.value   = res.data.data;
        unread.value = res.data.unread;
      } catch (e) {
        // diam-diam gagal, tidak mengganggu UX utama
      }
    }

    async function onOpen() {
      loading.value = true;
      await load();
      loading.value = false;
    }

    async function klik(n: NotifikasiItem) {
      if (!n.dibaca_at) {
        try {
          await NotifikasiService.tandaiBaca(n.id);
          n.dibaca_at = new Date().toISOString();
          unread.value = Math.max(0, unread.value - 1);
        } catch (e) { /* diam-diam gagal */ }
      }
    }

    async function tandaiSemua() {
      try {
        await NotifikasiService.tandaiSemuaBaca();
        list.value.forEach(n => n.dibaca_at = n.dibaca_at || new Date().toISOString());
        unread.value = 0;
      } catch (e) { /* diam-diam gagal */ }
    }

    function waktu(iso: string) {
      const d = new Date(iso);
      const diffMin = Math.floor((Date.now() - d.getTime()) / 60000);
      if (diffMin < 1) return "Baru saja";
      if (diffMin < 60) return `${diffMin} menit lalu`;
      const diffJam = Math.floor(diffMin / 60);
      if (diffJam < 24) return `${diffJam} jam lalu`;
      return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    }

    onMounted(() => {
      load();
      polling = setInterval(load, 30000);
    });

    onUnmounted(() => {
      if (polling) clearInterval(polling);
    });

    return { list, unread, loading, onOpen, klik, tandaiSemua, waktu };
  },
});
</script>
