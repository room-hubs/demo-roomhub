<!-- PropertyContact.vue -->
<template>
  <div class="bg-white p-5 space-y-5">
    <div class="flex items-center gap-4">
      <div
        class="w-14 h-14 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center shrink-0"
      >
        <img v-if="avatar" :src="avatar" alt="host" class="w-full h-full object-cover" />
        <span v-else class="text-gray-500 font-semibold">{{ initials }}</span>
      </div>
      <div>
        <h3 class="text-base font-semibold text-gray-900">{{ name }}</h3>
        <p class="text-sm text-gray-500">Host / Owner</p>
      </div>
    </div>

    <div class="space-y-3">
      <a
        :href="`tel:${telHref}`"
        class="flex items-center justify-center gap-2 w-full h-12 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition"
      >
        <PhoneIcon class="w-5 h-5" />
        <span>CALL</span>
        <span class="text-sm font-normal opacity-90">{{ phone }}</span>
      </a>

      <a
        v-if="whatsapp"
        :href="`https://wa.me/${whatsapp}`"
        target="_blank"
        rel="noopener noreferrer"
        class="flex items-center justify-center gap-2 w-full h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition"
      >
        <ChatBubbleLeftRightIcon class="w-5 h-5" />
        <span>Telegram</span>
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { PhoneIcon, ChatBubbleLeftRightIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
  name: { type: String, default: "Host Name" },
  phone: { type: String, required: true },
  whatsapp: { type: String, default: "" },
  avatar: { type: String, default: "" },
});

const initials = computed(() => {
  if (!props.name) return "H";
  return props.name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase();
});

const telHref = computed(() => props.phone.replace(/[^\d+]/g, ""));
</script>
