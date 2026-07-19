<script setup>
import { advetigeSidebar } from "~/data/rooms";
</script>
<template>
  <div class="sticky top-24 space-y-5">
    <NuxtLink
      v-for="item in advetigeSidebar"
      :key="item.id"
      to="#"
      class="block overflow-hidden bg-white shadow-sm ring-1 ring-gray-100 transition duration-300 hover:-translate-y-1 hover:shadow-lg"
    >
      <img :src="item.banner" :alt="item.alt" class="w-full object-cover" />
    </NuxtLink>
  </div>
</template>
// 1630
