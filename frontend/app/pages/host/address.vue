<template>
  <div class="min-h-screen flex items-center justify-center bg-white px-4">
    <div class="w-full max-w-2xl">
      <div class="mt-8 animate-fade-in-up" style="animation-delay: 0ms">
        <h2 class="text-xl md:text-xl font-medium text-gray-700">Set up your listing</h2>
      </div>

      <div class="mt-8 animate-fade-in-up" style="animation-delay: 120ms">
        <h3 class="text-lg md:text-xl font-medium text-gray-800">
          It's easy to create a great listing — let's start with your address.
        </h3>

        <div class="mt-6 relative">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
            />
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"
            />
          </svg>

          <input
            :value="address.formatted"
            type="text"
            readonly
            placeholder="Enter your address"
            @click="openStep(1)"
            class="w-full pl-11 pr-4 py-4 rounded-xl border border-gray-300 text-gray-800 placeholder-gray-400 cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent transition"
          />
        </div>
      </div>
    </div>

    <!-- ===================== MODAL LAYER ===================== -->

    <Teleport to="body">
      <Transition name="backdrop">
        <div
          v-if="step"
          class="fixed inset-0 bg-black/40 z-40 flex items-center justify-center px-4"
          @click.self="close"
        >
          <!-- Step 1: search-style address entry -->
          <Transition name="modal" mode="out-in">
            <div
              v-if="step === 1"
              key="step1"
              class="relative w-full max-w-[800px] bg-white rounded-3xl border border-gray-200 shadow-xl p-6 sm:p-10"
            >
              <button
                @click="close"
                class="absolute top-5 right-5 w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="w-5 h-5 text-gray-700"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18 18 6M6 6l12 12"
                  />
                </svg>
              </button>

              <h3
                class="text-2xl sm:text-3xl font-semibold text-gray-900 text-center mt-2 mb-8"
              >
                Enter your address
              </h3>

              <!-- search pill -->
              <div class="relative">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="2"
                  stroke="currentColor"
                  class="w-5 h-5 text-gray-500 absolute left-5 top-1/2 -translate-y-1/2 pointer-events-none"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                  />
                </svg>
                <input
                  v-model="query"
                  type="text"
                  placeholder="Enter your address"
                  autofocus
                  @keyup.enter="searchAddress"
                  class="w-full pl-12 pr-5 py-4 rounded-full border border-gray-300 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent transition"
                />
              </div>

              <!-- current location row -->
              <button
                @click="useCurrentLocation"
                :disabled="locating"
                class="w-full flex items-center gap-3 mt-5 py-2 rounded-xl hover:bg-gray-50 transition text-left disabled:opacity-60"
              >
                <span
                  class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-4 h-4 text-gray-700"
                    :class="{ 'animate-spin': locating }"
                  >
                    <path
                      v-if="!locating"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 19V5m0 0-5 5m5-5 5 5"
                      transform="rotate(45 12 12)"
                    />
                    <path
                      v-else
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                    />
                  </svg>
                </span>
                <span class="text-gray-800">{{
                  locating ? "Finding your location…" : "Use my current location"
                }}</span>
              </button>
            </div>

            <!-- Step 2: confirm address form -->
            <div
              v-else-if="step === 2"
              key="step2"
              class="relative w-full max-w-[800px] max-h-[90vh] bg-white rounded-3xl border border-gray-200 shadow-xl flex flex-col overflow-hidden"
            >
              <!-- header -->
              <div class="flex items-center justify-between px-6 pt-6 sm:px-10 sm:pt-8">
                <button
                  @click="openStep(1)"
                  class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5 text-gray-800"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
                    />
                  </svg>
                </button>
                <button
                  @click="close"
                  class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5 text-gray-800"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M6 18 18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>

              <!-- scrollable body -->
              <div class="px-6 sm:px-10 pb-4 overflow-y-auto">
                <h3
                  class="text-2xl sm:text-3xl font-semibold text-gray-900 text-center mt-1 mb-6"
                >
                  Confirm your address
                </h3>

                <!-- error banner: shown when geolocation couldn't resolve -->
                <div
                  v-if="locationError"
                  class="flex items-start gap-4 bg-gray-100 rounded-2xl p-5 mb-6"
                >
                  <span
                    class="w-7 h-7 rounded-full bg-red-600 flex items-center justify-center flex-shrink-0 text-white text-sm font-bold"
                    >!</span
                  >
                  <div>
                    <p class="font-semibold text-gray-900">
                      We couldn't find your location
                    </p>
                    <p class="text-gray-500 text-sm mt-0.5">
                      Please enter your address instead.
                    </p>
                  </div>
                </div>

                <!-- country / region — fixed to Cambodia for this local-only flow -->
                <div
                  class="rounded-xl border border-gray-300 pt-6 pb-2.5 px-4 mb-4 bg-gray-50"
                >
                  <span class="block text-xs text-gray-500">Country / region</span>
                  <span class="text-gray-900">Cambodia</span>
                </div>

                <!-- grouped fields -->
                <div
                  class="rounded-xl border border-gray-300 divide-y divide-gray-300 overflow-hidden"
                >
                  <input
                    v-model="form.street1"
                    type="text"
                    placeholder="Street address"
                    class="w-full px-4 py-3.5 text-gray-800 placeholder-gray-500 focus:outline-none focus:bg-gray-50 transition"
                  />
                  <input
                    v-model="form.unit"
                    type="text"
                    placeholder="Apt, floor, bldg (if applicable)"
                    class="w-full px-4 py-3.5 text-gray-800 placeholder-gray-500 focus:outline-none focus:bg-gray-50 transition"
                  />
                  <input
                    v-model="form.city"
                    type="text"
                    placeholder="City / town / village"
                    class="w-full px-4 py-3.5 text-gray-800 placeholder-gray-500 focus:outline-none focus:bg-gray-50 transition"
                  />
                  <button
                    type="button"
                    @click="openStep(3)"
                    class="w-full flex items-center justify-between px-4 py-3.5 text-left focus:outline-none focus:bg-gray-50 transition"
                  >
                    <span :class="form.province ? 'text-gray-800' : 'text-gray-500'">
                      {{ form.province || "Province / state / territory" }}
                    </span>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="w-5 h-5 text-gray-600 flex-shrink-0"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m8.25 4.5 7.5 7.5-7.5 7.5"
                      />
                    </svg>
                  </button>
                  <input
                    v-model="form.postalCode"
                    type="text"
                    placeholder="Postal code (if applicable)"
                    class="w-full px-4 py-3.5 text-gray-800 placeholder-gray-500 focus:outline-none focus:bg-gray-50 transition"
                  />
                </div>
              </div>

              <!-- sticky footer -->
              <div class="px-6 py-4 sm:px-10 sm:py-5 border-t border-gray-200">
                <button
                  @click="confirmAddress"
                  :disabled="!canConfirm"
                  class="w-full py-4 rounded-xl bg-gray-900 hover:bg-black disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-medium transition"
                >
                  Next
                </button>
              </div>
            </div>

            <!-- Step 3: province grid picker -->
            <div
              v-else-if="step === 3"
              key="step3"
              class="relative w-full max-w-[800px] max-h-[90vh] bg-white rounded-3xl border border-gray-200 shadow-xl flex flex-col overflow-hidden"
            >
              <!-- header -->
              <div class="flex items-center justify-between px-6 pt-6 sm:px-10 sm:pt-8">
                <button
                  @click="openStep(2)"
                  class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5 text-gray-800"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
                    />
                  </svg>
                </button>
                <button
                  @click="close"
                  class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5 text-gray-800"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M6 18 18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>

              <div class="px-6 sm:px-10 pb-2 overflow-y-auto">
                <h3
                  class="text-2xl sm:text-3xl font-semibold text-gray-900 text-center mt-1 mb-6"
                >
                  Select province
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pb-6">
                  <button
                    v-for="(p, i) in cambodiaProvinces"
                    :key="p"
                    type="button"
                    @click="selectProvince(p)"
                    class="province-card relative flex items-center justify-center text-center px-3 py-4 rounded-xl border transition-all duration-150 ease-out"
                    :style="{ animationDelay: `${i * 25}ms` }"
                    :class="
                      form.province === p
                        ? 'border-green-600 bg-green-50 text-green-800 font-medium scale-[0.98]'
                        : 'border-gray-200 text-gray-700 hover:border-gray-300 hover:bg-gray-50'
                    "
                  >
                    <span>{{ p }}</span>
                    <svg
                      v-if="form.province === p"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="2"
                      stroke="currentColor"
                      class="w-4 h-4 text-green-600 absolute top-2 right-2"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m4.5 12.75 6 6 9-13.5"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from "vue";

