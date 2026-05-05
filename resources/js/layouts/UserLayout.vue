<template>
  <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

      <!--begin::Header-->
      <div id="kt_app_header" class="app-header"
        :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'}; border-bottom: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'};`">
        <div class="app-container container-fluid d-flex align-items-center justify-content-between">

          <!--begin::Logo-->
          <div class="d-flex align-items-center gap-2">
            <span class="fw-bold fs-3 text-primary">MyApp</span>
          </div>
          <!--end::Logo-->

          <!--begin::Search-->
          <div class="d-flex align-items-center" style="flex: 1; max-width: 400px; margin: 0 40px;">
            <div class="position-relative w-100">
              <i class="ki-duotone ki-magnifier fs-2 position-absolute"
                style="left: 0; top: 50%; transform: translateY(-50%); color: #565674; z-index: 1;">
                <span class="path1"></span><span class="path2"></span>
              </i>
              <input
                type="text"
                v-model="searchQuery"
                @focus="searchFocus = true"
                @blur="handleSearchBlur"
                @input="handleSearch"
                placeholder="Search..."
                class="search-input"
                :class="[searchFocus ? 'search-input-focus' : '', isDark ? 'search-input-dark' : 'search-input-light']"
              />

              <!--begin::Search Results-->
              <div v-show="searchFocus && searchQuery.length > 0 && searchResults.length > 0"
                :style="`position: absolute; top: 44px; left: 0; right: 0; background: ${isDark ? '#1e1e2d' : '#ffffff'}; border: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'}; border-radius: 10px; z-index: 999; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;`">
                <div v-for="result in searchResults" :key="result.path"
                  class="search-result-item" @mousedown="goTo(result.path)">
                  <i :class="`ki-duotone ki-${result.icon} fs-3 text-primary`">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                  </i>
                  <div>
                    <div class="fw-semibold fs-7" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">{{ result.name }}</div>
                    <div class="fs-8" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">{{ result.section }}</div>
                  </div>
                </div>
              </div>
              <!--end::Search Results-->

              <!--begin::No Results-->
              <div v-show="searchFocus && searchQuery.length > 0 && searchResults.length === 0"
                :style="`position: absolute; top: 44px; left: 0; right: 0; background: ${isDark ? '#1e1e2d' : '#ffffff'}; border: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'}; border-radius: 10px; z-index: 999; padding: 20px 16px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.15);`">
                <i class="ki-duotone ki-information-5 fs-2x text-gray-400 mb-2">
                  <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                </i>
                <div class="fw-semibold fs-7" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">
                  No results for "{{ searchQuery }}"
                </div>
              </div>
              <!--end::No Results-->

            </div>
          </div>
          <!--end::Search-->

          <!--begin::Navbar-->
          <div class="d-flex align-items-center gap-2">

            <!--begin::Notifications-->
            <div class="position-relative">
              <button class="header-icon-btn" @click="toggleNotif"
                :style="`background: ${isDark ? '#2d2d3f' : '#f5f8fa'};`">
                <i class="ki-duotone ki-bell fs-2" :class="isDark ? 'text-gray-400' : 'text-gray-600'">
                  <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                </i>
              </button>
              <span class="notif-badge" :style="`border-color: ${isDark ? '#1e1e2d' : '#ffffff'};`"></span>

              <!--begin::Notif Dropdown-->
              <div v-show="notifOpen" class="dropdown-box"
                :style="`width: 320px; right: 0; top: 48px; background: ${isDark ? '#1e1e2d' : '#ffffff'}; border-color: ${isDark ? '#2d2d3f' : '#eff2f5'};`">
                <div class="d-flex align-items-center justify-content-between p-5"
                  :style="`border-bottom: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'};`">
                  <div class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Notifications</div>
                  <span class="badge badge-light-primary fw-semibold">3 New</span>
                </div>
                <div class="p-4">
                  <div class="d-flex align-items-start gap-3 mb-4">
                    <span class="notif-dot" style="background: #009ef7;"></span>
                    <div>
                      <div class="fw-semibold fs-7" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Profile updated successfully</div>
                      <div class="fs-8 mt-1 text-gray-500">Just now</div>
                    </div>
                  </div>
                  <div class="separator separator-dashed mb-4"></div>
                  <div class="d-flex align-items-start gap-3 mb-4">
                    <span class="notif-dot" style="background: #50cd89;"></span>
                    <div>
                      <div class="fw-semibold fs-7" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Account verified</div>
                      <div class="fs-8 mt-1 text-gray-500">1 day ago</div>
                    </div>
                  </div>
                  <div class="separator separator-dashed mb-4"></div>
                  <div class="d-flex align-items-start gap-3">
                    <span class="notif-dot" style="background: #ffc700;"></span>
                    <div>
                      <div class="fw-semibold fs-7" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Welcome to the platform!</div>
                      <div class="fs-8 mt-1 text-gray-500">2024</div>
                    </div>
                  </div>
                </div>
                <div class="p-4 text-center" :style="`border-top: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'};`">
                  <a href="#" class="fw-semibold text-primary fs-7">View All Notifications</a>
                </div>
              </div>
              <!--end::Notif Dropdown-->
            </div>
            <!--end::Notifications-->

            <!--begin::User Dropdown-->
            <div class="position-relative">
              <div class="d-flex align-items-center gap-2 user-badge" @click="toggleUserMenu"
                :style="`background: ${isDark ? '#2d2d3f' : '#f5f8fa'};`">
                <div class="user-avatar">{{ userStore.user.name?.charAt(0).toUpperCase() }}</div>
                <div class="d-none d-md-block">
                  <div class="fw-bold fs-7 lh-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">{{ userStore.user.name }}</div>
                  <div class="fs-8 text-capitalize text-gray-500">{{ userStore.user.role }}</div>
                </div>
                <i class="ki-duotone ki-down fs-7 ms-1 text-gray-500">
                  <span class="path1"></span><span class="path2"></span>
                </i>
              </div>

              <!--begin::User Menu-->
              <div v-show="userMenuOpen" class="dropdown-box"
                :style="`width: 230px; right: 0; top: 52px; padding: 8px; background: ${isDark ? '#1e1e2d' : '#ffffff'}; border-color: ${isDark ? '#2d2d3f' : '#eff2f5'};`">

                <div class="px-4 py-3 mb-1" :style="`border-bottom: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'};`">
                  <div class="fw-bold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">{{ userStore.user.name }}</div>
                  <div class="fs-7 text-gray-500">{{ userStore.user.email }}</div>
                </div>

                <router-link to="/user/profile/overview" class="dropdown-user-item"
                  :class="isDark ? 'dropdown-item-dark' : 'dropdown-item-light'" @click="userMenuOpen = false">
                  <i class="ki-duotone ki-profile-circle fs-4 text-primary">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                  </i>
                  My Profile
                </router-link>

                <router-link to="/user/profile/edit" class="dropdown-user-item"
                  :class="isDark ? 'dropdown-item-dark' : 'dropdown-item-light'" @click="userMenuOpen = false">
                  <i class="ki-duotone ki-pencil fs-4 text-warning">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                  Edit Profile
                </router-link>

                <router-link to="/user/dashboard" class="dropdown-user-item"
                  :class="isDark ? 'dropdown-item-dark' : 'dropdown-item-light'" @click="userMenuOpen = false">
                  <i class="ki-duotone ki-home-2 fs-4 text-info">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                  Dashboard
                </router-link>

                <div :style="`border-top: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'}; margin: 6px 0;`"></div>

                <!--begin::Dark Mode-->
                <div class="d-flex align-items-center justify-content-between px-3 py-2 rounded mb-2"
                  :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                  <div class="d-flex align-items-center gap-2 fw-semibold fs-7">
                    <i class="ki-duotone ki-moon fs-4"><span class="path1"></span><span class="path2"></span></i>
                    Dark Mode
                  </div>
                  <div class="form-check form-switch mb-0" style="padding-left: 0;">
                    <input class="form-check-input" type="checkbox" :checked="isDark" @change="toggleTheme"
                      style="width: 40px; height: 22px; cursor: pointer; margin: 0;" />
                  </div>
                </div>
                <!--end::Dark Mode-->

                <div :style="`border-top: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'}; margin: 6px 0 8px;`"></div>

                <div class="px-1">
                  <button @click="onLogout" class="btn w-100 fw-bold fs-6"
                    :style="`background: ${isDark ? '#2d2d3f' : '#f1f1f4'}; color: ${isDark ? '#ffffff' : '#181c32'}; border: none; border-radius: 8px; padding: 10px; transition: background 0.2s;`"
                    @mouseover="e => (e.target as HTMLElement).style.background = isDark ? '#3d3d5f' : '#e4e6ef'"
                    @mouseleave="e => (e.target as HTMLElement).style.background = isDark ? '#2d2d3f' : '#f1f1f4'">
                    Logout
                  </button>
                </div>

              </div>
              <!--end::User Menu-->
            </div>
            <!--end::User Dropdown-->

          </div>
          <!--end::Navbar-->

        </div>
      </div>
      <!--end::Header-->

      <!--begin::Wrapper-->
      <div style="display: flex; flex-direction: row; flex: 1;">

        <!--begin::Sidebar-->
        <div :style="`width: 265px; min-height: calc(100vh - 65px); background: ${isDark ? '#1e1e2d' : '#ffffff'}; padding: 16px 12px; flex-shrink: 0; border-right: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'}; transition: background 0.3s;`">

          <!--begin::Dashboard-->
          <div style="margin-bottom: 4px;">
            <router-link to="/user/dashboard" class="user-menu-link" active-class="user-menu-link-active"
              :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
              <i class="ki-duotone ki-home-2 fs-2"><span class="path1"></span><span class="path2"></span></i>
              <span>Dashboard</span>
            </router-link>
          </div>
          <!--end::Dashboard-->

          <!--begin::Pages Label-->
          <div class="sidebar-label" :style="`color: ${isDark ? '#565674' : '#b5b5c3'};`">Pages</div>
          <!--end::Pages Label-->

          <!--begin::User Profile-->
          <div>
            <div class="user-menu-link justify-content-between" style="cursor: pointer;"
              :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`" @click="toggleProfile">
              <div class="d-flex align-items-center gap-3">
                <i class="ki-duotone ki-profile-user fs-2">
                  <span class="path1"></span><span class="path2"></span>
                  <span class="path3"></span><span class="path4"></span>
                </i>
                <span>User Profile</span>
              </div>
              <i :class="profileOpen ? 'ki-up' : 'ki-down'" class="ki-duotone fs-5">
                <span class="path1"></span><span class="path2"></span>
              </i>
            </div>
            <div v-show="profileOpen" style="padding-left: 16px; margin-top: 2px;">
              <router-link to="/user/profile/overview" class="sidebar-sub-item" active-class="sidebar-sub-item-active" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Overview</span>
              </router-link>
              <router-link to="/user/profile/projects" class="sidebar-sub-item" active-class="sidebar-sub-item-active" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Projects</span>
              </router-link>
              <router-link to="/user/profile/campaigns" class="sidebar-sub-item" active-class="sidebar-sub-item-active" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Campaigns</span>
              </router-link>
              <router-link to="/user/profile/documents" class="sidebar-sub-item" active-class="sidebar-sub-item-active" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Documents</span>
              </router-link>
              <router-link to="/user/profile/followers" class="sidebar-sub-item" active-class="sidebar-sub-item-active" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Followers</span>
              </router-link>
              <router-link to="/user/profile/activity" class="sidebar-sub-item" active-class="sidebar-sub-item-active" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Activity</span>
              </router-link>
              <router-link to="/user/profile/edit" class="sidebar-sub-item" active-class="sidebar-sub-item-active" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Edit Profile</span>
              </router-link>
            </div>
          </div>
          <!--end::User Profile-->

          <!--begin::Support Label-->
          <div class="sidebar-label" :style="`color: ${isDark ? '#565674' : '#b5b5c3'}; margin-top: 8px;`">Support</div>
          <!--end::Support Label-->

          <!--begin::Messages-->
          <div style="margin-bottom: 4px;">
            <router-link to="/user/messages" class="user-menu-link" active-class="user-menu-link-active"
              :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
              <i class="ki-duotone ki-message-text-2 fs-2">
                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
              </i>
              <span class="flex-grow-1">Messages</span>
              <span style="background: #f64e60; color: #fff; font-size: 10px; font-weight: 700; min-width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</span>
            </router-link>
          </div>
          <!--end::Messages-->

          <!--begin::Help & Support-->
          <div>
            <div class="user-menu-link justify-content-between" style="cursor: pointer;"
              :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`" @click="toggleHelp">
              <div class="d-flex align-items-center gap-3">
                <i class="ki-duotone ki-information-5 fs-2">
                  <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                </i>
                <span>Help & Support</span>
              </div>
              <i :class="helpOpen ? 'ki-up' : 'ki-down'" class="ki-duotone fs-5">
                <span class="path1"></span><span class="path2"></span>
              </i>
            </div>
            <div v-show="helpOpen" style="padding-left: 16px; margin-top: 2px;">
              <router-link to="/user/help" class="sidebar-sub-item" active-class="sidebar-sub-item-active"
                :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>FAQ</span>
              </router-link>
              <router-link to="/user/help" class="sidebar-sub-item" active-class="sidebar-sub-item-active"
                :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Contact Us</span>
              </router-link>
              <router-link to="/user/help" class="sidebar-sub-item" active-class="sidebar-sub-item-active"
                :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
                <span class="sidebar-dot"></span><span>Documentation</span>
              </router-link>
            </div>
          </div>
          <!--end::Help & Support-->

        </div>
        <!--end::Sidebar-->

        <!--begin::Main Content-->
        <div :style="`flex: 1; padding: 30px; overflow-y: auto; background: ${isDark ? '#151521' : '#f5f8fa'}; transition: background 0.3s;`">
          <router-view />
        </div>
        <!--end::Main Content-->

      </div>
      <!--end::Wrapper-->

    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, provide } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";

