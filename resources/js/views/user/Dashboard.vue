<template>
  <div>

    <!--begin::Profile Hero-->
    <div class="card mb-6" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border: none; min-height: 200px; position: relative; overflow: hidden;">
      <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1;">
        <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background: #009ef7; top: -50px; right: 100px;"></div>
        <div style="position: absolute; width: 150px; height: 150px; border-radius: 50%; background: #f64e60; bottom: -30px; right: 300px;"></div>
      </div>
      <div class="card-body p-8 d-flex align-items-center gap-8" style="position: relative; z-index: 1;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #009ef7, #f64e60); display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: 700; color: white; flex-shrink: 0; border: 4px solid rgba(255,255,255,0.2);">
          {{ userStore.user.name?.charAt(0).toUpperCase() }}
        </div>
        <div class="flex-grow-1">
          <div class="d-flex align-items-center gap-3 mb-2">
            <h2 class="fw-bold text-white mb-0">{{ userStore.user.name }}</h2>
            <span class="badge badge-light-success fw-semibold">Active</span>
          </div>
          <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="d-flex align-items-center gap-2">
              <i class="ki-duotone ki-sms fs-5 text-gray-400"><span class="path1"></span><span class="path2"></span></i>
              <span class="text-gray-400 fw-semibold fs-6">{{ userStore.user.email }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <i class="ki-duotone ki-geolocation fs-5 text-gray-400"><span class="path1"></span><span class="path2"></span></i>
              <span class="text-gray-400 fw-semibold fs-6">Indonesia</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--end::Profile Hero-->

    <!--begin::Stats Row-->
    <div class="row g-5 mb-6">
      <div class="col-6 col-md-3">
        <div class="card card-flush h-100" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-body p-6 text-center">
            <div class="fw-bold fs-2x text-white mb-1">12</div>
            <div class="text-gray-500 fw-semibold fs-7">Projects</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card card-flush h-100" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-body p-6 text-center">
            <div class="fw-bold fs-2x text-white mb-1">98%</div>
            <div class="text-gray-500 fw-semibold fs-7">Success Rate</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card card-flush h-100" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-body p-6 text-center">
            <div class="fw-bold fs-2x text-white mb-1">4.8</div>
            <div class="text-gray-500 fw-semibold fs-7">Rating</div>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card card-flush h-100" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-body p-6 text-center">
            <div class="fw-bold fs-2x text-white mb-1">250+</div>
            <div class="text-gray-500 fw-semibold fs-7">Connections</div>
          </div>
        </div>
      </div>
    </div>
    <!--end::Stats Row-->

    <!--begin::Main Row-->
    <div class="row g-5">

      <!--begin::Left Column-->
      <div class="col-md-4">

        <!--begin::About-->
        <div class="card mb-5" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">About Me</h3>
            </div>
          </div>
          <div class="card-body pt-2">
            <p class="fw-semibold fs-6 mb-5" :style="`color: ${isDark ? '#a1a5b7' : '#5e6278'};`">
              Hello! I'm {{ userStore.user.name }}, a member of this platform. Passionate about collaboration and building great experiences.
            </p>
            <div class="separator separator-dashed mb-5"></div>
            <div class="d-flex align-items-center mb-4">
              <i class="ki-duotone ki-sms fs-4 text-primary me-3"><span class="path1"></span><span class="path2"></span></i>
              <div>
                <div class="text-gray-500 fs-7 fw-semibold">Email</div>
                <div class="fw-bold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">{{ userStore.user.email }}</div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-4">
              <i class="ki-duotone ki-geolocation fs-4 text-danger me-3"><span class="path1"></span><span class="path2"></span></i>
              <div>
                <div class="text-gray-500 fs-7 fw-semibold">Location</div>
                <div class="fw-bold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Indonesia</div>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <i class="ki-duotone ki-calendar fs-4 text-success me-3"><span class="path1"></span><span class="path2"></span></i>
              <div>
                <div class="text-gray-500 fs-7 fw-semibold">Member Since</div>
                <div class="fw-bold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">2024</div>
              </div>
            </div>
          </div>
        </div>
        <!--end::About-->

        <!--begin::Skills-->
        <div class="card mb-5" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Skills</h3>
            </div>
          </div>
          <div class="card-body pt-2">
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold fs-6" :style="`color: ${isDark ? '#cdcde6' : '#3f4254'};`">Communication</span>
                <span class="text-gray-500 fw-semibold fs-7">90%</span>
              </div>
              <div class="h-6px bg-light-primary rounded">
                <div class="bg-primary rounded h-6px" style="width: 90%;"></div>
              </div>
            </div>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold fs-6" :style="`color: ${isDark ? '#cdcde6' : '#3f4254'};`">Teamwork</span>
                <span class="text-gray-500 fw-semibold fs-7">85%</span>
              </div>
              <div class="h-6px bg-light-success rounded">
                <div class="bg-success rounded h-6px" style="width: 85%;"></div>
              </div>
            </div>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold fs-6" :style="`color: ${isDark ? '#cdcde6' : '#3f4254'};`">Problem Solving</span>
                <span class="text-gray-500 fw-semibold fs-7">78%</span>
              </div>
              <div class="h-6px bg-light-warning rounded">
                <div class="bg-warning rounded h-6px" style="width: 78%;"></div>
              </div>
            </div>
            <div>
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold fs-6" :style="`color: ${isDark ? '#cdcde6' : '#3f4254'};`">Leadership</span>
                <span class="text-gray-500 fw-semibold fs-7">70%</span>
              </div>
              <div class="h-6px bg-light-info rounded">
                <div class="bg-info rounded h-6px" style="width: 70%;"></div>
              </div>
            </div>
          </div>
        </div>
        <!--end::Skills-->

        <!--begin::Badges-->
        <div class="card" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Community Badges</h3>
            </div>
          </div>
          <div class="card-body pt-2">
            <div class="d-flex gap-4 flex-wrap">
              <div class="text-center">
                <div class="symbol symbol-50px mb-2">
                  <span class="symbol-label bg-light-primary">
                    <i class="ki-duotone ki-star fs-2x text-primary"><span class="path1"></span><span class="path2"></span></i>
                  </span>
                </div>
                <div class="fw-bold fs-7" :style="`color: ${isDark ? '#cdcde6' : '#3f4254'};`">New Member</div>
              </div>
              <div class="text-center">
                <div class="symbol symbol-50px mb-2">
                  <span class="symbol-label bg-light-success">
                    <i class="ki-duotone ki-shield-tick fs-2x text-success"><span class="path1"></span><span class="path2"></span></i>
                  </span>
                </div>
                <div class="fw-bold fs-7" :style="`color: ${isDark ? '#cdcde6' : '#3f4254'};`">Verified</div>
              </div>
              <div class="text-center">
                <div class="symbol symbol-50px mb-2">
                  <span class="symbol-label bg-light-warning">
                    <i class="ki-duotone ki-crown fs-2x text-warning"><span class="path1"></span><span class="path2"></span></i>
                  </span>
                </div>
                <div class="fw-bold fs-7" :style="`color: ${isDark ? '#cdcde6' : '#3f4254'};`">Premium</div>
              </div>
              <div class="text-center">
                <div class="symbol symbol-50px mb-2">
                  <span class="symbol-label bg-light-info">
                    <i class="ki-duotone ki-people fs-2x text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                  </span>
                </div>
                <div class="fw-bold fs-7" :style="`color: ${isDark ? '#cdcde6' : '#3f4254'};`">Collaborator</div>
              </div>
            </div>
          </div>
        </div>
        <!--end::Badges-->

      </div>
      <!--end::Left Column-->

      <!--begin::Right Column-->
      <div class="col-md-8">

        <!--begin::Media Uploads Chart-->
        <div class="card mb-5" :style="`background: ${isDark ? '#151521' : '#ffffff'};`">
          <div class="card-header border-0 pt-6 pb-0">
            <div class="card-title">
              <h3 class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Media Uploads</h3>
            </div>
            <div class="card-toolbar">
              <span class="fw-bold fs-4" style="cursor: pointer; color: #a1a5b7;">&#8942;</span>
            </div>
          </div>
          <div class="card-body pt-4">
            <canvas ref="mediaChartRef" height="280"></canvas>
          </div>
        </div>
        <!--end::Media Uploads Chart-->

        <!--begin::Activity Overview-->
        <div class="card mb-5" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Activity Overview</h3>
            </div>
            <div class="card-toolbar">
              <span class="badge badge-light-primary fw-semibold">Last 7 days</span>
            </div>
          </div>
          <div class="card-body pt-2">
            <div class="d-flex justify-content-between mb-6">
              <div class="text-center">
                <div class="fw-bold fs-4 text-primary">24</div>
                <div class="text-gray-500 fs-7">Logins</div>
              </div>
              <div class="text-center">
                <div class="fw-bold fs-4 text-success">8</div>
                <div class="text-gray-500 fs-7">Updates</div>
              </div>
              <div class="text-center">
                <div class="fw-bold fs-4 text-warning">15</div>
                <div class="text-gray-500 fs-7">Actions</div>
              </div>
              <div class="text-center">
                <div class="fw-bold fs-4 text-info">3</div>
                <div class="text-gray-500 fs-7">Reports</div>
              </div>
            </div>
            <div class="d-flex align-items-end gap-2" style="height: 100px;">
              <div v-for="(bar, i) in activityBars" :key="i" class="flex-grow-1 d-flex flex-column align-items-center gap-1">
                <div class="rounded-top w-100" :style="`height: ${bar.height}px; background: ${bar.color}; opacity: 0.85;`"></div>
                <div class="fw-semibold" style="font-size: 10px; color: #a1a5b7;">{{ bar.day }}</div>
              </div>
            </div>
          </div>
        </div>
        <!--end::Activity Overview-->

        <!--begin::Recent Activity-->
        <div class="card" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Recent Activity</h3>
            </div>
          </div>
          <div class="card-body pt-2">
            <div class="d-flex align-items-center mb-6">
              <div class="symbol symbol-40px me-4">
                <span class="symbol-label bg-light-primary">
                  <i class="ki-duotone ki-profile-circle fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Profile Updated</div>
                <div class="text-gray-500 fw-semibold fs-7">You updated your profile information</div>
              </div>
              <span class="badge badge-light fw-semibold">Just now</span>
            </div>
            <div class="separator separator-dashed mb-6"></div>
            <div class="d-flex align-items-center mb-6">
              <div class="symbol symbol-40px me-4">
                <span class="symbol-label bg-light-success">
                  <i class="ki-duotone ki-shield-tick fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Account Verified</div>
                <div class="text-gray-500 fw-semibold fs-7">Your account has been verified successfully</div>
              </div>
              <span class="badge badge-light fw-semibold">1 day ago</span>
            </div>
            <div class="separator separator-dashed mb-6"></div>
            <div class="d-flex align-items-center">
              <div class="symbol symbol-40px me-4">
                <span class="symbol-label bg-light-info">
                  <i class="ki-duotone ki-entrance-right fs-2 text-info"><span class="path1"></span><span class="path2"></span></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Account Created</div>
                <div class="text-gray-500 fw-semibold fs-7">Welcome to the platform!</div>
              </div>
              <span class="badge badge-light fw-semibold">2024</span>
            </div>
          </div>
        </div>
        <!--end::Recent Activity-->

      </div>
      <!--end::Right Column-->

    </div>
    <!--end::Main Row-->

  </div>
</template>

<script lang="ts">
import { defineComponent, ref, inject, onMounted, watch, Ref } from "vue";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "user-dashboard",
  setup() {
    const userStore = useAuthStore();
    const isDark = inject<Ref<boolean>>("isDark", ref(true));
    const mediaChartRef = ref<HTMLCanvasElement | null>(null);
    let chartInstance: any = null;

    const activityBars = ref([
      { day: "Mon", height: 60, color: "#009ef7" },
      { day: "Tue", height: 90, color: "#009ef7" },
      { day: "Wed", height: 45, color: "#009ef7" },
      { day: "Thu", height: 100, color: "#f64e60" },
      { day: "Fri", height: 70, color: "#009ef7" },
      { day: "Sat", height: 30, color: "#009ef7" },
      { day: "Sun", height: 55, color: "#009ef7" },
    ]);

    const buildChart = async () => {
      if (!mediaChartRef.value) return;
      const { Chart, registerables } = await import("chart.js");
      Chart.register(...registerables);

      if (chartInstance) {
        chartInstance.destroy();
      }

      const dark = isDark.value;
      const gridColor = dark ? "rgba(255,255,255,0.04)" : "rgba(0,0,0,0.06)";
      const tickColor = dark ? "#a1a5b7" : "#7e8299";

      chartInstance = new Chart(mediaChartRef.value, {
        type: "line",
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
            label: "Media Uploads",
            data: [82000, 65000, 50000, 68000, 40000, 43000, 100000, 58000, 84000, 60000, 72000, 90000],
            borderColor: "#009ef7",
            backgroundColor: dark ? "rgba(0,158,247,0.08)" : "rgba(0,158,247,0.05)",
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "#009ef7",
            pointHoverBorderColor: "#fff",
            pointHoverBorderWidth: 2,
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              mode: "index",
              intersect: false,
              backgroundColor: dark ? "#1e1e2d" : "#ffffff",
              titleColor: dark ? "#ffffff" : "#181c32",
              bodyColor: dark ? "#a1a5b7" : "#5e6278",
              borderColor: dark ? "#2d2d3f" : "#eff2f5",
              borderWidth: 1,
              padding: 12,
              callbacks: {
                label: (ctx) => ` $${((ctx.raw as number) / 1000).toFixed(0)}K`,
              },
            },
          },
          scales: {
            x: {
              grid: { color: gridColor },
              ticks: { color: tickColor, font: { size: 12 } },
              border: { display: false },
            },
            y: {
              grid: { color: gridColor },
              ticks: {
                color: tickColor,
                font: { size: 12 },
                callback: (val) => `$${((val as number) / 1000).toFixed(0)}K`,
              },
              border: { display: false },
              min: 0,
              max: 120000,
            },
          },
        },
      });
    };

    onMounted(() => { buildChart(); });

    watch(() => isDark.value, () => { buildChart(); });

    return {
      userStore,
      isDark,
      mediaChartRef,
      activityBars,
    };
  },
});
</script>
