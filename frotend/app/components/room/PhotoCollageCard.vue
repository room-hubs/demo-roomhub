<template>
  <div class="mx-auto w-full max-w-[280px] rounded-2xl bg-white p-2">
    <!-- Image collage -->
    <div class="relative h-[180px] w-full">
      <img
        :src="images[0]"
        alt="room photo 1"
        class="absolute left-0 top-0 h-[70%] w-[48%] rounded-xl object-cover shadow"
      />
      <img
        :src="images[1]"
        alt="room photo 2"
        class="absolute right-0 top-0 h-[70%] w-[48%] rounded-xl object-cover shadow"
      />
      <img
        :src="images[2]"
        alt="room photo 3"
        class="absolute left-1/2 top-[35%] h-[65%] w-[48%] -translate-x-1/2 rounded-xl object-cover shadow-lg ring-4 ring-white"
      />
    </div>

    <NuxtLink
      to="/listing"
      class="mt-3 flex items-center justify-center gap-1 text-base font-medium text-gray-900 hover:text-gray-700"
    >
      មើលទាំងអស់
      <ChevronRightIcon class="h-5 w-5" />
    </NuxtLink>
  </div>
</template>

<script setup>
import { ChevronRightIcon } from "@heroicons/vue/24/outline";

defineProps({
  images: {
    type: Array,
    required: true,
  },
  link: {
    type: String,
    default: "/gallery",
  },
});
</script>
