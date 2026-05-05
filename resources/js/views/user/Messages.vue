<template>
  <div>
    <!--begin::Page Header-->
    <div class="mb-6">
      <h1 class="fw-bold mb-1" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Messages</h1>
      <div class="text-gray-500 fw-semibold fs-7">Dashboard &mdash; Messages</div>
    </div>
    <!--end::Page Header-->

    <div class="row g-5" style="height: calc(100vh - 200px); min-height: 500px;">

      <!--begin::Contacts-->
      <div class="col-md-4">
        <div class="card h-100" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">
          <div class="card-header border-0 pt-6 pb-4">
            <div class="w-100">
              <div class="fw-bold fs-5 mb-4" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">Inbox</div>
              <div class="position-relative">
                <i class="ki-duotone ki-magnifier fs-4 position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #565674;">
                  <span class="path1"></span><span class="path2"></span>
                </i>
                <input v-model="searchContact" type="text" placeholder="Search messages..."
                  class="form-control ps-10 fs-7"
                  :style="`background: ${isDark ? '#2d2d3f' : '#f9f9f9'}; border-color: ${isDark ? '#3d3d5f' : '#e4e6ef'}; color: ${isDark ? '#ffffff' : '#181c32'};`" />
              </div>
            </div>
          </div>
          <div class="card-body pt-0 overflow-auto">
            <div v-for="contact in filteredContacts" :key="contact.id"
              class="contact-item d-flex align-items-center gap-3 p-3 rounded mb-1"
              :class="{ 'contact-item-active': selectedContact?.id === contact.id }"
              :style="selectedContact?.id === contact.id
                ? `background: rgba(0,158,247,0.12);`
                : `background: transparent;`"
              @click="selectContact(contact)"
              style="cursor: pointer;">
              <div class="position-relative flex-shrink-0">
                <div :style="`width: 42px; height: 42px; border-radius: 50%; background: ${contact.color}; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: white;`">
                  {{ contact.name.charAt(0) }}
                </div>
                <span v-if="contact.online"
                  style="position: absolute; bottom: 1px; right: 1px; width: 10px; height: 10px; background: #50cd89; border-radius: 50%; border: 2px solid;"
                  :style="`border-color: ${isDark ? '#1e1e2d' : '#ffffff'};`"></span>
              </div>
              <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div class="fw-bold fs-7" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">{{ contact.name }}</div>
                  <div class="fs-8 text-gray-500">{{ contact.time }}</div>
                </div>
                <div class="fs-8 text-truncate" :style="`color: ${isDark ? '#565674' : '#a1a5b7'};`">{{ contact.lastMsg }}</div>
              </div>
              <span v-if="contact.unread > 0"
                style="background: #009ef7; color: white; font-size: 10px; font-weight: 700; min-width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                {{ contact.unread }}
              </span>
            </div>
          </div>
        </div>
      </div>
      <!--end::Contacts-->

      <!--begin::Chat-->
      <div class="col-md-8">
        <div class="card h-100 d-flex flex-column" :style="`background: ${isDark ? '#1e1e2d' : '#ffffff'};`">

          <!--begin::No Contact Selected-->
          <div v-if="!selectedContact" class="flex-grow-1 d-flex flex-column align-items-center justify-content-center">
            <i class="ki-duotone ki-message-text-2 fs-5x text-gray-300 mb-4">
              <span class="path1"></span><span class="path2"></span><span class="path3"></span>
            </i>
            <div class="fw-bold fs-5 text-gray-400 mb-2">Select a conversation</div>
            <div class="text-gray-500 fs-7">Choose a contact to start messaging</div>
          </div>
          <!--end::No Contact Selected-->

          <!--begin::Chat Active-->
          <template v-if="selectedContact">
            <!--begin::Chat Header-->
            <div class="card-header border-0 pt-5 pb-4"
              :style="`border-bottom: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'} !important;`">
              <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                  <div :style="`width: 42px; height: 42px; border-radius: 50%; background: ${selectedContact.color}; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: white;`">
                    {{ selectedContact.name.charAt(0) }}
                  </div>
                  <span v-if="selectedContact.online"
                    style="position: absolute; bottom: 1px; right: 1px; width: 10px; height: 10px; background: #50cd89; border-radius: 50%; border: 2px solid;"
                    :style="`border-color: ${isDark ? '#1e1e2d' : '#ffffff'};`"></span>
                </div>
                <div>
                  <div class="fw-bold fs-6" :style="`color: ${isDark ? '#ffffff' : '#181c32'};`">{{ selectedContact.name }}</div>
                  <div class="fs-8" :style="`color: ${selectedContact.online ? '#50cd89' : '#a1a5b7'};`">
                    {{ selectedContact.online ? 'Online' : 'Offline' }}
                  </div>
                </div>
              </div>
            </div>
            <!--end::Chat Header-->

            <!--begin::Messages-->
            <div ref="msgContainer" class="flex-grow-1 p-6 overflow-auto" style="max-height: calc(100% - 140px);">
              <div v-for="(msg, i) in selectedContact.messages" :key="i"
                class="d-flex mb-4" :class="msg.fromMe ? 'justify-content-end' : 'justify-content-start'">
                <div style="max-width: 70%;">
                  <div class="px-4 py-3 rounded-3 fs-7 fw-semibold"
                    :style="msg.fromMe
                      ? `background: #009ef7; color: #ffffff; border-radius: 16px 16px 4px 16px !important;`
                      : `background: ${isDark ? '#2d2d3f' : '#f5f8fa'}; color: ${isDark ? '#ffffff' : '#181c32'}; border-radius: 16px 16px 16px 4px !important;`">
                    {{ msg.text }}
                  </div>
                  <div class="fs-8 text-gray-500 mt-1" :class="msg.fromMe ? 'text-end' : 'text-start'">{{ msg.time }}</div>
                </div>
              </div>
            </div>
            <!--end::Messages-->

            <!--begin::Input-->
            <div class="p-4" :style="`border-top: 1px solid ${isDark ? '#2d2d3f' : '#eff2f5'};`">
              <div class="d-flex align-items-center gap-3">
                <input v-model="newMessage" type="text" placeholder="Type a message..."
                  class="form-control fs-7"
                  :style="`background: ${isDark ? '#2d2d3f' : '#f9f9f9'}; border-color: ${isDark ? '#3d3d5f' : '#e4e6ef'}; color: ${isDark ? '#ffffff' : '#181c32'}; border-radius: 24px; padding: 10px 18px;`"
                  @keyup.enter="sendMessage" />
                <button class="btn btn-primary btn-sm" style="border-radius: 50%; width: 40px; height: 40px; padding: 0; flex-shrink: 0;"
                  @click="sendMessage">
                  <i class="ki-duotone ki-send fs-4"><span class="path1"></span><span class="path2"></span></i>
                </button>
              </div>
            </div>
            <!--end::Input-->
          </template>
          <!--end::Chat Active-->

        </div>
      </div>
      <!--end::Chat-->

    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, inject, computed, nextTick, Ref } from "vue";