export default defineComponent({
  name: "user-layout",
  setup() {
    const userStore    = useAuthStore();
    const router       = useRouter();
    const profileOpen  = ref(true);
    const helpOpen     = ref(false);
    const notifOpen    = ref(false);
    const userMenuOpen = ref(false);
    const isDark       = ref(true);
    const searchQuery  = ref("");
    const searchFocus  = ref(false);

    provide("isDark", isDark);

    const allMenus = [
      { name: "Dashboard",     section: "Main",         path: "/user/dashboard",         icon: "home-2"         },
      { name: "Overview",      section: "User Profile", path: "/user/profile/overview",  icon: "profile-circle" },
      { name: "Projects",      section: "User Profile", path: "/user/profile/projects",  icon: "code"           },
      { name: "Campaigns",     section: "User Profile", path: "/user/profile/campaigns", icon: "chart-simple"   },
      { name: "Documents",     section: "User Profile", path: "/user/profile/documents", icon: "document"       },
      { name: "Followers",     section: "User Profile", path: "/user/profile/followers", icon: "people"         },
      { name: "Activity",      section: "User Profile", path: "/user/profile/activity",  icon: "time"           },
      { name: "Edit Profile",  section: "User Profile", path: "/user/profile/edit",      icon: "pencil"         },
      { name: "Messages",      section: "Support",      path: "/user/messages",          icon: "message-text-2" },
      { name: "Help & Support",section: "Support",      path: "/user/help",              icon: "information-5"  },
    ];

    const searchResults = ref<typeof allMenus>([]);

    const handleSearch = () => {
      if (searchQuery.value.trim() === "") { searchResults.value = []; return; }
      const q = searchQuery.value.toLowerCase();
      searchResults.value = allMenus.filter(m =>
        m.name.toLowerCase().includes(q) ||
        m.section.toLowerCase().includes(q)
      );
    };

    const handleSearchBlur = () => {
      setTimeout(() => {
        searchFocus.value   = false;
        searchQuery.value   = "";
        searchResults.value = [];
      }, 150);
    };

    const goTo = (path: string) => {
      router.push(path);
      searchQuery.value   = "";
      searchResults.value = [];
      searchFocus.value   = false;
    };

    const toggleProfile  = () => { profileOpen.value  = !profileOpen.value; };
    const toggleHelp     = () => { helpOpen.value     = !helpOpen.value; };
    const toggleNotif    = () => { notifOpen.value    = !notifOpen.value;    userMenuOpen.value = false; };
    const toggleUserMenu = () => { userMenuOpen.value = !userMenuOpen.value; notifOpen.value    = false; };
    const toggleTheme    = () => { isDark.value       = !isDark.value; };

    const onLogout = () => {
      userStore.logout();
      router.push({ name: "sign-in" });
    };

    return {
      userStore, onLogout,
      profileOpen, toggleProfile,
      helpOpen,    toggleHelp,
      notifOpen,   toggleNotif,
      userMenuOpen, toggleUserMenu,
      isDark,      toggleTheme,
      searchQuery, searchFocus, searchResults,
      handleSearch, handleSearchBlur, goTo,
    };
  },
});
</script>