definePageMeta({
  layout: "host",
});

// step: null (closed) | 1 (search entry) | 2 (confirm form)
const step = ref(null);
const locating = ref(false);
const locationError = ref(false);
const query = ref("");

const cambodiaProvinces = [
  "Banteay Meanchey",
  "Battambang",
  "Kampong Cham",
  "Kampong Chhnang",
  "Kampong Speu",
  "Kampong Thom",
  "Kampot",
  "Kandal",
  "Kep",
  "Koh Kong",
  "Kratie",
  "Mondulkiri",
  "Oddar Meanchey",
  "Pailin",
  "Phnom Penh",
  "Preah Sihanouk",
  "Preah Vihear",
  "Prey Veng",
  "Pursat",
  "Ratanakiri",
  "Siem Reap",
  "Stung Treng",
  "Svay Rieng",
  "Takeo",
  "Tboung Khmum",
];

const form = reactive({
  street1: "",
  unit: "",
  city: "",
  province: "",
  postalCode: "",
});

const address = reactive({
  formatted: "",
});

const canConfirm = computed(() => form.street1 && form.city && form.province);

function openStep(n) {
  step.value = n;
}

function close() {
  step.value = null;
}

async function useCurrentLocation() {
  locating.value = true;
  locationError.value = false;
  try {
    // Replace with a real geolocation + reverse-geocode call.
    // navigator.geolocation.getCurrentPosition(...) -> reverse geocode API
    await new Promise((resolve, reject) => setTimeout(reject, 900)); // mocked failure path
    Object.assign(form, {
      street1: "Street 271",
      city: "Phnom Penh",
      province: "Phnom Penh",
    });
  } catch {
    // couldn't resolve a location — surface the banner and let them fill in manually
    locationError.value = true;
  } finally {
    locating.value = false;
    step.value = 2;
  }
}

