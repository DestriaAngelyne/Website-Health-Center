<template>
  <div>
    <div class="mb-6">
      <h1 class="fw-bold text-gray-900 mb-1">Documents</h1>
      <div class="text-gray-500 fw-semibold fs-7">Home &mdash; User Profile &mdash; Documents</div>
    </div>

    <!--begin::Profile Card-->
    <div class="card mb-6">
      <div class="card-body p-8">
        <div class="d-flex align-items-start gap-8 flex-wrap">
          <div style="width: 120px; height: 120px; border-radius: 12px; background: linear-gradient(135deg, #009ef7, #f64e60); display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: 700; color: white; flex-shrink: 0; position: relative;">
            {{ userStore.user.name?.charAt(0).toUpperCase() }}
            <span style="position: absolute; bottom: 8px; right: 8px; width: 14px; height: 14px; background: #50cd89; border-radius: 50%; border: 2px solid white;"></span>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-3 mb-2">
              <h2 class="fw-bold text-gray-900 mb-0 fs-2">{{ userStore.user.name }}</h2>
              <i class="ki-duotone ki-verify fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
            </div>
            <div class="d-flex align-items-center gap-5 mb-5 flex-wrap">
              <div class="d-flex align-items-center gap-2 text-gray-500 fw-semibold fs-6">
                <i class="ki-duotone ki-profile-circle fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <span class="text-capitalize">{{ userStore.user.role }}</span>
              </div>
              <div class="d-flex align-items-center gap-2 text-gray-500 fw-semibold fs-6">
                <i class="ki-duotone ki-geolocation fs-4"><span class="path1"></span><span class="path2"></span></i>
                <span>Indonesia</span>
              </div>
              <div class="d-flex align-items-center gap-2 text-gray-500 fw-semibold fs-6">
                <i class="ki-duotone ki-sms fs-4"><span class="path1"></span><span class="path2"></span></i>
                <span>{{ userStore.user.email }}</span>
              </div>
            </div>
            <!--begin::Stats-->
            <div class="d-flex gap-4 flex-wrap">
              <div class="border border-dashed rounded px-4 py-2 text-center">
                <div class="d-flex align-items-center gap-1">
                  <i class="ki-duotone ki-arrow-up fs-5 text-success"><span class="path1"></span><span class="path2"></span></i>
                  <!-- PERUBAHAN: animatedEarnings -->
                  <span class="fw-bold fs-4 text-gray-900">${{ animatedEarnings }}</span>
                </div>
                <div class="text-gray-500 fs-7">Earnings</div>
              </div>
              <div class="border border-dashed rounded px-4 py-2 text-center">
                <div class="d-flex align-items-center gap-1">
                  <i class="ki-duotone ki-arrow-down fs-5 text-danger"><span class="path1"></span><span class="path2"></span></i>
                  <!-- PERUBAHAN: animatedProjects -->
                  <span class="fw-bold fs-4 text-gray-900">{{ animatedProjects }}</span>
                </div>
                <div class="text-gray-500 fs-7">Projects</div>
              </div>
              <div class="border border-dashed rounded px-4 py-2 text-center">
                <div class="d-flex align-items-center gap-1">
                  <i class="ki-duotone ki-arrow-up fs-5 text-success"><span class="path1"></span><span class="path2"></span></i>
                  <!-- PERUBAHAN: animatedSuccessRate -->
                  <span class="fw-bold fs-4 text-gray-900">%{{ animatedSuccessRate }}</span>
                </div>
                <div class="text-gray-500 fs-7">Success Rate</div>
              </div>
            </div>
            <!--end::Stats-->
            <div class="mt-5">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-gray-500 fw-semibold fs-7">Profile Completion</span>
                <span class="fw-bold text-gray-800 fs-7">50%</span>
              </div>
              <div class="h-6px bg-light rounded">
                <div class="bg-success rounded h-6px" style="width: 50%;"></div>
              </div>
            </div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-light fw-semibold">Follow</button>
            <button class="btn btn-primary fw-semibold">Hire Me</button>
          </div>
        </div>
      </div>
    </div>
    <!--end::Profile Card-->

    <div class="card">
      <div class="card-header border-0 pt-6">
        <div class="card-title">
          <h3 class="fw-bold text-gray-900 fs-5">My Documents</h3>
        </div>
        <div class="card-toolbar">
          <button class="btn btn-primary btn-sm">
            <i class="ki-duotone ki-cloud-add fs-4"><span class="path1"></span><span class="path2"></span></i>
            Upload
          </button>
        </div>
      </div>
      <div class="card-body pt-0">
        <div class="row g-4">
          <div class="col-md-4" v-for="doc in documents" :key="doc.id">
            <div class="d-flex align-items-center p-4 rounded border border-dashed gap-3">
              <div class="symbol symbol-45px">
                <span class="symbol-label" :class="`bg-light-${doc.color}`">
                  <i :class="`ki-duotone ki-${doc.icon} fs-2 text-${doc.color}`"><span class="path1"></span><span class="path2"></span></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold text-gray-800 fs-7">{{ doc.name }}</div>
                <div class="text-gray-500 fs-8">{{ doc.size }} &bull; {{ doc.date }}</div>
              </div>
              <button class="btn btn-sm btn-icon btn-light">
                <i class="ki-duotone ki-cloud-download fs-4"><span class="path1"></span><span class="path2"></span></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "user-profile-documents",
  setup() {
    const userStore = useAuthStore();

    // ========================================
    // COUNT-UP ANIMATION — tambahan baru
    // ========================================
    const animatedEarnings = ref(0);
    const animatedProjects = ref(0);
    const animatedSuccessRate = ref(0);

    const countUp = (
      refValue: { value: number },
      target: number,
      duration = 1800
    ) => {
      const startTime = performance.now();
      const easeOutExpo = (t: number) =>
        t === 1 ? 1 : 1 - Math.pow(2, -10 * t);

      const step = (currentTime: number) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        refValue.value = Math.floor(easeOutExpo(progress) * target);
        if (progress < 1) requestAnimationFrame(step);
        else refValue.value = target;
      };

      requestAnimationFrame(step);
    };

    onMounted(() => {
      countUp(animatedEarnings, 4500);
      countUp(animatedProjects, 80);
      countUp(animatedSuccessRate, 60);
    });
    // ========================================

    const documents = ref([
      { id: 1, name: "Project Proposal.pdf", size: "2.4 MB",  date: "Dec 10, 2024", color: "danger",  icon: "document" },
      { id: 2, name: "Design Assets.zip",    size: "15.8 MB", date: "Dec 5, 2024",  color: "primary", icon: "folder" },
      { id: 3, name: "Meeting Notes.docx",   size: "540 KB",  date: "Nov 28, 2024", color: "info",    icon: "notepad" },
      { id: 4, name: "Budget Sheet.xlsx",    size: "1.2 MB",  date: "Nov 20, 2024", color: "success", icon: "graph-3" },
      { id: 5, name: "Brand Guidelines.pdf", size: "8.1 MB",  date: "Nov 15, 2024", color: "warning", icon: "book" },
      { id: 6, name: "Source Code.zip",      size: "45.3 MB", date: "Oct 30, 2024", color: "primary", icon: "code" },
    ]);

    return {
      userStore,
      documents,
      // expose ke template
      animatedEarnings,
      animatedProjects,
      animatedSuccessRate,
    };
  },
});
</script>
