<template>
  <div class="relative z-10 w-full flex justify-center">
    <form
      @submit.prevent="searchProperty"
      class="flex w-full max-w-5xl flex-col sm:flex-row items-stretch sm:items-center gap-2 rounded-md sm:rounded-md bg-gray-100 p-3 sm:p-4 shadow-xl backdrop-blur-md z-5"
    >
      <!-- Keyword -->
      <div class="relative flex-1 min-w-0">
        <MagnifyingGlassIcon
          class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
        />
        <input
          v-model="form.keyword"
          type="text"
          placeholder="Search property..."
          class="h-12 w-full rounded-xl border border-gray-200 bg-white pl-11 pr-4 text-sm outline-none focus:border-green-500 focus:ring-1 focus:ring-green-100"
        />
      </div>

      <!-- Province + Type -->
      <div class="flex gap-2">
        <!-- Province custom dropdown -->
        <div
          class="relative w-1/2 sm:w-36 md:w-40"
          v-click-outside="() => (provinceOpen = false)"
        >
          <button
            type="button"
            @click="
              provinceOpen = !provinceOpen;
              typeOpen = false;
            "
            class="flex h-12 w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-3 sm:px-4 text-sm text-left outline-none focus:border-green-500 focus:ring-1 focus:ring-green-100"
          >
            <span :class="form.province ? 'text-gray-900' : 'text-gray-400'">
              {{ form.province || "Province" }}
            </span>
            <ChevronDownIcon
              class="h-4 w-4 text-gray-400 transition-transform duration-200"
              :class="provinceOpen ? 'rotate-180' : ''"
            />
          </button>

          <Transition
            enter-active-class="transition duration-200 ease-out origin-top"
            enter-from-class="opacity-0 scale-y-0 -translate-y-1"
            enter-to-class="opacity-100 scale-y-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in origin-top"
            leave-from-class="opacity-100 scale-y-100 translate-y-0"
            leave-to-class="opacity-0 scale-y-0 -translate-y-1"
          >
            <ul
              v-if="provinceOpen"
              class="absolute left-0 top-full z-30 mt-2 w-full overflow-hidden rounded-xl border border-gray-100 bg-white py-1 shadow-lg"
            >
              <li
                @click="selectProvince('')"
                class="cursor-pointer px-4 py-2 text-sm text-gray-400 hover:bg-green-50 hover:text-green-700"
              >
                Province
              </li>
              <li
                v-for="p in provinces"
                :key="p"
                @click="selectProvince(p)"
                class="cursor-pointer px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700"
              >
                {{ p }}
              </li>
            </ul>
          </Transition>
        </div>

        <!-- Type custom dropdown -->
        <div
          class="relative w-1/2 sm:w-36 md:w-40"
          v-click-outside="() => (typeOpen = false)"
        >
          <button
            type="button"
            @click="
              typeOpen = !typeOpen;
              provinceOpen = false;
            "
            class="flex h-12 w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-3 sm:px-4 text-sm text-left outline-none focus:border-green-500 focus:ring-1 focus:ring-green-100"
          >
            <span :class="form.type ? 'text-gray-900' : 'text-gray-400'">
              {{ form.type || "Type" }}
            </span>
            <ChevronDownIcon
              class="h-4 w-4 text-gray-400 transition-transform duration-200"
              :class="typeOpen ? 'rotate-180' : ''"
            />
          </button>

          <Transition
            enter-active-class="transition duration-200 ease-out origin-top"
            enter-from-class="opacity-0 scale-y-0 -translate-y-1"
            enter-to-class="opacity-100 scale-y-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in origin-top"
            leave-from-class="opacity-100 scale-y-100 translate-y-0"
            leave-to-class="opacity-0 scale-y-0 -translate-y-1"
          >
            <ul
              v-if="typeOpen"
              class="absolute left-0 top-full z-30 mt-2 w-full overflow-hidden rounded-xl border border-gray-100 bg-white py-1 shadow-lg"
            >
              <li
                @click="selectType('')"
                class="cursor-pointer px-4 py-2 text-sm text-gray-400 hover:bg-green-50 hover:text-green-700"
              >
                Type
              </li>
              <li
                v-for="t in propertyTypes"
                :key="t"
                @click="selectType(t)"
                class="cursor-pointer px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700"
              >
                {{ t }}
              </li>
            </ul>
          </Transition>
        </div>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        class="flex h-12 w-full sm:w-12 items-center justify-center gap-2 rounded-full bg-green-600 text-white transition hover:bg-green-700 active:scale-95"
      >
        <MagnifyingGlassIcon class="h-5 w-5" />
        <span class="sm:hidden text-sm font-medium">Search</span>
      </button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onUnmounted } from "vue";
import { MagnifyingGlassIcon, ChevronDownIcon } from "@heroicons/vue/24/outline";

const form = reactive({
  keyword: "",
  province: "",
  type: "",
});

const provinces = ["Phnom Penh", "Siem Reap", "Battambang", "Kampot", "Sihanoukville"];
const propertyTypes = ["Apartment", "House", "Room", "Villa", "Office", "Land"];

const provinceOpen = ref(false);
const typeOpen = ref(false);

function selectProvince(p) {
  form.province = p;
  provinceOpen.value = false;
}

function selectType(t) {
  form.type = t;
  typeOpen.value = false;
}

const searchProperty = () => {
  console.log("Search:", form);
  // emit or call API here, e.g.:
  // emit('search', { ...form })
};

// Minimal click-outside directive (no external dependency needed)
const vClickOutside = {
  mounted(el, binding) {
    el.__clickOutsideHandler__ = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event);
      }
    };
    document.addEventListener("click", el.__clickOutsideHandler__, true);
  },
  unmounted(el) {
    document.removeEventListener("click", el.__clickOutsideHandler__, true);
  },
};
</script>
