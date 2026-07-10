<template>
  <div class="w-full">
    <div class="max-w-360 mx-auto px-4 lg:px-6">
      <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-2 lg:h-150">
        <!-- Top Right Buttons (Favorite + Share only) -->
        <div class="absolute top-4 right-4 flex items-center gap-2 z-10">
          <button
            class="bg-white/90 hover:bg-white px-4 py-2 rounded-xl shadow cursor-pointer flex items-center gap-2 text-sm"
          >
            <HeartIcon class="w-5 h-5 text-gray-700" />
            <span class="hidden sm:inline">តាមដាន</span>
          </button>

          <button
            class="bg-white/90 hover:bg-white px-4 py-2 rounded-xl cursor-pointer shadow flex items-center gap-2 text-sm"
          >
            <ShareIcon class="w-5 h-5 text-gray-700" />
            <span class="hidden sm:inline">ចែករំលែក</span>
          </button>
        </div>

        <!-- Main Image -->
        <div
          class="overflow-hidden rounded-t-2xl lg:rounded-t-none lg:rounded-l-2xl aspect-4/3 lg:aspect-auto lg:h-full"
        >
          <img
            :src="images?.[0]"
            class="w-full h-full object-cover hover:scale-105 transition duration-500"
          />
        </div>

        <!-- Right Grid: locked 2x2 with EQUAL rows -->
        <div class="grid grid-cols-2 grid-rows-[1fr_1fr] gap-2 h-75 lg:h-full">
          <div class="overflow-hidden">
            <img
              :src="images?.[1]"
              class="w-full h-full object-cover hover:scale-105 transition duration-500"
            />
          </div>

          <div class="overflow-hidden lg:rounded-tr-2xl">
            <img
              :src="images?.[2]"
              class="w-full h-full object-cover hover:scale-105 transition duration-500"
            />
          </div>

          <div class="overflow-hidden rounded-bl-2xl lg:rounded-bl-none">
            <img
              :src="images?.[3]"
              class="w-full h-full object-cover hover:scale-105 transition duration-500"
            />
          </div>

          <div class="relative overflow-hidden rounded-br-2xl">
            <img
              :src="images?.[4]"
              class="w-full h-full object-cover hover:scale-105 transition duration-500"
            />
            <div class="absolute inset-0 bg-black/20"></div>
            <button
              @click="openModal"
              class="absolute bottom-4 right-4 cursor-pointer bg-white/90 hover:bg-white text-gray-800 px-4 py-2 rounded-xl shadow flex items-center gap-2 text-sm z-10"
            >
              <span>មើលរូបទាំងអស់</span>
              <ArrowRightIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Photo Modal -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="isOpen"
          class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
          @click.self="closeModal"
        >
          <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div v-if="isOpen" class="relative w-full max-w-5xl mx-auto px-4">
              <!-- Close button -->
              <button
                @click="closeModal"
                class="absolute -top-12 right-4 sm:right-0 z-10 bg-white/90 hover:bg-white text-gray-800 rounded-full p-2 shadow cursor-pointer transition"
              >
                <XMarkIcon class="w-6 h-6" />
              </button>

              <!-- Prev arrow -->
              <button
                @click="scrollByCard(-1)"
                class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-800 rounded-full p-2 shadow cursor-pointer transition"
              >
                <ChevronLeftIcon class="w-6 h-6" />
              </button>

              <!-- Next arrow -->
              <button
                @click="scrollByCard(1)"
                class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-800 rounded-full p-2 shadow cursor-pointer transition"
              >
                <ChevronRightIcon class="w-6 h-6" />
              </button>

              <!-- Horizontal scrollable track -->
              <div
                ref="scrollTrack"
                class="flex gap-3 overflow-x-auto scroll-smooth snap-x snap-mandatory rounded-2xl p-3 sm:p-4 shadow-xl [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] scrollbar-none"
              >
                <img
                  v-for="(img, index) in images"
                  :key="index"
                  :src="img"
                  class="w-[92%] sm:w-[85%] md:w-[75%] lg:w-[65%] xl:w-[55%] 2xl:w-[45%] h-[70vh] sm:h-[75vh] md:h-[80vh] shrink-0 snap-center rounded-xl object-cover transition duration-500"
                  loading="lazy"
                />
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from "vue";
import {
  HeartIcon,
  ShareIcon,
  ArrowRightIcon,
  XMarkIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
} from "@heroicons/vue/24/outline";

defineProps({
  images: {
    type: Array,
    default: () => [],
  },
});

const isOpen = ref(false);
const scrollTrack = ref(null);

function openModal() {
  isOpen.value = true;
}

function closeModal() {
  isOpen.value = false;
}

// Scroll one "card" width left (-1) or right (1)
function scrollByCard(direction) {
  const track = scrollTrack.value;
  if (!track) return;
  const card = track.querySelector("img");
  const cardWidth = card ? card.offsetWidth + 12 : track.clientWidth * 0.7; // 12 = gap-3
  track.scrollBy({ left: direction * cardWidth, behavior: "smooth" });
}

// Lock body scroll while modal is open
watch(isOpen, (val) => {
  if (typeof document !== "undefined") {
    document.body.style.overflow = val ? "hidden" : "";
  }
});

// Keyboard support: Escape closes, arrow keys scroll
function handleKeydown(e) {
  if (!isOpen.value) return;
  if (e.key === "Escape") closeModal();
  if (e.key === "ArrowRight") scrollByCard(1);
  if (e.key === "ArrowLeft") scrollByCard(-1);
}

onMounted(() => {
  window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener("keydown", handleKeydown);
  document.body.style.overflow = "";
});
</script>
