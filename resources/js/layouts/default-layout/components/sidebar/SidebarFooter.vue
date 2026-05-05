<template>
  <!--begin::Footer-->
  <div
    class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6"
    id="kt_app_sidebar_footer"
  >
    <!--begin::Beranda Button-->
    <a
      href="/"
      class="btn btn-flex flex-center btn-custom btn-light-primary overflow-hidden text-nowrap px-0 h-40px w-100 mb-3"
    >
      <KTIcon icon-name="home" icon-class="btn-icon fs-2 m-0 me-2" />
      <span class="btn-label">Kembali ke Beranda</span>
    </a>
    <!--end::Beranda Button-->


  </div>
  <!--end::Footer-->
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent } from "vue";

export default defineComponent({
  name: "sidebar-footer",
  components: {},
  setup() {
    return {
      getAssetPath,
    };
  },
});
</script>
