<template>
  <div>
    <div class="mb-6">
      <h1 class="fw-bold text-gray-900 mb-1">Projects</h1>
      <div class="text-gray-500 fw-semibold fs-7">Home &mdash; User Profile &mdash; Projects</div>
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
      <div class="card-body p-8">

        <!--begin::Header-->
        <div class="d-flex align-items-center justify-content-between mb-8">
          <div class="d-flex align-items-center gap-3">
            <h3 class="fw-bold text-gray-900 fs-4 mb-0">My Projects</h3>
            <span class="text-gray-400 fw-semibold fs-6">{{ filterStatus }}</span>
          </div>
          <div class="d-flex gap-3">
            <select v-model="filterStatus" class="form-select form-select-sm fw-semibold"
              style="min-width: 130px; background-color: #1e1e2d; border-color: #2d2d3f; color: #a1a5b7;"
              @change="visibleCount = perPage">
              <option>All</option>
              <option>Active</option>
              <option>Pending</option>
              <option>Completed</option>
            </select>
            <button class="btn btn-primary btn-sm fw-semibold px-5">New Project</button>
          </div>
        </div>
        <!--end::Header-->

        <!--begin::Grid-->
        <div class="row g-5">
          <div class="col-md-4" v-for="p in visibleProjects" :key="p.id">
            <div class="rounded p-7 h-100 d-flex flex-column" style="background: #1e1e2d; border: 1px solid #2d2d3f;">
              <div class="d-flex align-items-center justify-content-between mb-6">
                <div class="d-flex align-items-center justify-content-center rounded" style="width: 52px; height: 52px; background: #13131a;">
                  <img :src="p.logo" :alt="p.name" style="width: 32px; height: 32px; object-fit: contain;" />
                </div>
                <span class="fw-semibold fs-7 px-4 py-2 rounded" :style="`background: ${p.statusBg}; color: ${p.statusColor};`">{{ p.status }}</span>
              </div>
              <div class="fw-bold text-gray-900 fs-4 mb-2">{{ p.name }}</div>
              <div class="text-gray-500 fw-semibold fs-7 mb-6" style="line-height: 1.6;">{{ p.desc }}</div>
              <div class="mb-2">
                <div class="rounded p-4 mb-3" style="border: 1px dashed #2d2d3f;">
                  <div class="fw-bold text-gray-900 fs-5">{{ p.dueDate }}</div>
                  <div class="text-gray-500 fs-7 mt-1">Due Date</div>
                </div>
                <div class="rounded p-4" style="border: 1px dashed #2d2d3f;">
                  <div class="fw-bold text-gray-900 fs-5">{{ p.budget }}</div>
                  <div class="text-gray-500 fs-7 mt-1">Budget</div>
                </div>
              </div>
              <div class="my-5">
                <div class="h-4px rounded" style="background: #2d2d3f;">
                  <div class="h-4px rounded" :style="`width: ${p.progress}%; background: ${p.progressColor};`"></div>
                </div>
              </div>
              <div class="d-flex align-items-center mt-auto">
                <div class="d-flex">
                  <div v-for="(member, mi) in p.team" :key="mi"
                    style="width: 34px; height: 34px; border-radius: 50%; border: 2px solid #1e1e2d; overflow: hidden; flex-shrink: 0;"
                    :style="`z-index: ${p.team.length - mi}; margin-left: ${mi === 0 ? '0' : '-10px'};`">
                    <img v-if="member.photo" :src="member.photo" style="width: 100%; height: 100%; object-fit: cover;" />
                    <div v-else style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: white;" :style="`background: ${member.color};`">{{ member.initial }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--end::Grid-->

        <!--begin::Show More-->
        <div class="text-center mt-8">
          <button v-if="visibleCount < filteredProjects.length" class="btn btn-light fw-semibold px-10" @click="visibleCount += perPage">
            <i class="ki-duotone ki-down fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            Show More <span class="text-gray-400 ms-1 fs-7">({{ filteredProjects.length - visibleCount }} remaining)</span>
          </button>
          <button v-else-if="filteredProjects.length > perPage" class="btn btn-light fw-semibold px-10" @click="visibleCount = perPage">
            <i class="ki-duotone ki-up fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            Show Less
          </button>
        </div>
        <!--end::Show More-->

      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "user-profile-projects",
  setup() {
    const userStore = useAuthStore();
    const filterStatus = ref("All");
    const perPage = 6;
    const visibleCount = ref(perPage);

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

    const projects = ref([
      { id: 1, name: "ShopEase",    desc: "E-commerce platform with AI-powered product recommendations.",         status: "In Progress", statusBg: "rgba(0,158,247,0.15)",   statusColor: "#009ef7", dueDate: "Jun 30, 2026", budget: "$128,500.00", progress: 55,  progressColor: "#009ef7", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/shopify.svg",     team: [{ photo: "https://randomuser.me/api/portraits/women/44.jpg", initial: "", color: "" }, { photo: "https://randomuser.me/api/portraits/men/32.jpg", initial: "", color: "" }, { photo: "", initial: "S", color: "#009ef7" }] },
      { id: 2, name: "MediTrack",   desc: "Healthcare management system for patient records and appointments.",    status: "Pending",     statusBg: "rgba(114,57,234,0.15)", statusColor: "#7239ea", dueDate: "Aug 20, 2026", budget: "$36,400.00",  progress: 20,  progressColor: "#7239ea", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/notion.svg",      team: [{ photo: "", initial: "A", color: "#ffc700" }, { photo: "https://randomuser.me/api/portraits/men/55.jpg", initial: "", color: "" }] },
      { id: 3, name: "FinEdge",     desc: "Personal finance tracker with budgeting and investment analytics.",     status: "Completed",   statusBg: "rgba(80,205,137,0.15)", statusColor: "#50cd89", dueDate: "Mar 14, 2025", budget: "$605,100.00", progress: 100, progressColor: "#50cd89", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/stripe.svg",      team: [{ photo: "https://randomuser.me/api/portraits/women/65.jpg", initial: "", color: "" }, { photo: "https://randomuser.me/api/portraits/men/41.jpg", initial: "", color: "" }, { photo: "", initial: "M", color: "#7239ea" }] },
      { id: 4, name: "EduNest",     desc: "Online learning platform with live classes and progress tracking.",     status: "Active",      statusBg: "rgba(0,158,247,0.15)",   statusColor: "#009ef7", dueDate: "Sep 01, 2026", budget: "$74,200.00",  progress: 40,  progressColor: "#009ef7", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/udemy.svg",       team: [{ photo: "https://randomuser.me/api/portraits/men/68.jpg", initial: "", color: "" }, { photo: "", initial: "R", color: "#f64e60" }] },
      { id: 5, name: "LogiFlow",    desc: "Supply chain and logistics management tool for enterprise teams.",      status: "In Progress", statusBg: "rgba(0,158,247,0.15)",   statusColor: "#009ef7", dueDate: "Nov 15, 2026", budget: "$210,000.00", progress: 65,  progressColor: "#ffc700", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/slack.svg",       team: [{ photo: "https://randomuser.me/api/portraits/women/31.jpg", initial: "", color: "" }, { photo: "https://randomuser.me/api/portraits/men/76.jpg", initial: "", color: "" }, { photo: "", initial: "L", color: "#50cd89" }] },
      { id: 6, name: "TaskBoard",   desc: "Agile project management tool with Kanban boards and sprint planning.", status: "Completed",   statusBg: "rgba(80,205,137,0.15)", statusColor: "#50cd89", dueDate: "Jan 10, 2025", budget: "$48,750.00",  progress: 100, progressColor: "#50cd89", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/trello.svg",      team: [{ photo: "", initial: "T", color: "#009ef7" }, { photo: "https://randomuser.me/api/portraits/women/22.jpg", initial: "", color: "" }] },
      { id: 7, name: "CloudVault",  desc: "Secure cloud storage solution with end-to-end encryption.",             status: "Pending",     statusBg: "rgba(114,57,234,0.15)", statusColor: "#7239ea", dueDate: "Dec 01, 2026", budget: "$390,000.00", progress: 10,  progressColor: "#7239ea", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/googledrive.svg", team: [{ photo: "https://randomuser.me/api/portraits/men/23.jpg", initial: "", color: "" }, { photo: "", initial: "C", color: "#f64e60" }] },
      { id: 8, name: "SocialPulse", desc: "Social media analytics dashboard with real-time engagement metrics.",   status: "Active",      statusBg: "rgba(0,158,247,0.15)",   statusColor: "#009ef7", dueDate: "Jul 22, 2026", budget: "$55,300.00",  progress: 72,  progressColor: "#f64e60", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/buffer.svg",      team: [{ photo: "https://randomuser.me/api/portraits/women/57.jpg", initial: "", color: "" }, { photo: "https://randomuser.me/api/portraits/men/85.jpg", initial: "", color: "" }, { photo: "", initial: "N", color: "#ffc700" }] },
      { id: 9, name: "DevLaunch",   desc: "CI/CD pipeline tool for automated deployment and monitoring.",          status: "In Progress", statusBg: "rgba(0,158,247,0.15)",   statusColor: "#009ef7", dueDate: "Oct 05, 2026", budget: "$167,800.00", progress: 48,  progressColor: "#009ef7", logo: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/github.svg",      team: [{ photo: "", initial: "D", color: "#50cd89" }, { photo: "https://randomuser.me/api/portraits/men/60.jpg", initial: "", color: "" }, { photo: "https://randomuser.me/api/portraits/women/83.jpg", initial: "", color: "" }] },
    ]);

    const filteredProjects = computed(() => {
      if (filterStatus.value === "All") return projects.value;
      return projects.value.filter((p) => p.status.toLowerCase().includes(filterStatus.value.toLowerCase()));
    });
    const visibleProjects = computed(() => filteredProjects.value.slice(0, visibleCount.value));

    return {
      userStore,
      filteredProjects,
      visibleProjects,
      filterStatus,
      visibleCount,
      perPage,
      // expose ke template
      animatedEarnings,
      animatedProjects,
      animatedSuccessRate,
    };
  },
});
</script>
