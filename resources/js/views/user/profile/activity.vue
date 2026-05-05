<template>
  <div>
    <div class="mb-6">
      <h1 class="fw-bold text-gray-900 mb-1">Activity</h1>
      <div class="text-gray-500 fw-semibold fs-7">Home &mdash; User Profile &mdash; Activity</div>
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
      <div class="card-body p-0">

        <!--begin::Header Tabs-->
        <div class="d-flex align-items-center justify-content-between px-8 py-5 border-bottom border-gray-200">
          <div class="d-flex align-items-center gap-3">
            <i class="ki-duotone ki-calendar fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
            <span class="fw-bold text-gray-900 fs-4">{{ currentDateLabel }}</span>
          </div>
          <div class="d-flex gap-2">
            <button v-for="tab in tabs" :key="tab" class="btn btn-sm fw-semibold px-4"
              :class="activeTab === tab ? 'btn-primary' : 'btn-light text-gray-500'"
              @click="activeTab = tab">
              {{ tab }}
            </button>
          </div>
        </div>
        <!--end::Header Tabs-->

        <!--begin::Timeline-->
        <div class="px-8 py-6">
          <div v-for="(group, gi) in filteredGroups" :key="gi" class="mb-8">
            <div class="d-flex gap-4">
              <div class="d-flex flex-column align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; flex-shrink: 0;" :style="`background: ${group.iconBg};`">
                  <i :class="`ki-duotone ki-${group.icon} fs-3`" :style="`color: ${group.iconColor};`">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                  </i>
                </div>
                <div v-if="gi < filteredGroups.length - 1" style="width: 2px; flex: 1; background: #2d2d3f; margin-top: 8px; min-height: 40px;"></div>
              </div>
              <div class="flex-grow-1 pb-2">
                <div class="fw-bold text-gray-900 fs-6 mb-1">{{ group.title }}</div>
                <div class="text-gray-500 fs-7 mb-4">
                  {{ group.time }}
                  <span v-if="group.avatar" class="ms-2">by
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle ms-1"
                      style="width: 24px; height: 24px; font-size: 10px; font-weight: 700; color: white; vertical-align: middle;"
                      :style="`background: linear-gradient(135deg, ${group.avatarColor1 || '#009ef7'}, ${group.avatarColor2 || '#50cd89'});`">
                      {{ group.avatar }}
                    </span>
                  </span>
                </div>
                <div v-if="group.tasks && group.tasks.length" class="d-flex flex-column gap-3">
                  <div v-for="(task, ti) in group.tasks" :key="ti"
                    class="d-flex align-items-center justify-content-between rounded px-5 py-4"
                    style="border: 1px dashed #2d2d3f;">
                    <div class="fw-bold text-gray-800 fs-6" style="min-width: 200px;">{{ task.name }}</div>
                    <div class="text-gray-500 fs-7 flex-grow-1 px-4">{{ task.project }}</div>
                    <div class="d-flex align-items-center gap-2 me-4">
                      <span v-for="(av, ai) in task.assignees" :key="ai"
                        class="d-inline-flex align-items-center justify-content-center rounded-circle"
                        style="width: 30px; height: 30px; font-size: 11px; font-weight: 700; color: white; margin-left: -8px;"
                        :style="`background: linear-gradient(135deg, ${av.color1}, ${av.color2}); z-index: ${task.assignees.length - ai};`">
                        {{ av.initial }}
                      </span>
                    </div>
                    <span class="badge fw-semibold fs-7 px-3 py-2 me-4" :class="task.statusClass">{{ task.status }}</span>
                    <button class="btn btn-sm btn-link text-primary fw-semibold p-0">View</button>
                  </div>
                </div>
                <div v-if="group.files && group.files.length" class="d-flex gap-4 flex-wrap mt-2">
                  <div v-for="(file, fi) in group.files" :key="fi"
                    class="d-flex align-items-center gap-3 rounded px-4 py-3"
                    style="border: 1px dashed #2d2d3f; cursor: pointer; min-width: 200px;">
                    <i class="ki-duotone ki-document fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                    <span class="fw-semibold text-primary fs-7">{{ file }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--end::Timeline-->

      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "user-profile-activity",
  setup() {
    const userStore = useAuthStore();
    const tabs = ["Today", "Week", "Month", "2026"];
    const activeTab = ref("Today");

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

    const currentDateLabel = computed(() => {
      const now = new Date();
      return now.toLocaleDateString("en-US", { month: "long", day: "numeric", year: "numeric" });
    });

    const allGroups = [
      { icon: "message-text-2", iconBg: "rgba(0,158,247,0.15)",  iconColor: "#009ef7", title: 'There are 2 new tasks for you in "AirPlus Mobile App" project:', time: "Added at 4:23 PM", avatar: "A", avatarColor1: "#f64e60", avatarColor2: "#ffc700", tab: "Today",
        tasks: [
          { name: "Meeting with customer",        project: "Application Design",     assignees: [{ initial: "A", color1: "#f64e60", color2: "#ffc700" }, { initial: "B", color1: "#009ef7", color2: "#50cd89" }], status: "In Progress", statusClass: "badge-light-primary" },
          { name: "Project Delivery Preparation", project: "CRM System Development", assignees: [{ initial: "C", color1: "#7239ea", color2: "#f64e60" }, { initial: "B", color1: "#50cd89", color2: "#009ef7" }], status: "Completed",   statusClass: "badge-light-success" },
        ], files: null },
      { icon: "flag",           iconBg: "rgba(114,57,234,0.15)", iconColor: "#7239ea", title: "Invitation for crafting engaging designs that speak human workshop", time: "Sent at 4:23 PM",   avatar: "N", avatarColor1: "#009ef7", avatarColor2: "#50cd89", tab: "Today",  tasks: null, files: null },
      { icon: "abstract-26",    iconBg: "rgba(80,205,137,0.15)", iconColor: "#50cd89", title: "3 New Incoming Project Files:", time: "Sent at 10:30 PM", avatar: "J", avatarColor1: "#ffc700", avatarColor2: "#f64e60", tab: "Today",  tasks: null, files: ["Finance KPI App Guidelines", "Client UAT Testing Reports", "Finance Reports"] },
      { icon: "code",           iconBg: "rgba(0,158,247,0.15)",  iconColor: "#009ef7", title: "New project 'Metronic Dashboard' has been created",               time: "Monday at 9:00 AM", avatar: "S", avatarColor1: "#7239ea", avatarColor2: "#f64e60", tab: "Week",   tasks: null, files: null },
      { icon: "chart-simple",   iconBg: "rgba(255,199,0,0.15)",  iconColor: "#ffc700", title: "Campaign 'Social Media Boost' was launched successfully",          time: "Tuesday at 2:15 PM",avatar: "E", avatarColor1: "#50cd89", avatarColor2: "#009ef7", tab: "Week",
        tasks: [{ name: "Review campaign analytics", project: "Marketing", assignees: [{ initial: "E", color1: "#50cd89", color2: "#009ef7" }], status: "In Progress", statusClass: "badge-light-warning" }], files: null },
      { icon: "people",         iconBg: "rgba(246,78,96,0.15)",  iconColor: "#f64e60", title: "5 new followers this month",          time: "This month",  avatar: null, tab: "Month", tasks: null, files: null },
      { icon: "shield-tick",    iconBg: "rgba(80,205,137,0.15)", iconColor: "#50cd89", title: "Account verified successfully",        time: "Jan 2026",    avatar: null, tab: "2026",  tasks: null, files: null },
      { icon: "entrance-right", iconBg: "rgba(0,158,247,0.15)",  iconColor: "#009ef7", title: "Account created — Welcome to the platform!", time: "Jan 1, 2026", avatar: null, tab: "2026", tasks: null, files: null },
    ];

    const filteredGroups = computed(() => allGroups.filter((g) => g.tab === activeTab.value));

    return {
      userStore,
      tabs,
      activeTab,
      currentDateLabel,
      filteredGroups,
      // expose ke template
      animatedEarnings,
      animatedProjects,
      animatedSuccessRate,
    };
  },
});
</script>