function selectProvince(p) {
  form.province = p;
  // brief pause so the checkmark/highlight is visible before the popup swaps back,
  // instead of an abrupt instant jump
  setTimeout(() => {
    step.value = 2;
  }, 220);
}

function searchAddress() {
  if (!query.value.trim()) return;
  locationError.value = false;
  // Replace with a real geocode/autocomplete lookup — for now, seed street1
  // with what was typed and let the person refine the rest in step 2.
  form.street1 = query.value.trim();
  step.value = 2;
}

function confirmAddress() {
  address.formatted = [form.street1, form.city, form.province, "Cambodia"]
    .filter(Boolean)
    .join(", ");
  close();
  // TODO: persist to your listing store / emit to parent / call API
}
</script>

<style scoped>
.animate-fade-in-up {
  opacity: 0;
  animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* backdrop fade */
.backdrop-enter-active,
.backdrop-leave-active {
  transition: opacity 0.2s ease;
}
.backdrop-enter-from,
.backdrop-leave-to {
  opacity: 0;
}

/* modal scale+fade, and cross-fade between step1/step2/step3 */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.modal-enter-from {
  opacity: 0;
  transform: scale(0.96) translateY(8px);
}
.modal-leave-to {
  opacity: 0;
  transform: scale(0.98);
}

/* province grid: each card fades + scales in with a small stagger */
.province-card {
  opacity: 0;
  animation: cardIn 0.32s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes cardIn {
  from {
    opacity: 0;
    transform: translateY(6px) scale(0.94);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@media (prefers-reduced-motion: reduce) {
  .province-card {
    animation: none;
    opacity: 1;
  }
}
</style>
