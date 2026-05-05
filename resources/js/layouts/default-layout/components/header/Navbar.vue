<template>
  <!--begin::Navbar-->
  <div class="app-navbar flex-shrink-0">

    <!--begin::Theme mode-->
    <div class="app-navbar-item ms-1 ms-md-3">
      <button
        type="button"
        class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px"
        data-kt-menu-trigger="{default:'click', lg: 'hover'}"
        data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end"
      >
        <KTIcon v-if="themeMode === 'light'" icon-name="night-day" icon-class="fs-2" />
        <KTIcon v-else icon-name="moon" icon-class="fs-2" />
      </button>
      <KTThemeModeSwitcher />
    </div>
    <!--end::Theme mode-->

    <!--begin::User menu-->
    <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
      <div
        class="cursor-pointer d-flex align-items-center gap-3"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
        data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end"
      >
        <!--begin::Avatar trigger-->
        <div class="symbol symbol-35px">
          <template v-if="authStore.fotoUrl">
            <img
              :src="authStore.fotoUrl"
              alt="Foto Profil"
              style="width:35px;height:35px;object-fit:cover;border-radius:50%;display:block;"
            />
          </template>
          <div v-else
            class="symbol-label fs-6 fw-bold text-white"
            style="background:#0ea5e9;">
            {{ userInitial }}
          </div>
        </div>
        <!--end::Avatar trigger-->

        <!--begin::Info (desktop only)-->
        <div class="d-none d-md-flex flex-column text-end">
          <span class="fw-bold text-gray-800 fs-7 lh-1">{{ userName }}</span>
          <span class="badge badge-light-primary fs-8 mt-1 text-capitalize">{{ userRole }}</span>
        </div>
        <!--end::Info-->
      </div>

      <!--begin::User dropdown menu-->
      <div
        class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
        data-kt-menu="true"
      >
        <!--begin::Menu item - User info-->
        <div class="menu-item px-3">
          <div class="menu-content d-flex align-items-center px-3">
            <!--begin::Avatar dropdown-->
            <div class="symbol symbol-50px me-5">
              <template v-if="authStore.fotoUrl">
                <img
                  :src="authStore.fotoUrl"
                  alt="Foto Profil"
                  style="width:50px;height:50px;object-fit:cover;border-radius:50%;display:block;"
                />
              </template>
              <div v-else
                class="symbol-label fs-4 fw-bold text-white"
                style="background:#0ea5e9;">
                {{ userInitial }}
              </div>
            </div>
            <!--end::Avatar dropdown-->

            <div class="d-flex flex-column">
              <div class="fw-bold d-flex align-items-center fs-5">{{ authStore.user?.name }}</div>
              <span class="fw-semibold text-muted text-hover-primary fs-7">{{ authStore.user?.email }}</span>
              <span class="badge badge-light-primary fs-8 mt-1 text-capitalize">{{ userRole }}</span>
            </div>
          </div>
        </div>
        <!--end::Menu item-->

        <div class="separator my-2"></div>

        <!--begin::Menu item - Edit Profil-->
        <div class="menu-item px-5">
          <router-link :to="editProfileRoute" class="menu-link px-5">
            <span class="menu-icon">
              <KTIcon icon-name="pencil" icon-class="fs-4" />
            </span>
            Edit Profil
          </router-link>
        </div>
        <!--end::Menu item-->

        <div class="separator my-2"></div>

        <!--begin::Menu item - Logout-->
        <div class="menu-item px-5">
          <a @click.prevent="handleLogout" class="menu-link px-5 text-danger cursor-pointer">
            <span class="menu-icon">
              <KTIcon icon-name="exit-right" icon-class="fs-4 text-danger" />
            </span>
            Keluar
          </a>
        </div>
        <!--end::Menu item-->
      </div>
      <!--end::User dropdown menu-->
    </div>
    <!--end::User menu-->

    <!--begin::Header menu toggle (mobile)-->
    <div class="app-navbar-item d-lg-none ms-2 me-n2" v-tooltip title="Show header menu">
      <div
        class="btn btn-flex btn-icon btn-active-color-primary w-30px h-30px"
        id="kt_app_header_menu_toggle"
      >
        <KTIcon icon-name="element-4" icon-class="fs-2" />
      </div>
    </div>
    <!--end::Header menu toggle-->

  </div>
  <!--end::Navbar-->
</template>

<script lang="ts">
import { computed, defineComponent } from "vue";
import { useAuthStore } from "@/stores/auth.store";
import KTThemeModeSwitcher from "@/layouts/default-layout/components/theme-mode/ThemeModeSwitcher.vue";
import { ThemeModeComponent } from "@/assets/ts/layout";
import { useThemeStore } from "@/stores/theme";

export default defineComponent({
  name: "header-navbar",
  components: {
    KTThemeModeSwitcher,
  },
  setup() {
    const themeStore = useThemeStore();
    const authStore  = useAuthStore();

    const themeMode = computed(() => {
      if (themeStore.mode === "system") return ThemeModeComponent.getSystemMode();
      return themeStore.mode;
    });

    const userName = computed(() => authStore.user?.name ?? "Pengguna");

    const userInitial = computed(() =>
      authStore.user?.name?.charAt(0).toUpperCase() ?? "U"
    );

    const userRole = computed(() => {
      const roles: Record<string, string> = {
        admin  : "Administrator",
        loket  : "Petugas Loket",
        perawat: "Perawat",
        dokter : "Dokter",
        pasien : "Pasien",
      };
      return roles[authStore.user?.role ?? ""] ?? authStore.user?.role ?? "-";
    });

    const profileRoute = computed(() => {
      const routes: Record<string, string> = {
        admin  : "/admin/profil",
        loket  : "/loket/profil",
        perawat: "/perawat/profil",
        dokter : "/dokter/profil",
        pasien : "/pasien/profil",
      };
      return routes[authStore.user?.role ?? ""] ?? "/";
    });

    const editProfileRoute = computed(() => {
      const routes: Record<string, string> = {
        admin  : "/admin/profil/edit",
        loket  : "/loket/profil/edit",
        perawat: "/perawat/profil/edit",
        dokter : "/dokter/profil/edit",
        pasien : "/pasien/profil/edit",
      };
      return routes[authStore.user?.role ?? ""] ?? "/";
    });

    async function handleLogout() {
      await authStore.logout();
    }

    return {
      themeMode,
      authStore,
      userName,
      userInitial,
      userRole,
      profileRoute,
      editProfileRoute,
      handleLogout,
    };
  },
});
</script>
