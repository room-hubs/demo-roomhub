<template>
  <NuxtLink to="/" class="group flex gap-4 rounded-sm bg-white p-4">
    <img :src="promotion.image" class="h-28 w-40 shrink-0 rounded-2xl object-cover" />

    <div class="flex flex-1 flex-col justify-between">
      <div class="hover:text-green-500">
        <div class="flex justify-between items-start">
          <h3 class="line-clamp-2 font-semibold text-lg leading-tight">
            {{ promotion.title }}
          </h3>
          <span class="font-bold text-xl text-red-600"> ${{ promotion.price }} </span>
        </div>

        <p class="mt-1 text-sm text-gray-500">
          {{ promotion.location }}
        </p>

        <div class="mt-3 text-sm text-gray-600">
          {{ promotion.bedroom }} BR · {{ promotion.bathroom }} BA ·
          {{ promotion.floor }} Floor
        </div>
      </div>

      <span class="mt-auto text-xs text-gray-400 pt-2">
        Available from {{ promotion.date }}
      </span>
    </div>
  </NuxtLink>
</template>

<script setup>
defineProps({
  promotion: {
    type: Object,
    required: true,
  },
});
</script>
