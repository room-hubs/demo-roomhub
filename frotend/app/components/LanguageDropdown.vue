<template>
  <div ref="dropdownRef" class="relative">
    <!-- Button -->
    <button
      @click="toggleDropdown"
      class="flex items-center gap-2 rounded-full px-3 py-2 transition-all duration-200 hover:bg-gray-100 cursor-pointer"
    >
      <img
        :src="selected.flag"
        :alt="selected.name"
        class="h-5 w-7 rounded object-cover"
      />

      <span class="text-sm font-medium text-gray-700">
        {{ selected.name }}
      </span>

      <ChevronDownIcon
        class="h-4 w-4 text-gray-500 transition-transform duration-300"
        :class="{ 'rotate-180': isOpen }"
      />
    </button>

    <!-- Dropdown -->
    <Transition
      enter-active-class="transition duration-[120ms] ease-out origin-top"
      enter-from-class="opacity-0 scale-y-0 -translate-y-1"
      enter-to-class="opacity-100 scale-y-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in origin-top"
      leave-from-class="opacity-100 scale-y-100 translate-y-0"
      leave-to-class="opacity-0 scale-y-0 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-3 w-[140px] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl"
      >
        <button
          v-for="lang in languages"
          :key="lang.code"
          @click="selectLanguage(lang)"
          class="flex w-full items-center justify-between px-4 py-3 transition hover:bg-green-50"
        >
          <div class="flex items-center gap-3">
            <img :src="lang.flag" class="h-5 w-7 rounded object-cover" />

            <span class="text-sm font-medium text-gray-700">
              {{ lang.name }}
            </span>
          </div>

          <CheckIcon v-if="selected.code === lang.code" class="h-5 w-5 text-green-600" />
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const languages = [
  {
    code: "kh",
    name: "ភាសាខ្មែរ",
    flag: "/flags/kh.png",
  },
  {
    code: "en",
    name: "English",
    flag: "/flags/us.png",
  },
  {
    code: "zh",
    name: "中文",
    flag: "/flags/cn.png",
  },
];

const selected = ref(languages[0]);

const isOpen = ref(false);

const dropdownRef = ref(null);

function toggleDropdown() {
  isOpen.value = !isOpen.value;
}

function selectLanguage(lang) {
  selected.value = lang;
  isOpen.value = false;
}

function handleOutside(event) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false;
  }
}

onMounted(() => {
  document.addEventListener("click", handleOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleOutside);
});
</script>
