<template>
  <span>
    <section class="relative overflow-visible">
      <div class="relative z-0">
        <HeroSlider />
        <div
          class="absolute inset-x-0 bottom-0 flex justify-center px-4 pb-4 sm:pb-6 lg:pb-8"
        >
          <div class="w-full max-w-6xl mt-2">
            <HeroSearch />
          </div>
        </div>
      </div>
    </section>
    <div class="h-20 sm:h-16"></div>
  </span>
</template>

<script setup>
import HeroSearch from "./HeroSearch.vue";
import HeroSlider from "./HeroSlider.vue";
</script>
