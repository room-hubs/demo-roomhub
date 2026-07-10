<template>
  <main class="grid h-screen w-full grid-cols-1 overflow-hidden lg:grid-cols-2">
    <!-- Left: Auto slider (hidden on mobile, form takes full width there) -->
    <div class="relative hidden overflow-hidden bg-green-800 lg:block">
      <div
        ref="track"
        class="flex h-full"
        :class="noTransition ? '' : 'transition-transform duration-700 ease-in-out'"
        :style="{
          transform: `translateX(-${current * 100}%)`,
          display: 'flex',
          width: '100%',
          height: '100%',
        }"
        @transitionend="handleTransitionEnd"
      >
        <div
          v-for="(slide, index) in extendedSlides"
          :key="index"
          class="relative h-full w-full shrink-0"
          style="width: 100%; height: 100%; flex-shrink: 0"
        >
          <img
            :src="slide.image"
            :alt="slide.alt"
            class="absolute inset-0 h-full w-full object-cover object-center select-none"
            draggable="false"
          />

          <!-- Gradient for text legibility -->
          <div
            class="absolute inset-0 bg-gradient-to-t from-green-900/90 via-green-900/30 to-transparent"
          ></div>

          <!-- Caption -->
          <div class="absolute inset-x-0 bottom-16 z-10 px-10 text-white">
            <p class="text-2xl font-semibold leading-snug max-w-md">
              {{ slide.quote }}
            </p>
            <p class="mt-4 text-sm font-medium text-white/70">
              {{ slide.author }}
            </p>
          </div>
        </div>
      </div>

      <!-- Dots -->
      <div class="absolute bottom-6 left-10 z-20 flex gap-2">
        <button
          v-for="(slide, index) in slides"
          :key="slide.id"
          @click="goTo(index)"
          class="h-1.5 rounded-full transition-all duration-300"
          :class="
            realIndex === index ? 'w-6 bg-white' : 'w-1.5 bg-white/50 hover:bg-white'
          "
        />
      </div>
    </div>

    <!-- Right: Form slot -->
    <div
      class="flex h-full items-center justify-center overflow-hidden bg-white px-4 py-10 sm:px-8 lg:px-12"
    >
      <div class="w-full max-w-md">
        <slot />
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from "vue";

const slides = [
  {
    id: 1,
    image: "/picture/auth-slide-1.jpg",
    alt: "Modern apartment interior",
    quote: "Finding my new home took minutes, not months.",
    author: "— Sophea, verified tenant",
  },
  {
    id: 2,
    image: "/picture/auth-slide-2.jpg",
    alt: "Cozy living room",
    quote: "Thousands of listings, all verified and up to date.",
    author: "— Dara, property owner",
  },
  {
    id: 3,
    image: "/picture/auth-slide-3.jpg",
    alt: "City skyline view",
    quote: "Buy, rent, or sell — all in one place.",
    author: "— Harbor Property",
  },
];

const extendedSlides = computed(() => [...slides, slides[0]]);

const current = ref(0);
const noTransition = ref(false);
const track = ref(null);
let timer = null;
let advancing = false;

const realIndex = computed(() => current.value % slides.length);

function nextSlide() {
  if (advancing) return;
  advancing = true;
  current.value++;
}

function handleTransitionEnd(e) {
  if (e.propertyName !== "transform") return;

  if (current.value === extendedSlides.value.length - 1) {
    noTransition.value = true;
    current.value = 0;

    nextTick(() => {
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          noTransition.value = false;
          advancing = false;
        });
      });
    });
  } else {
    advancing = false;
  }
}

function goTo(index) {
  noTransition.value = false;
  advancing = false;
  current.value = index;
  restartTimer();
}

function restartTimer() {
  if (timer) clearInterval(timer);
  timer = setInterval(nextSlide, 5000);
}

onMounted(() => {
  restartTimer();
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>
