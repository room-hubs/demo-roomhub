<template>
  <nav class="w-full h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6">

    <!-- Logo -->
    <NuxtLink to="/" class="flex items-center gap-2 text-rose-500">
      <HomeHeartIcon class="w-7 h-7" />
    </NuxtLink>

    <!-- Right side -->
    <div class="flex items-center gap-1">

      <!-- Become a host -->
      <NuxtLink
        to="/auth/login"
        class="flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium text-gray-800 hover:bg-gray-100 transition-colors"
        @click="goHost"
      >
        <!-- <BuildingStorefrontIcon class="w-5 h-5" /> -->
        Become a host
      </NuxtLink>

      <!-- Language -->
      <button
        class="flex items-center p-2.5 rounded-full text-gray-800 hover:bg-gray-100 transition-colors"
        aria-label="Select language"
        @click="openLang"
      >
        <!-- <GlobeAltIcon class="w-5 h-5" /> -->
      </button>

      <!-- Burger + avatar -->
      <button
        class="flex items-center gap-2 pl-3 pr-1.5 py-1.5 rounded-full border border-gray-300 hover:shadow-md transition-shadow"
        aria-label="Open menu"
        @click="openMenu"
      >
        <Bars3Icon class="w-5 h-5 text-gray-800" />
        <div class="w-7 h-7 rounded-full bg-gray-500 flex items-center justify-center">
          <!-- <UserIcon class="w-4 h-4 text-white" /> -->
        </div>
      </button>

    </div>
  </nav>
</template>

<script setup>
// import {
//   GlobeAltIcon,
//   Bars3Icon,
//   UserIcon,
//   BuildingStorefrontIcon,
// } from '@heroicons/vue/24/outline'
// import { HomeHeartIcon } from '@heroicons/vue/24/outline' 

const goHost  = () => navigateTo('/auth/login')
const openLang = () => { /* open language modal */ }
const openMenu = () => { /* open dropdown menu */ }
</script>