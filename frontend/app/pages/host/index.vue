<template>
  <div class="min-h-screen flex items-center justify-center bg-white px-4">
    <div class="w-full max-w-2xl">
      <!-- Welcome Card -->
      <div
        class="rounded-3xl bg-green-600 px-6 py-5 sm:px-8 sm:py-8 animate-fade-in-up"
        style="animation-delay: 0ms"
      >
        <h1 class="text-2xl font-semibold text-white sm:text-4xl">Welcome back, Dam</h1>
      </div>

      <!-- Subtitle -->
      <div class="mt-8 animate-fade-in-up" style="animation-delay: 120ms">
        <h2 class="text-lg md:text-xl font-medium text-gray-700">Start a new listing</h2>
      </div>

      <!-- Create Listing -->
      <div class="mt-8 animate-fade-in-up" style="animation-delay: 240ms">
        <NuxtLink
          to="/host/address"
          class="flex items-center gap-4 p-3 border-b border-gray-200 hover:bg-gray-50 transition rounded-lg"
        >
          <div
            class="w-14 h-14 md:w-16 md:h-16 rounded-2xl border border-gray-200 flex items-center justify-center"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-7 h-7 text-green-500"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"
              />
            </svg>
          </div>
          <h3 class="text-lg md:text-xl font-medium text-gray-800">Create new listing</h3>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "host",
});
</script>

<style scoped>
.animate-fade-in-up {
  opacity: 0;
  animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(16px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
