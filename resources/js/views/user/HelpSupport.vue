<template>
  <div>
    <!--begin::Page Header-->
    <div class="mb-6">
      <h1 class="fw-bold mb-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Help & Support</h1>
      <div class="text-gray-500 fw-semibold fs-7">Dashboard &mdash; Help & Support</div>
    </div>
    <!--end::Page Header-->

    <!--begin::Search Banner-->
    <div class="card mb-6" style="background: linear-gradient(135deg, #009ef7 0%, #0078d4 100%); border: none;">
      <div class="card-body p-10 text-center">
        <h2 class="fw-bold text-white mb-2">How can we help you?</h2>
        <p class="text-white opacity-75 mb-6 fs-6">Search our knowledge base or browse categories below</p>
        <div class="position-relative mx-auto" style="max-width: 500px;">
          <i class="ki-duotone ki-magnifier fs-3 position-absolute" style="left: 16px; top: 50%; transform: translateY(-50%); color: #565674;">
            <span class="path1"></span><span class="path2"></span>
          </i>
          <input v-model="searchFaq" type="text" placeholder="Search for answers..."
            class="form-control ps-12 py-4 fs-6 fw-semibold"
            style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15);" />
        </div>
      </div>
    </div>
    <!--end::Search Banner-->

    <div class="row g-5 mb-6">

      <!--begin::Quick Links-->
      <div class="col-md-3 col-6">
        <div class="card text-center h-100 quick-link-card" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'}; cursor: pointer;`">
          <div class="card-body p-6">
            <div class="symbol symbol-60px mx-auto mb-4">
              <span class="symbol-label bg-light-primary">
                <i class="ki-duotone ki-book fs-2x text-primary"><span class="path1"></span><span class="path2"></span></i>
              </span>
            </div>
            <div class="fw-bold fs-6 mb-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Documentation</div>
            <div class="text-gray-500 fs-7">Read our guides</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card text-center h-100 quick-link-card" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'}; cursor: pointer;`">
          <div class="card-body p-6">
            <div class="symbol symbol-60px mx-auto mb-4">
              <span class="symbol-label bg-light-success">
                <i class="ki-duotone ki-message-text-2 fs-2x text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
              </span>
            </div>
            <div class="fw-bold fs-6 mb-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Live Chat</div>
            <div class="text-gray-500 fs-7">Chat with us</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card text-center h-100 quick-link-card" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'}; cursor: pointer;`">
          <div class="card-body p-6">
            <div class="symbol symbol-60px mx-auto mb-4">
              <span class="symbol-label bg-light-warning">
                <i class="ki-duotone ki-sms fs-2x text-warning"><span class="path1"></span><span class="path2"></span></i>
              </span>
            </div>
            <div class="fw-bold fs-6 mb-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Email Us</div>
            <div class="text-gray-500 fs-7">support@myapp.com</div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card text-center h-100 quick-link-card" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'}; cursor: pointer;`">
          <div class="card-body p-6">
            <div class="symbol symbol-60px mx-auto mb-4">
              <span class="symbol-label bg-light-info">
                <i class="ki-duotone ki-call fs-2x text-info"><span class="path1"></span><span class="path2"></span></i>
              </span>
            </div>
            <div class="fw-bold fs-6 mb-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Call Us</div>
            <div class="text-gray-500 fs-7">+62 800 000 0000</div>
          </div>
        </div>
      </div>
      <!--end::Quick Links-->

    </div>

    <div class="row g-5">

      <!--begin::FAQ-->
      <div class="col-md-8">
        <div class="card" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">
                Frequently Asked Questions
              </h3>
            </div>
          </div>
          <div class="card-body pt-2">
            <div v-for="(faq, i) in filteredFaqs" :key="i" class="mb-2">
              <div class="faq-item p-4 rounded"
                :style="`background: ${isDark ? '#151521' : '#f9f9f9'}; cursor: pointer;`"
                @click="toggleFaq(i)">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="fw-semibold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">
                    {{ faq.question }}
                  </div>
                  <i :class="openFaq === i ? 'ki-up' : 'ki-down'" class="ki-duotone fs-4 text-gray-500 flex-shrink-0 ms-3">
                    <span class="path1"></span><span class="path2"></span>
                  </i>
                </div>
                <div v-show="openFaq === i" class="mt-3 text-gray-500 fw-semibold fs-7" style="line-height: 1.7;">
                  {{ faq.answer }}
                </div>
              </div>
            </div>
            <div v-if="filteredFaqs.length === 0" class="text-center py-10">
              <i class="ki-duotone ki-information-5 fs-3x text-gray-300 mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
              <div class="text-gray-500 fw-semibold">No results found for "{{ searchFaq }}"</div>
            </div>
          </div>
        </div>
      </div>
      <!--end::FAQ-->

      <!--begin::Contact Form-->
      <div class="col-md-4">
        <div class="card" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-header border-0 pt-6">
            <div class="card-title">
              <h3 class="fw-bold fs-5" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Send a Message</h3>
            </div>
          </div>
          <div class="card-body pt-2">
            <div class="mb-4">
              <label class="fw-semibold fs-7 mb-2 d-block text-gray-500">Subject</label>
              <input v-model="contactForm.subject" type="text" class="form-control fs-7"
                placeholder="How can we help?"
                :style="`background: ${isDark ? '#2d2d3f' : '#f9f9f9'}; border-color: ${isDark ? '#3d3d5f' : '#e4e6ef'}; color: ${isDark ? '#ffffff' : '#181c32'};`" />
            </div>
            <div class="mb-4">
              <label class="fw-semibold fs-7 mb-2 d-block text-gray-500">Category</label>
              <select v-model="contactForm.category" class="form-select fs-7"
                :style="`background: ${isDark ? '#2d2d3f' : '#f9f9f9'}; border-color: ${isDark ? '#3d3d5f' : '#e4e6ef'}; color: ${isDark ? '#ffffff' : '#181c32'};`">
                <option value="">Select category...</option>
                <option value="account">Account Issues</option>
                <option value="billing">Billing</option>
                <option value="technical">Technical Support</option>
                <option value="feature">Feature Request</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="mb-6">
              <label class="fw-semibold fs-7 mb-2 d-block text-gray-500">Message</label>
              <textarea v-model="contactForm.message" rows="5" class="form-control fs-7"
                placeholder="Describe your issue..."
                :style="`background: ${isDark ? '#2d2d3f' : '#f9f9f9'}; border-color: ${isDark ? '#3d3d5f' : '#e4e6ef'}; color: ${isDark ? '#ffffff' : '#181c32'}; resize: none;`"></textarea>
            </div>
            <button class="btn btn-primary w-100 fw-semibold" @click="submitContact" :disabled="submitting">
              <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
              {{ submitting ? 'Sending...' : 'Send Message' }}
            </button>
          </div>
        </div>
      </div>
      <!--end::Contact Form-->

    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, inject, computed, Ref } from "vue";

