<template>
  <NuxtLink v-if="room" :to="`/rooms/${room.id}`" class="group block">
    <!-- Image -->
    <div class="relative overflow-hidden rounded-2xl">
      <img
        :src="room.image"
        :alt="room.title"
        class="aspect-4/3 w-full object-cover transition duration-500 group-hover:scale-105"
      />

      <!-- Favourite -->
      <button
        @click.prevent="$emit('toggle-fav', room.id)"
        class="absolute right-3 top-3 transition duration-300 hover:scale-110"
      >
        <HeartSolidIcon v-if="isFavorite" class="h-7 w-7 text-red-500 drop-shadow-lg" />
        <HeartIcon v-else class="h-7 w-7 text-white drop-shadow-lg" />
      </button>
    </div>

    <!-- Content -->
    <div class="mt-3">
      <!-- Title + Price -->
      <div class="flex items-center justify-between gap-3">
        <h3 class="line-clamp-1 text-lg font-bold text-gray-900">
          {{ room.title }}
        </h3>

        <span class="whitespace-nowrap text-right">
          <span class="text-sm text-gray-500">តម្លៃចាប់ពី</span>
          <span class="ml-1 text-xl font-bold text-red-600">${{ room.price }}</span>
        </span>
      </div>
      <span class="flex gap-3">
        <!-- Location -->
        <p class="mt-1 line-clamp-1 text-sm text-gray-500">
          {{ room.location }}
        </p>
        <!-- Bedrooms | Floor -->
        <div class="mt-1 flex flex-wrap items-center gap-1 text-sm text-gray-500">
          <span>{{ room.bedrooms }} បន្ទប់គេង</span>
          <span>|</span>
          <span>ជាន់{{ room.floor }}</span>
        </div>
      </span>

      <!-- Date & Time -->
      <p class="mt-1 text-sm text-gray-400">
        {{ room.date }} &nbsp;&nbsp; {{ room.time }}
      </p>
    </div>
  </NuxtLink>
</template>

<script setup>
import { HeartIcon } from "@heroicons/vue/24/outline";
import { HeartIcon as HeartSolidIcon } from "@heroicons/vue/24/solid";

defineProps({
  room: Object,
  isFavorite: Boolean,
});

defineEmits(["toggle-fav"]);
</script>
