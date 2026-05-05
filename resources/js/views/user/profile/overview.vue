<template>
  <div>
    <div class="mb-6">
      <h1 class="fw-bold text-gray-900 mb-1">Overview</h1>
      <div class="text-gray-500 fw-semibold fs-7">Home &mdash; User Profile &mdash; Overview</div>
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
                  <!-- PERUBAHAN: gunakan animatedEarnings -->
                  <span class="fw-bold fs-4 text-gray-900">${{ animatedEarnings }}</span>
                </div>
                <div class="text-gray-500 fs-7">Earnings</div>
              </div>
              <div class="border border-dashed rounded px-4 py-2 text-center">
                <div class="d-flex align-items-center gap-1">
                  <i class="ki-duotone ki-arrow-down fs-5 text-danger"><span class="path1"></span><span class="path2"></span></i>
                  <!-- PERUBAHAN: gunakan animatedProjects -->
                  <span class="fw-bold fs-4 text-gray-900">{{ animatedProjects }}</span>
                </div>
                <div class="text-gray-500 fs-7">Projects</div>
              </div>
              <div class="border border-dashed rounded px-4 py-2 text-center">
                <div class="d-flex align-items-center gap-1">
                  <i class="ki-duotone ki-arrow-up fs-5 text-success"><span class="path1"></span><span class="path2"></span></i>
                  <!-- PERUBAHAN: gunakan animatedSuccessRate -->
                  <span class="fw-bold fs-4 text-gray-900">%{{ animatedSuccessRate }}</span>
                </div>
                <div class="text-gray-500 fs-7">Success Rate</div>
              </div>
            </div>
            <!--end::Stats-->
            <!--begin::Profile Completion-->
            <div class="mt-5">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-gray-500 fw-semibold fs-7">Profile Completion</span>
                <span class="fw-bold text-gray-800 fs-7">50%</span>
              </div>
              <div class="h-6px bg-light rounded">
                <div class="bg-success rounded h-6px" style="width: 50%;"></div>
              </div>
            </div>
            <!--end::Profile Completion-->
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-light fw-semibold">Follow</button>
            <button class="btn btn-primary fw-semibold">Hire Me</button>
          </div>
        </div>
      </div>
    </div>
    <!--end::Profile Card-->

    <!--begin::Content Row-->
    <div class="row g-5">
      <!--begin::Left - Feed-->
      <div class="col-md-7">
        <!--begin::Post Box-->
        <div class="card mb-5">
          <div class="card-body p-6">
            <textarea class="form-control form-control-solid border-0 p-0 fs-6" rows="3" placeholder="What is on your mind ?" style="resize: none; background: transparent;"></textarea>
            <div class="separator my-4"></div>
            <div class="d-flex align-items-center gap-3">
              <button class="btn btn-sm btn-light fw-semibold">Normal</button>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-icon btn-light"><b>B</b></button>
                <button class="btn btn-sm btn-icon btn-light"><i>I</i></button>
                <button class="btn btn-sm btn-icon btn-light"><u>U</u></button>
              </div>
            </div>
          </div>
        </div>
        <!--end::Post Box-->

        <!--begin::Comment Card-->
        <div class="card mb-5">
          <div class="card-body p-6">
            <div class="d-flex align-items-start gap-4 mb-4">
              <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #50cd89, #009ef7); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: white; flex-shrink: 0;">N</div>
              <div class="flex-grow-1">
                <div class="fw-bold text-gray-900 fs-6">Nick Logan</div>
                <div class="text-gray-500 fs-7">PHP, SQLite, Artisan CLI</div>
              </div>
            </div>
            <p class="text-gray-700 fw-semibold fs-6 mb-4">
              Outlines keep you honest. They stop you from indulging in poorly thought-out metaphors about driving and keep you focused on the overall structure of your post.
            </p>
            <div class="d-flex align-items-center gap-4">
              <div class="d-flex align-items-center gap-2 text-gray-500 fw-semibold fs-7">
                <i class="ki-duotone ki-message-text fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                120
              </div>
              <div class="d-flex align-items-center gap-2 text-gray-500 fw-semibold fs-7">
                <i class="ki-duotone ki-heart fs-4 text-danger"><span class="path1"></span><span class="path2"></span></i>
                15
              </div>
            </div>
            <div class="separator my-4"></div>
            <input type="text" class="form-control form-control-solid" placeholder="Reply..." />
          </div>
        </div>
        <!--end::Comment Card-->
      </div>
      <!--end::Left-->

      <!--begin::Right - Stats-->
      <div class="col-md-5">
        <!--begin::Recent Statistics-->
        <div class="card mb-5">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold text-gray-900 fs-5">Recent Statistics</h3>
            </div>
            <div class="card-toolbar">
              <span class="text-gray-500 fw-semibold fs-7">More than 400 new members</span>
            </div>
          </div>
          <div class="card-body pt-2">
            <canvas ref="statsChart" style="width: 100%; height: 200px;"></canvas>
          </div>
        </div>
        <!--end::Recent Statistics-->

        <!--begin::Activities-->
        <div class="card">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold text-gray-900 fs-5">Activities</h3>
            </div>
            <div class="card-toolbar">
              <span class="text-gray-500 fw-semibold fs-7">890,344 Sales</span>
            </div>
          </div>
          <div class="card-body pt-2">
            <div class="d-flex align-items-center mb-4">
              <div class="symbol symbol-40px me-4">
                <span class="symbol-label bg-light-primary">
                  <i class="ki-duotone ki-abstract-26 fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold text-gray-800 fs-6">Top Authors</div>
                <div class="text-gray-500 fs-7">Successful Fellas</div>
              </div>
              <span class="badge badge-light-success fw-semibold">+82%</span>
            </div>
            <div class="separator separator-dashed mb-4"></div>
            <div class="d-flex align-items-center mb-4">
              <div class="symbol symbol-40px me-4">
                <span class="symbol-label bg-light-warning">
                  <i class="ki-duotone ki-chart-simple fs-2 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold text-gray-800 fs-6">Popular Authors</div>
                <div class="text-gray-500 fs-7">Most Viewed This Week</div>
              </div>
              <span class="badge badge-light-warning fw-semibold">+72%</span>
            </div>
            <div class="separator separator-dashed mb-4"></div>
            <div class="d-flex align-items-center">
              <div class="symbol symbol-40px me-4">
                <span class="symbol-label bg-light-info">
                  <i class="ki-duotone ki-briefcase fs-2 text-info"><span class="path1"></span><span class="path2"></span></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold text-gray-800 fs-6">New Users</div>
                <div class="text-gray-500 fs-7">Joined this month</div>
              </div>
              <span class="badge badge-light-info fw-semibold">+45%</span>
            </div>
          </div>
        </div>
        <!--end::Activities-->
      </div>
      <!--end::Right-->
    </div>
    <!--end::Content Row-->
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "user-profile-overview",
  setup() {
    const userStore = useAuthStore();
    const statsChart = ref<HTMLCanvasElement | null>(null);

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
    // ========================================

    onMounted(async () => {
      // Jalankan animasi count-up
      countUp(animatedEarnings, 4500);
      countUp(animatedProjects, 80);
      countUp(animatedSuccessRate, 60);

      // Chart.js (tidak berubah)
      if (!statsChart.value) return;
      const { Chart, registerables } = await import("chart.js");
      Chart.register(...registerables);
      new Chart(statsChart.value, {
        type: "bar",
        data: {
          labels: ["Feb", "Mar", "Apr", "May", "Jun", "Jul"],
          datasets: [
            {
              label: "Total",
              data: [75, 100, 95, 90, 60, 105],
              backgroundColor: "rgba(200,200,220,0.3)",
              borderRadius: 6,
              barPercentage: 0.5,
            },
            {
              label: "Active",
              data: [40, 52, 52, 52, 58, 55],
              backgroundColor: "#009ef7",
              borderRadius: 6,
              barPercentage: 0.5,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { color: "#a1a5b7" }, border: { display: false } },
            y: { grid: { color: "rgba(255,255,255,0.05)" }, ticks: { color: "#a1a5b7" }, border: { display: false } },
          },
        },
      });
    });

    return {
      userStore,
      statsChart,
      // expose ke template
      animatedEarnings,
      animatedProjects,
      animatedSuccessRate,
    };
  },
});
</script>