export default defineComponent({
  name: "user-help-support",
  setup() {
    const isDark = inject<Ref<boolean>>("isDark", ref(true));
    const searchFaq = ref("");
    const openFaq = ref<number | null>(null);
    const submitting = ref(false);

    const contactForm = ref({ subject: "", category: "", message: "" });

    const faqs = [
      {
        question: "How do I update my profile information?",
        answer: "Go to User Profile → Edit Profile from the sidebar or click Edit Profile in the dropdown menu at the top right. You can update your name, phone, location, bio, and more.",
      },
      {
        question: "How do I change my password?",
        answer: "Navigate to Edit Profile page. Scroll down to the Change Password section. Enter your current password, then your new password and confirm it. Click Save Changes.",
      },
      {
        question: "How do I manage my projects?",
        answer: "Go to User Profile → Projects from the sidebar. You can view all your projects, their progress, status, and deadlines from there.",
      },
      {
        question: "How do I switch between dark and light mode?",
        answer: "Click your profile avatar at the top right corner, then toggle the Dark Mode switch in the dropdown menu.",
      },
      {
        question: "How do I view my followers?",
        answer: "Navigate to User Profile → Followers from the sidebar to see all users following your profile.",
      },
      {
        question: "How do I track my activity history?",
        answer: "Go to User Profile → Activity from the sidebar. All your recent actions and events are logged there with timestamps.",
      },
      {
        question: "How do I upload documents?",
        answer: "Navigate to User Profile → Documents and click the upload button to add new files. Supported formats include PDF, DOCX, XLSX, and ZIP.",
      },
    ];

    const filteredFaqs = computed(() => {
      if (!searchFaq.value) return faqs;
      return faqs.filter(f =>
        f.question.toLowerCase().includes(searchFaq.value.toLowerCase()) ||
        f.answer.toLowerCase().includes(searchFaq.value.toLowerCase())
      );
    });

    const toggleFaq = (i: number) => {
      openFaq.value = openFaq.value === i ? null : i;
    };

    const submitContact = async () => {
      if (!contactForm.value.subject || !contactForm.value.message) return;
      submitting.value = true;
      await new Promise(r => setTimeout(r, 1500));
      submitting.value = false;
      const Swal = (await import("sweetalert2")).default;
      Swal.fire({
        icon: "success",
        title: "Message Sent!",
        text: "We'll get back to you within 24 hours.",
        confirmButtonColor: "#009ef7",
        background: isDark.value ? "#1e1e2d" : "#ffffff",
        color: isDark.value ? "#ffffff" : "#181c32",
      });
      contactForm.value = { subject: "", category: "", message: "" };
    };

    return { isDark, searchFaq, openFaq, faqs, filteredFaqs, toggleFaq, contactForm, submitting, submitContact };
  },
});
</script>

<style>
.quick-link-card { transition: transform 0.2s, box-shadow 0.2s; }
.quick-link-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; }
.faq-item { transition: background 0.2s; }
.contact-item { transition: background 0.15s; }
</style>
