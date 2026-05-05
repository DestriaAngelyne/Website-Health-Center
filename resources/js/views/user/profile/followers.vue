<template>
  <div>
    <div class="mb-6">
      <h1 class="fw-bold text-gray-900 mb-1">Followers</h1>
      <div class="text-gray-500 fw-semibold fs-7">Home &mdash; User Profile &mdash; Followers</div>
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
            <h3 class="fw-bold text-gray-900 fs-4 mb-0">My Connections</h3>
            <span class="text-gray-400 fw-semibold fs-5">({{ filteredFollowers.length }})</span>
          </div>
          <select v-model="filterStatus" class="form-select form-select-sm fw-semibold"
            style="min-width: 130px; background-color: #1e1e2d; border-color: #2d2d3f; color: #a1a5b7;"
            @change="visibleCount = perPage">
            <option value="All">All</option>
            <option value="Active">Active</option>
            <option value="Following">Following</option>
          </select>
        </div>
        <!--end::Header-->

        <!--begin::Grid-->
        <div class="row g-5">
          <div class="col-md-6" v-for="f in visibleFollowers" :key="f.id">
            <div class="d-flex flex-column align-items-center text-center rounded p-8 h-100" style="border: 1px solid #2d2d3f; background: #1e1e2d;">
              <div class="position-relative mb-5">
                <div v-if="f.photo" style="width: 90px; height: 90px; border-radius: 50%; overflow: hidden; border: 3px solid #2d2d3f;">
                  <img :src="f.photo" alt="" style="width: 100%; height: 100%; object-fit: cover;" />
                </div>
                <div v-else style="width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 700; color: white; border: 3px solid #2d2d3f;" :style="`background: ${f.avatarBg};`">
                  {{ f.name.charAt(0) }}
                </div>
                <span v-if="f.online" style="position: absolute; bottom: 4px; right: 4px; width: 14px; height: 14px; background: #50cd89; border-radius: 50%; border: 2px solid #1e1e2d;"></span>
              </div>
              <div class="fw-bold text-gray-900 fs-5 mb-1">{{ f.name }}</div>
              <div class="text-gray-500 fw-semibold fs-7 mb-6">{{ f.role }} at {{ f.company }}</div>
              <div class="d-flex gap-4 mb-7">
                <div class="text-center px-5 py-3 rounded" style="border: 1px dashed #2d2d3f; min-width: 100px;">
                  <div class="fw-bold text-gray-900 fs-5">{{ f.earnings }}</div>
                  <div class="text-gray-500 fs-8 mt-1">Earnings</div>
                </div>
                <div class="text-center px-5 py-3 rounded" style="border: 1px dashed #2d2d3f; min-width: 100px;">
                  <div class="fw-bold text-gray-900 fs-5">{{ f.sales }}</div>
                  <div class="text-gray-500 fs-8 mt-1">Sales</div>
                </div>
              </div>
              <button class="btn btn-sm fw-semibold px-8" :class="f.following ? 'btn-light-primary' : 'btn-light text-gray-600'" style="min-width: 140px;" @click="f.following = !f.following">
                <i v-if="f.following" class="ki-duotone ki-check fs-5 text-primary me-1"><span class="path1"></span><span class="path2"></span></i>
                <i v-else class="ki-duotone ki-plus fs-5 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                {{ f.following ? 'Following' : 'Follow' }}
              </button>
            </div>
          </div>
        </div>
        <!--end::Grid-->

        <!--begin::Show More / Show Less-->
        <div class="text-center mt-8">
          <button v-if="visibleCount < filteredFollowers.length" class="btn btn-light fw-semibold px-10" @click="visibleCount += perPage">
            <i class="ki-duotone ki-down fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            Show More <span class="text-gray-400 ms-1 fs-7">({{ filteredFollowers.length - visibleCount }} remaining)</span>
          </button>
          <button v-else-if="filteredFollowers.length > perPage" class="btn btn-light fw-semibold px-10" @click="visibleCount = perPage">
            <i class="ki-duotone ki-up fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
            Show Less
          </button>
        </div>
        <!--end::Show More / Show Less-->

      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