<style>
.app-header {
  height: 65px;
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  transition: background 0.3s, border-color 0.3s;
}

.search-input {
  width: 100%;
  height: 38px;
  background: transparent;
  border: none;
  border-bottom: 2px solid #2d2d3f;
  border-radius: 0;
  font-size: 14px;
  font-weight: 500;
  padding-left: 32px;
  outline: none;
  transition: border-color 0.25s, color 0.25s;
}
.search-input-dark             { color: #ffffff; }
.search-input-dark::placeholder  { color: #565674; }
.search-input-light            { color: #181c32; border-bottom-color: #eff2f5; }
.search-input-light::placeholder { color: #b5b5c3; }
.search-input-focus            { border-bottom-color: #009ef7 !important; }

.search-result-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  cursor: pointer;
  transition: background 0.15s;
}
.search-result-item:hover { background: rgba(0,158,247,0.08); }

.header-icon-btn {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: filter 0.2s;
}
.header-icon-btn:hover { filter: brightness(1.15); }

.notif-badge {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 8px;
  height: 8px;
  background: #f64e60;
  border-radius: 50%;
  border: 2px solid;
}
.notif-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-top: 6px;
  flex-shrink: 0;
}

.user-badge {
  cursor: pointer;
  padding: 6px 10px;
  border-radius: 8px;
  transition: filter 0.2s;
}
.user-badge:hover { filter: brightness(1.15); }

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #009ef7, #f64e60);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}

.dropdown-box {
  position: absolute;
  border-radius: 12px;
  z-index: 999;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  border: 1px solid;
  transition: background 0.3s;
}

.dropdown-user-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  font-size: 13.5px;
  transition: background 0.2s, color 0.2s;
  cursor: pointer;
  color: #a1a5b7;
}
.dropdown-item-dark:hover  { background: #2d2d3f; color: #ffffff; }
.dropdown-item-light:hover { background: #f5f8fa; color: #181c32; }

.sidebar-label {
  padding: 14px 16px 6px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  transition: color 0.3s;
}

.user-menu-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 16px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  transition: background 0.2s, color 0.2s;
  margin-bottom: 2px;
}
.user-menu-link:hover  { background: rgba(0,158,247,0.08); color: #009ef7 !important; }
.user-menu-link-active { background: rgba(0,158,247,0.1) !important; color: #009ef7 !important; }

.sidebar-sub-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 16px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  font-size: 13.5px;
  transition: background 0.2s, color 0.2s;
  cursor: pointer;
  margin-bottom: 2px;
}
.sidebar-sub-item:hover        { background: rgba(0,158,247,0.06); color: #009ef7 !important; }
.sidebar-sub-item-active       { color: #009ef7 !important; font-weight: 700 !important; }
.sidebar-sub-item-active .sidebar-dot {
  background: #009ef7 !important;
  width: 8px !important;
  height: 8px !important;
}

.sidebar-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #565674;
  flex-shrink: 0;
  transition: all 0.2s;
}

.form-check-input:checked {
  background-color: #009ef7 !important;
  border-color: #009ef7 !important;
}
</style>
