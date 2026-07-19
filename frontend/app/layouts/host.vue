<template>
  <div class="flex min-h-screen flex-col bg-gray-50">
    <!-- Logo -->
    <NuxtLink to="/" class="absolute top-6 left-6">
      <img
        src="/picture/logo.jpg"
        alt="Logo"
        class="h-16 w-auto object-contain sm:h-20 md:h-24"
      />
    </NuxtLink>

    <!-- Main Content -->
    <main class="flex flex-1 items-center justify-center px-4">
      <div class="w-full">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup></script>
