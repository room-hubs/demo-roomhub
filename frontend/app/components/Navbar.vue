<template>
  <header class="sticky top-0 z-50 border-b border-gray-200 bg-white/90 backdrop-blur-md">
    <div
      class="mx-auto flex h-16 max-w-407.5 w-full items-center justify-between px-4 md:px-6 xl:px-8"
    >
      <!-- Logo -->
      <NuxtLink to="/" class="flex shrink-0 items-center gap-2 select-none">
        <span class="text-2xl font-black tracking-tight text-gray-900"> Room </span>
        <span class="text-2xl font-black tracking-tight text-green-600"> HUB </span>
      </NuxtLink>

      <!-- ================= Desktop (>=1280px) ================= -->

      <div class="hidden shrink-0 items-center gap-2 xl:flex xl:gap-4">
        <!-- Host -->
        <NuxtLink
          to="/host"
          class="shrink-0 whitespace-nowrap rounded-full bg-green-700 px-5 py-2 text-sm font-semibold text-white transition-all duration-300 hover:bg-green-800 hover:shadow-lg active:scale-95"
        >
          ដាក់បន្ទប់ជួល
        </NuxtLink>

        <!-- Language -->
        <LanguageDropdown />

        <!-- Login / Profile -->
        <NuxtLink
          :to="user ? '/dashboard' : '/auth/login'"
          class="flex shrink-0 items-center gap-2 whitespace-nowrap rounded-full px-3 py-2 transition hover:bg-gray-100"
        >
          <template v-if="user">
            <img
              :src="user.avatar || '/picture/default-avatar.png'"
              alt="User avatar"
              class="h-8 w-8 rounded-full object-cover"
            />
            <span class="font-medium text-gray-700">{{ user.name || "Account" }}</span>
          </template>

          <template v-else>
            <UserCircleIcon class="h-6 w-6 text-gray-600" />
            <span class="font-medium text-gray-700"> ចូលគណនី </span>
          </template>
        </NuxtLink>
      </div>

      <!-- ================= Tablet (768px - 1280px) ================= -->
      <div class="hidden shrink-0 items-center gap-3 md:flex xl:hidden">
        <NuxtLink
          to="/host"
          class="shrink-0 whitespace-nowrap rounded-full bg-green-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-800"
        >
          ដាក់បន្ទប់ជួល
        </NuxtLink>

        <button
          @click="mobileMenu = true"
          class="shrink-0 rounded-xl p-2 transition hover:bg-gray-100"
        >
          <Bars3Icon class="h-7 w-7 text-gray-700" />
        </button>
      </div>

      <!-- ================= Mobile (<768px) ================= -->

      <button
        @click="mobileMenu = true"
        class="shrink-0 rounded-xl p-2 transition hover:bg-gray-100 md:hidden"
      >
        <Bars3Icon class="h-7 w-7 text-gray-700" />
      </button>
    </div>

    <!-- Mobile Drawer -->
    <MobileMenu :open="mobileMenu" @close="mobileMenu = false" />
  </header>
</template>

<script setup>
import { ref } from "vue";

import { Bars3Icon, UserCircleIcon } from "@heroicons/vue/24/outline";

import LanguageDropdown from "~/components/LanguageDropdown.vue";
import MobileMenu from "~/components/MobileMenu.vue";

const mobileMenu = ref(false);

// auth state (shows avatar when logged in)
const { user } = useAuth();
</script>
