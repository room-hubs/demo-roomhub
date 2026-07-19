<template>
  <div class="bg-white p-5 lg:p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">ទីតាំងជិតៗ</h2>

    <ul class="space-y-2">
      <li
        v-for="(place, i) in places"
        :key="i"
        class="flex items-center justify-between border-l-2 border-gray-300 pl-3 text-sm text-gray-700"
      >
        <span>{{ place.name }}</span>
        <span class="text-gray-700">{{ place.distance }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({
  places: {
    type: Array,
    default: () => [
      { name: "ABA ភ្នាះ", distance: "100m" },
      { name: "សាលារៀនNTTI", distance: "500m" },
      { name: "វត្តអារាម", distance: "900m" },
      { name: "ផ្សារទំនើប", distance: "1km" },
    ],
  },
});
</script>