export default defineComponent({
  name: "user-profile-followers",
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

    const followers = ref([
      { id: 1,  name: "Patric Watson",    role: "Art Director",       company: "Novica Co.",     earnings: "$14,560", sales: "$236,400",   following: true,  online: true,  photo: "https://randomuser.me/api/portraits/men/32.jpg",   avatarBg: "" },
      { id: 2,  name: "Olivia Larson",    role: "Art Director",       company: "Seal Inc.",      earnings: "$14,560", sales: "$236,400",   following: false, online: false, photo: "https://randomuser.me/api/portraits/women/44.jpg", avatarBg: "" },
      { id: 3,  name: "Alex Johnson",     role: "UI Designer",        company: "TechCorp",       earnings: "$9,800",  sales: "$120,000",   following: false, online: true,  photo: "",                                                 avatarBg: "#c9a227" },
      { id: 4,  name: "Paula Smith",      role: "Backend Dev",        company: "DevHub",         earnings: "$18,200", sales: "$310,000",   following: true,  online: false, photo: "",                                                 avatarBg: "#7239ea" },
      { id: 5,  name: "Grace Green",      role: "PHP Developer",      company: "WebSolve",       earnings: "$11,400", sales: "$98,700",    following: true,  online: true,  photo: "https://randomuser.me/api/portraits/women/65.jpg", avatarBg: "" },
      { id: 6,  name: "Nick Logan",       role: "Project Manager",    company: "Agile Co.",      earnings: "$22,100", sales: "$450,000",   following: false, online: false, photo: "https://randomuser.me/api/portraits/men/55.jpg",   avatarBg: "" },
      { id: 7,  name: "James Carter",     role: "DevOps Engineer",    company: "CloudBase",      earnings: "$19,300", sales: "$275,000",   following: true,  online: true,  photo: "https://randomuser.me/api/portraits/men/41.jpg",   avatarBg: "" },
      { id: 8,  name: "Emma Williams",    role: "Data Analyst",       company: "DataStream",     earnings: "$13,750", sales: "$189,500",   following: false, online: true,  photo: "https://randomuser.me/api/portraits/women/22.jpg", avatarBg: "" },
      { id: 9,  name: "Ryan Cooper",      role: "Full Stack Dev",     company: "CodeFactory",    earnings: "$21,000", sales: "$390,000",   following: true,  online: false, photo: "https://randomuser.me/api/portraits/men/68.jpg",   avatarBg: "" },
      { id: 10, name: "Sophia Turner",    role: "UX Researcher",      company: "DesignLab",      earnings: "$16,800", sales: "$204,000",   following: false, online: true,  photo: "https://randomuser.me/api/portraits/women/31.jpg", avatarBg: "" },
      { id: 11, name: "Marcus Reid",      role: "Mobile Developer",   company: "AppWorks",       earnings: "$17,500", sales: "$260,000",   following: true,  online: true,  photo: "",                                                 avatarBg: "#009ef7" },
      { id: 12, name: "Isabella Moore",   role: "Product Manager",    company: "LaunchPad",      earnings: "$24,000", sales: "$510,000",   following: false, online: false, photo: "https://randomuser.me/api/portraits/women/57.jpg", avatarBg: "" },
      { id: 13, name: "Ethan Brooks",     role: "QA Engineer",        company: "BugFree Ltd.",   earnings: "$10,200", sales: "$95,000",    following: false, online: true,  photo: "https://randomuser.me/api/portraits/men/76.jpg",   avatarBg: "" },
      { id: 14, name: "Chloe Bennett",    role: "Graphic Designer",   company: "PixelStudio",    earnings: "$8,900",  sales: "$74,300",    following: true,  online: false, photo: "https://randomuser.me/api/portraits/women/12.jpg", avatarBg: "" },
      { id: 15, name: "Liam Foster",      role: "Cloud Architect",    company: "SkyNet Inc.",    earnings: "$28,400", sales: "$620,000",   following: true,  online: true,  photo: "https://randomuser.me/api/portraits/men/23.jpg",   avatarBg: "" },
      { id: 16, name: "Natalie Hughes",   role: "SEO Specialist",     company: "RankUp Co.",     earnings: "$7,600",  sales: "$58,000",    following: false, online: false, photo: "",                                                 avatarBg: "#f64e60" },
      { id: 17, name: "Daniel King",      role: "Security Analyst",   company: "SafeNet",        earnings: "$20,500", sales: "$340,000",   following: false, online: true,  photo: "https://randomuser.me/api/portraits/men/85.jpg",   avatarBg: "" },
      { id: 18, name: "Zoe Mitchell",     role: "Content Strategist", company: "MediaHub",       earnings: "$6,400",  sales: "$43,800",    following: true,  online: true,  photo: "https://randomuser.me/api/portraits/women/83.jpg", avatarBg: "" },
      { id: 19, name: "Oscar Nguyen",     role: "AI Engineer",        company: "NeuralWorks",    earnings: "$32,100", sales: "$780,000",   following: true,  online: false, photo: "",                                                 avatarBg: "#50cd89" },
      { id: 20, name: "Ava Richardson",   role: "Scrum Master",       company: "SprintHQ",       earnings: "$15,300", sales: "$218,000",   following: false, online: true,  photo: "https://randomuser.me/api/portraits/women/90.jpg", avatarBg: "" },
      { id: 21, name: "Henry Collins",    role: "Backend Developer",  company: "ServerSide Co.", earnings: "$23,700", sales: "$412,000",   following: true,  online: true,  photo: "https://randomuser.me/api/portraits/men/14.jpg",   avatarBg: "" },
      { id: 22, name: "Mia Perez",        role: "UI/UX Designer",     company: "CraftUI",        earnings: "$12,900", sales: "$167,500",   following: false, online: false, photo: "https://randomuser.me/api/portraits/women/36.jpg", avatarBg: "" },
      { id: 23, name: "Benjamin Scott",   role: "Data Scientist",     company: "InsightLab",     earnings: "$29,500", sales: "$670,000",   following: true,  online: true,  photo: "https://randomuser.me/api/portraits/men/60.jpg",   avatarBg: "" },
      { id: 24, name: "Charlotte Adams",  role: "HR Manager",         company: "PeopleFirst",    earnings: "$11,100", sales: "$88,200",    following: false, online: true,  photo: "",                                                 avatarBg: "#ffc700" },
      { id: 25, name: "Lucas Baker",      role: "Game Developer",     company: "PixelQuest",     earnings: "$18,700", sales: "$295,000",   following: true,  online: false, photo: "https://randomuser.me/api/portraits/men/37.jpg",   avatarBg: "" },
      { id: 26, name: "Amelia Nelson",    role: "Marketing Lead",     company: "GrowthHive",     earnings: "$14,200", sales: "$198,000",   following: false, online: true,  photo: "https://randomuser.me/api/portraits/women/48.jpg", avatarBg: "" },
      { id: 27, name: "Elijah Carter",    role: "Blockchain Dev",     company: "CryptoBase",     earnings: "$35,000", sales: "$890,000",   following: true,  online: true,  photo: "",                                                 avatarBg: "#7239ea" },
      { id: 28, name: "Harper Evans",     role: "iOS Developer",      company: "AppleSeed",      earnings: "$26,300", sales: "$520,000",   following: false, online: false, photo: "https://randomuser.me/api/portraits/women/71.jpg", avatarBg: "" },
      { id: 29, name: "Sebastian Wright", role: "CTO",                company: "VisionTech",     earnings: "$48,000", sales: "$1,200,000", following: true,  online: true,  photo: "https://randomuser.me/api/portraits/men/91.jpg",   avatarBg: "" },
    ]);

    const filteredFollowers = computed(() => {
      if (filterStatus.value === "Following") return followers.value.filter((f) => f.following);
      if (filterStatus.value === "Active") return followers.value.filter((f) => f.online);
      return followers.value;
    });
    const visibleFollowers = computed(() => filteredFollowers.value.slice(0, visibleCount.value));

    return {
      userStore,
      filteredFollowers,
      visibleFollowers,
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
