<template>
  <!--begin::Logo-->
  <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
    <!--begin::Logo-->
    <div class="d-flex align-items-center gap-3">
      <!--begin::Icon-->
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"
        style="width:36px;height:36px;flex-shrink:0;">
        <circle cx="50" cy="50" r="50" fill="#1a6aad"/>
        <circle cx="50" cy="50" r="44" fill="none" stroke="white" stroke-width="1.5"/>
        <defs>
          <path id="sb-top" d="M 15,50 A 35,35 0 0,1 85,50"/>
          <path id="sb-bot" d="M 23,63 A 29,29 0 0,0 77,63"/>
        </defs>
        <text font-family="Arial,sans-serif" font-weight="800" font-size="9.8" fill="white" letter-spacing="2">
          <textPath href="#sb-top" startOffset="50%" text-anchor="middle">GRAHA MEDIKA</textPath>
        </text>
        <text font-family="Arial,sans-serif" font-weight="600" font-size="8.5" fill="white" letter-spacing="1.2">
          <textPath href="#sb-bot" startOffset="50%" text-anchor="middle">Hospital</textPath>
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
      <!--end::Icon-->
      <!--begin::Text-->
      <div class="app-sidebar-logo-default">
        <div class="fw-bold text-white fs-6 lh-1">Graha Medika Prima</div>
      </div>
      <!--end::Text-->
    </div>
    <!--end::Logo-->

    <!--begin::Sidebar toggle-->
    <div
      v-if="sidebarToggleDisplay"
      ref="toggleRef"
      id="kt_app_sidebar_toggle"
      class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
      data-kt-toggle="true"
      data-kt-toggle-state="active"
      data-kt-toggle-target="body"
      data-kt-toggle-name="app-sidebar-minimize"
    >
      <KTIcon icon-name="black-left-line" icon-class="fs-3 rotate-180 ms-1" />
    </div>
    <!--end::Sidebar toggle-->
  </div>
  <!--end::Logo-->
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import { ToggleComponent } from "@/assets/ts/components";
import { sidebarToggleDisplay } from "@/layouts/default-layout/config/helper";

interface IProps {
  sidebarRef: HTMLElement | null;
}
const props = defineProps<IProps>();
const toggleRef = ref<HTMLFormElement | null>(null);

onMounted(() => {
  setTimeout(() => {
    const toggleObj = ToggleComponent.getInstance(toggleRef.value!) as ToggleComponent | null;
    if (toggleObj === null) return;
    toggleObj.on("kt.toggle.change", function () {
      props.sidebarRef?.classList.add("animating");
      setTimeout(function () {
        props.sidebarRef?.classList.remove("animating");
      }, 300);
    });
  }, 1);
});
</script>
