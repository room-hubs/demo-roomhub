<template>
  <div class="group relative overflow-hidden rounded-3xl">
    <img
      :src="promotion.image"
      class="h-120 w-full object-cover transition duration-500 group-hover:scale-105"
    />

    <div
      class="absolute inset-0 bg-linear-to-r from-green-900/80 via-green-700/50 to-transparent"
    />

    <div class="absolute left-10 top-1/2 -translate-y-1/2 text-white">
      <p class="text-5xl font-black uppercase">
        {{ promotion.discount }}
      </p>

      <h3 class="mt-3 text-4xl font-bold">
        {{ promotion.title }}
      </h3>

      <button class="mt-8 rounded-xl bg-white px-6 py-3 font-semibold text-green-700">
        Explore Now
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  promotion: Object,
});
</script>