export default defineComponent({
  name: "user-messages",
  setup() {
    const isDark = inject<Ref<boolean>>("isDark", ref(true));
    const searchContact = ref("");
    const newMessage = ref("");
    const msgContainer = ref<HTMLElement | null>(null);

    const contacts = ref([
      {
        id: 1, name: "Nick Logan", online: true, time: "Just now", unread: 2,
        lastMsg: "Hey! Are you available?",
        color: "linear-gradient(135deg, #009ef7, #0078d4)",
        messages: [
          { text: "Hey! Are you available for a quick call?", time: "10:30 AM", fromMe: false },
          { text: "Sure! Give me 5 minutes.", time: "10:31 AM", fromMe: true },
          { text: "Great, I'll call you then.", time: "10:31 AM", fromMe: false },
          { text: "Sounds good!", time: "10:32 AM", fromMe: true },
        ],
      },
      {
        id: 2, name: "Sarah Miller", online: true, time: "5 min", unread: 1,
        lastMsg: "Thanks for the update!",
        color: "linear-gradient(135deg, #f64e60, #ee2d41)",
        messages: [
          { text: "I reviewed the project files.", time: "09:15 AM", fromMe: false },
          { text: "What do you think?", time: "09:16 AM", fromMe: true },
          { text: "Thanks for the update!", time: "09:20 AM", fromMe: false },
        ],
      },
      {
        id: 3, name: "Grace Green", online: false, time: "1 hr", unread: 0,
        lastMsg: "See you tomorrow!",
        color: "linear-gradient(135deg, #50cd89, #47be7d)",
        messages: [
          { text: "Are we still meeting tomorrow?", time: "Yesterday", fromMe: true },
          { text: "Yes, 10 AM works for me.", time: "Yesterday", fromMe: false },
          { text: "See you tomorrow!", time: "Yesterday", fromMe: true },
        ],
      },
      {
        id: 4, name: "James Brown", online: false, time: "2 hr", unread: 0,
        lastMsg: "I'll send it over now.",
        color: "linear-gradient(135deg, #ffc700, #f1bc00)",
        messages: [
          { text: "Can you send me the report?", time: "08:00 AM", fromMe: true },
          { text: "I'll send it over now.", time: "08:05 AM", fromMe: false },
        ],
      },
      {
        id: 5, name: "Emma Wilson", online: true, time: "3 hr", unread: 0,
        lastMsg: "Perfect, let's go with that.",
        color: "linear-gradient(135deg, #7239ea, #5014d0)",
        messages: [
          { text: "Which design do you prefer?", time: "07:30 AM", fromMe: false },
          { text: "I like the second option more.", time: "07:35 AM", fromMe: true },
          { text: "Perfect, let's go with that.", time: "07:36 AM", fromMe: false },
        ],
      },
    ]);

    const selectedContact = ref<typeof contacts.value[0] | null>(null);

    const filteredContacts = computed(() => {
      if (!searchContact.value) return contacts.value;
      return contacts.value.filter(c =>
        c.name.toLowerCase().includes(searchContact.value.toLowerCase())
      );
    });

    const selectContact = (contact: typeof contacts.value[0]) => {
      selectedContact.value = contact;
      contact.unread = 0;
      nextTick(() => {
        if (msgContainer.value) {
          msgContainer.value.scrollTop = msgContainer.value.scrollHeight;
        }
      });
    };

    const sendMessage = () => {
      if (!newMessage.value.trim() || !selectedContact.value) return;
      selectedContact.value.messages.push({
        text: newMessage.value.trim(),
        time: new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }),
        fromMe: true,
      });
      selectedContact.value.lastMsg = newMessage.value.trim();
      newMessage.value = "";
      nextTick(() => {
        if (msgContainer.value) {
          msgContainer.value.scrollTop = msgContainer.value.scrollHeight;
        }
      });
    };

    return { isDark, contacts, selectedContact, filteredContacts, searchContact, newMessage, msgContainer, selectContact, sendMessage };
  },
});
</script>
