<template>
  <div>
    <div class="mb-6">
      <h1 class="fw-bold text-gray-900 mb-1">Campaigns</h1>
      <div class="text-gray-500 fw-semibold fs-7">Home &mdash; User Profile &mdash; Campaigns</div>
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
            <h3 class="fw-bold text-gray-900 fs-4 mb-0">My Campaigns</h3>
            <span class="text-gray-400 fw-semibold fs-6">{{ selectedPeriod }}</span>
          </div>
          <div class="d-flex gap-3">
            <select v-model="selectedPeriod" class="form-select form-select-sm fw-semibold"
              style="min-width: 130px; background-color: #1e1e2d; border-color: #2d2d3f; color: #a1a5b7;">
              <option>7 Days</option>
              <option>30 Days</option>
              <option>90 Days</option>
              <option>1 Year</option>
            </select>
            <button class="btn btn-primary btn-sm fw-semibold px-5">Add Campaign</button>
          </div>
        </div>
        <!--end::Header-->

        <!--begin::Grid-->
        <div class="row g-5">
          <div class="col-md-4" v-for="c in campaigns" :key="c.id">
            <div class="rounded p-7 h-100" style="background: #1e1e2d; border: 1px solid #2d2d3f;">
              <!--begin::Top Row-->
              <div class="d-flex align-items-center justify-content-between mb-6">
                <div class="d-flex align-items-center gap-3">
                  <div class="d-flex align-items-center justify-content-center rounded" style="width: 48px; height: 48px;" :style="`background: ${c.iconBg};`">
                    <img v-if="c.imgIcon" :src="c.imgIcon" :alt="c.name" style="width: 28px; height: 28px; object-fit: contain;" />
                    <i v-else :class="`ki-duotone ki-${c.icon} fs-2`" :style="`color: ${c.iconColor};`"><span class="path1"></span><span class="path2"></span></i>
                  </div>
                  <span class="fw-semibold text-gray-400 fs-6">{{ c.name }}</span>
                </div>
                <div style="cursor: pointer; opacity: 0.5;">
                  <i class="ki-duotone ki-element-plus fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                </div>
              </div>
              <!--end::Top Row-->
              <div class="fw-bold text-gray-900 mb-4" style="font-size: 36px; letter-spacing: -1px;">{{ c.value }}</div>
              <div class="d-flex align-items-center gap-2 mb-5">
                <i class="ki-duotone fs-5" :class="c.changePositive ? 'ki-arrow-up-right text-success' : 'ki-arrow-down-right text-danger'"><span class="path1"></span><span class="path2"></span></i>
                <span class="fw-bold fs-6" :class="c.changePositive ? 'text-success' : 'text-danger'">{{ c.change }}</span>
                <span class="text-gray-500 fw-semibold fs-7">{{ c.changeLabel }}</span>
              </div>
              <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold fs-8 px-3 py-1 rounded" style="background: #2d2d3f; color: #a1a5b7;">{{ c.statValue }}</span>
                <span class="text-gray-500 fw-semibold fs-7">{{ c.statLabel }}</span>
              </div>
            </div>
          </div>
        </div>
        <!--end::Grid-->

      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "user-profile-campaigns",
  setup() {
    const userStore = useAuthStore();
    const selectedPeriod = ref("30 Days");

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

    const campaigns = ref([
      { id: 1, name: "Twitch Posts",         value: "$500.00", change: "+40.5%",  changeLabel: "more impressions",         changePositive: true,  statValue: "0.5%", statLabel: "MRR",            iconBg: "rgba(114,57,234,0.2)",   iconColor: "#7239ea", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/twitch.svg" },
      { id: 2, name: "Twitter Followers",    value: "807k",    change: "+17.62%", changeLabel: "Followers growth",         changePositive: true,  statValue: "5%",   statLabel: "New trials",      iconBg: "rgba(29,161,242,0.15)",  iconColor: "#1da1f2", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/twitter.svg" },
      { id: 3, name: "Spotify Listeners",    value: "1,073",   change: "+10.45%", changeLabel: "Less comments than usual", changePositive: false, statValue: "40%",  statLabel: "Impressions",     iconBg: "rgba(30,215,96,0.15)",   iconColor: "#1ed760", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/spotify.svg" },
      { id: 4, name: "Pinterest Posts",      value: "97",      change: "+8.3%",   changeLabel: "more saves",               changePositive: true,  statValue: "12%",  statLabel: "Engagement",      iconBg: "rgba(230,0,35,0.15)",    iconColor: "#e60023", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/pinterest.svg" },
      { id: 5, name: "Github Contributes",   value: "4,109",   change: "+22.1%",  changeLabel: "more commits",             changePositive: true,  statValue: "78%",  statLabel: "Active repos",    iconBg: "rgba(255,255,255,0.05)", iconColor: "#ffffff", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/github.svg" },
      { id: 6, name: "Youtube Subscribers",  value: "354",     change: "+5.7%",   changeLabel: "subscriber growth",        changePositive: true,  statValue: "20%",  statLabel: "Watch time",      iconBg: "rgba(255,0,0,0.15)",     iconColor: "#ff0000", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/youtube.svg" },
      { id: 7, name: "Instagram Reach",      value: "52.4k",   change: "-3.2%",   changeLabel: "reach dropped",            changePositive: false, statValue: "6.8%", statLabel: "Story views",     iconBg: "rgba(225,48,108,0.15)",  iconColor: "#e1306c", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/instagram.svg" },
      { id: 8, name: "LinkedIn Impressions", value: "18.9k",   change: "+13.4%",  changeLabel: "post impressions",         changePositive: true,  statValue: "3.1%", statLabel: "Connection rate", iconBg: "rgba(0,119,181,0.15)",   iconColor: "#0077b5", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/linkedin.svg" },
      { id: 9, name: "TikTok Views",         value: "1.2M",    change: "+65.0%",  changeLabel: "video views",              changePositive: true,  statValue: "9.4%", statLabel: "Share rate",      iconBg: "rgba(255,255,255,0.05)", iconColor: "#ffffff", imgIcon: "https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/tiktok.svg" },
    ]);

    return {
      userStore,
      campaigns,
      selectedPeriod,
      // expose ke template
      animatedEarnings,
      animatedProjects,
      animatedSuccessRate,
    };
  },
});
</script>
