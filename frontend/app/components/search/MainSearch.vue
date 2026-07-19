<template>
  <section class="w-full flex flex-wrap sm:flex-nowrap justify-center items-center gap-3">
    <div
      class="max-w-250 w-full rounded-md p-2 flex flex-wrap lg:flex-nowrap items-center gap-2"
    >
      <!-- Location -->
      <div class="flex-1">
        <input
          v-model="locationQuery"
          type="text"
          placeholder="Search location..."
          class="h-12 w-full rounded-full border border-green-500 bg-white px-4 text-sm text-gray-700 placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:outline-none"
          @keyup.enter="applyFilters"
        />
      </div>

      <!-- Property Type -->
      <div class="relative w-full lg:w-44" v-click-outside="() => (typeOpen = false)">
        <button
          type="button"
          @click="
            typeOpen = !typeOpen;
            priceOpen = false;
          "
          class="flex h-12 w-full items-center justify-between rounded-full bg-white px-4 text-sm text-left text-gray-700 outline-none"
        >
          <span :class="selectedType ? 'text-gray-700' : 'text-gray-400'">
            {{ selectedType || "All Types" }}
          </span>
          <UIcon
            name="i-lucide-chevron-down"
            class="h-4 w-4 text-gray-400 transition-transform duration-200"
            :class="typeOpen ? 'rotate-180' : ''"
          />
        </button>

        <Transition
          enter-active-class="transition duration-[120ms] ease-out origin-top"
          enter-from-class="opacity-0 scale-y-0 -translate-y-1"
          enter-to-class="opacity-100 scale-y-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in origin-top"
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
              All Types
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

      <!-- Price -->
      <div class="relative w-full lg:w-40" v-click-outside="() => (priceOpen = false)">
        <button
          type="button"
          @click="
            priceOpen = !priceOpen;
            typeOpen = false;
          "
          class="flex h-12 w-full items-center justify-between rounded-full bg-white px-4 text-sm text-left text-gray-700 outline-none"
        >
          <span :class="selectedPrice ? 'text-gray-700' : 'text-gray-400'">
            {{ selectedPrice || "Any Price" }}
          </span>
          <UIcon
            name="i-lucide-chevron-down"
            class="h-4 w-4 text-gray-400 transition-transform duration-200"
            :class="priceOpen ? 'rotate-180' : ''"
          />
        </button>

        <Transition
          enter-active-class="transition duration-[120ms] ease-out origin-top"
          enter-from-class="opacity-0 scale-y-0 -translate-y-1"
          enter-to-class="opacity-100 scale-y-100 translate-y-0"
          leave-active-class="transition duration-100 ease-in origin-top"
          leave-from-class="opacity-100 scale-y-100 translate-y-0"
          leave-to-class="opacity-0 scale-y-0 -translate-y-1"
        >
          <ul
            v-if="priceOpen"
            class="absolute left-0 top-full z-30 mt-2 w-full overflow-hidden rounded-xl border border-gray-100 bg-white py-1 shadow-lg"
          >
            <li
              @click="selectPrice('')"
              class="cursor-pointer px-4 py-2 text-sm text-gray-400 hover:bg-green-50 hover:text-green-700"
            >
              Any Price
            </li>
            <li
              v-for="p in prices"
              :key="p"
              @click="selectPrice(p)"
              class="cursor-pointer px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700"
            >
              {{ p }}
            </li>
          </ul>
        </Transition>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <!-- Search -->
      <button
        @click="applyFilters"
        class="h-12 w-12 shrink-0 p-2 rounded-xl bg-green-700 cursor-pointer text-white transition hover:bg-green-800"
      >
        <UIcon name="i-lucide-search" class="mx-auto h-5 w-5" />
      </button>
      <!-- Filter -->
      <button
        class="h-12 w-12 shrink-0 rounded-xl bg-amber-400 text-white cursor-pointer transition hover:bg-amber-500"
      >
        <UIcon name="i-lucide-sliders-horizontal" class="mx-auto h-5 w-5" />
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";

const route = useRoute();
const router = useRouter();

const propertyTypes = ["Apartment", "Room", "Villa", "Condo"];
const prices = ["$100", "$200", "$300", "$500+"];

const locationQuery = ref("");
const selectedType = ref("");
const selectedPrice = ref("");

const typeOpen = ref(false);
const priceOpen = ref(false);

onMounted(() => {
  locationQuery.value = route.query.location || "";
  selectedType.value = route.query.type || "";
  selectedPrice.value = route.query.price || "";
});

function selectType(t) {
  selectedType.value = t;
  typeOpen.value = false;
  applyFilters();
}

function selectPrice(p) {
  selectedPrice.value = p;
  priceOpen.value = false;
  applyFilters();
}

// Push current filter state into the URL as query params
function applyFilters() {
  const query = { ...route.query };

  if (locationQuery.value) query.location = locationQuery.value;
  else delete query.location;

  if (selectedType.value) query.type = selectedType.value;
  else delete query.type;

  if (selectedPrice.value) query.price = selectedPrice.value;
  else delete query.price;

  router.push({ path: route.path, query });
}

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
