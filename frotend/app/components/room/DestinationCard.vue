<template>
  <NuxtLink :to="link" class="group block w-full">
    <div class="aspect-square overflow-hidden rounded-2xl">
      <img
        :src="image"
        :alt="title"
        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
      />
    </div>
    <p class="mt-2 line-clamp-2 text-xs text-gray-800 leading-snug sm:text-sm">
      {{ title }}
    </p>
    <p class="mt-0.5 text-sm font-bold text-red-600 sm:text-base">
      {{ count }}
    </p>
  </NuxtLink>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  count: { type: [String, Number], required: true },
  image: { type: String, required: true },
  link: { type: String, default: "/" },
});
</script>
