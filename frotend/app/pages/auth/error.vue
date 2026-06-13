<template>
  <div class="flex items-center justify-center min-h-screen">
    <div class="text-center">
      <h1 class="text-2xl font-bold text-red-500">Login Failed</h1>
      <p class="text-gray-600 mt-2">{{ message }}</p>
      <NuxtLink to="/login" class="mt-4 inline-block text-indigo-600">
        Try Again
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
const route   = useRoute()
const message = route.query.message ?? 'Something went wrong.'
</script>