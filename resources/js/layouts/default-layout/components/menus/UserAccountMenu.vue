<template>
  <!--begin::Menu-->
  <div
    class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold py-4 fs-6 w-275px"
    data-kt-menu="true"
  >
    <!--begin::Menu item-->
    <div class="menu-item px-3">
      <div class="menu-content d-flex align-items-center px-3">
        <!--begin::Avatar-->
        <div class="symbol symbol-50px me-5">
          <template v-if="authStore.fotoUrl">
            <img
              :src="authStore.fotoUrl"
              alt="Foto Profil"
              style="width:50px;height:50px;object-fit:cover;border-radius:50%;display:block;"
            />
          </template>
          <div v-else
            class="symbol-label fw-bold fs-3 text-white"
            :style="`background:${avatarBg};border-radius:50%;width:50px;height:50px;display:flex;align-items:center;justify-content:center;`"
          >
            {{ userInitial }}
          </div>
        </div>
        <!--end::Avatar-->

        <!--begin::Username-->
        <div class="d-flex flex-column">
          <div class="fw-bold d-flex align-items-center fs-5">
            {{ authStore.user?.name ?? 'Pengguna' }}
          </div>
          <span class="fw-semibold text-muted fs-7">
            {{ authStore.user?.email ?? '' }}
          </span>
          <span class="badge badge-light-primary fw-bold fs-8 px-2 py-1 mt-1 align-self-start">
            {{ roleLabel }}
          </span>
        </div>
        <!--end::Username-->
      </div>
    </div>
    <!--end::Menu item-->

    <!--begin::Menu separator-->
    <div class="separator my-2"></div>
    <!--end::Menu separator-->

    <!--begin::Menu item - Edit Profil (semua role)-->
    <div class="menu-item px-5">
      <router-link :to="editProfilRoute" class="menu-link px-5">
        <span class="menu-icon">
          <i class="ki-duotone ki-pencil fs-2">
            <span class="path1"></span><span class="path2"></span>
          </i>
        </span>
        Edit Profil
      </router-link>
    </div>
    <!--end::Menu item-->

    <!--begin::Menu separator-->
    <div class="separator my-2"></div>
    <!--end::Menu separator-->

    <!--begin::Menu item - Sign Out-->
    <div class="menu-item px-5">
      <a @click="signOut()" class="menu-link px-5" style="cursor:pointer;">
        <span class="menu-icon">
          <i class="ki-duotone ki-exit-right fs-2">
            <span class="path1"></span><span class="path2"></span>
          </i>
        </span>
        Keluar
      </a>
    </div>
    <!--end::Menu item-->
  </div>
  <!--end::Menu-->
</template>

<script lang="ts">
import { computed, defineComponent } from "vue";
import { useAuthStore } from "@/stores/auth.store";
import { useRouter } from "vue-router";

export default defineComponent({
  name: "kt-user-menu",
  setup() {
    const router    = useRouter();
    const authStore = useAuthStore();

    const signOut = async () => {
      await authStore.logout();
    };

    const userInitial = computed(() =>
      authStore.user?.name?.charAt(0).toUpperCase() ?? 'U'
    );

    const avatarBg = computed(() => {
      const map: Record<string, string> = {
        admin  : '#7239ea',
        loket  : '#0d6efd',
        perawat: '#20c997',
        dokter : '#0dcaf0',
        pasien : '#1B84FF',
      };
      return map[authStore.user?.role ?? ''] ?? '#1B84FF';
    });

    const roleLabel = computed(() => {
      const map: Record<string, string> = {
        admin  : 'Administrator',
        loket  : 'Petugas Loket',
        perawat: 'Perawat',
        dokter : 'Dokter',
        pasien : 'Pasien',
      };
      return map[authStore.user?.role ?? ''] ?? '-';
    });

    // Route edit profil berdasarkan role
    const editProfilRoute = computed(() => {
      const map: Record<string, string> = {
        admin  : '/admin/profil/edit',
        loket  : '/loket/profil/edit',
        perawat: '/perawat/profil/edit',
        dokter : '/dokter/profil/edit',
        pasien : '/pasien/profil/edit',
      };
      return map[authStore.user?.role ?? ''] ?? '/';
    });

    return {
      authStore,
      userInitial,
      avatarBg,
      roleLabel,
      editProfilRoute,
      signOut,
    };
  },
});
</script>
