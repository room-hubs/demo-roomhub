<template>
  <div class="flex min-h-screen flex-col bg-gray-50">
    <!-- Navbar -->
    <Navbar />

    <!-- Main Content -->
    <main class="flex-1">
      <div class="mx-auto w-full">
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
// Nuxt auto-imports Navbar and Footer components
</script>