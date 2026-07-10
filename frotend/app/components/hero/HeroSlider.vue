<template>
  <section class="relative isolate overflow-hidden">
    <!-- Slide Track -->
    <div
      class="relative w-full h-[220px] xs:h-[260px] sm:h-[300px] md:h-[380px] lg:h-[420px] overflow-hidden"
      style="width: 100%; overflow: hidden"
    >
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
          <!-- Background: image already contains all text/design baked in -->
          <img
            :src="slide.image"
            :alt="slide.alt"
            class="absolute inset-0 h-full w-full object-cover object-center select-none"
            draggable="false"
          />
        </div>
      </div>

      <!-- DOTS -->
      <div
        class="absolute bottom-3 sm:bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-1.5 sm:gap-2"
      >
        <button
          v-for="(slide, index) in slides"
          :key="slide.id"
          @click="goTo(index)"
          class="h-1.5 w-1.5 sm:h-2.5 sm:w-2.5 rounded-full transition-all duration-300"
          :class="
            realIndex === index ? 'w-5 sm:w-7 bg-white' : 'bg-white/50 hover:bg-white'
          "
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from "vue";

const slides = [
  {
    id: 1,
    image: "/picture/banner.png",
    alt: "Find a home easily - Buy, Rent, Quick Sale",
  },
  {
    id: 2,
    image: "/picture/banner-1.png",
    alt: "Have a Short-Stay Property? List it on Harbor Property",
  },
];

// Clone of the first slide appended at the end -> lets the track always move forward
const extendedSlides = computed(() => [...slides, slides[0]]);

const current = ref(0); // index into extendedSlides
const noTransition = ref(false); // true only during the instant snap-back
const track = ref(null);
let timer = null;
let advancing = false; // guard against double-advance while a snap is pending

const realIndex = computed(() => current.value % slides.length);

function nextSlide() {
  if (advancing) return;
  advancing = true;
  current.value++;
}

// Fires when the CSS transform transition actually finishes
function handleTransitionEnd(e) {
  if (e.propertyName !== "transform") return;

  // We just finished animating onto the cloned slide -> snap back invisibly
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
