<template>
  <div class="bg-white p-5 lg:p-6">
    <!-- Title + Price -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
      <h1 class="text-xl lg:text-2xl font-bold text-gray-900">
        {{ property.name }}
      </h1>

      <div class="text-left sm:text-right shrink-0">
        <div class="text-2xl lg:text-3xl font-bold text-red-600">
          ${{ property.price }}
        </div>
        <div class="text-xs text-gray-500">{{ priceLabel }}</div>
      </div>
    </div>

    <!-- Meta line -->
    <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-500">
      <span class="flex items-center gap-1">
        <MapPinIcon class="w-4 h-4 shrink-0" />
        {{ property.location }}
      </span>
      <span>|</span>
      <span>{{ property.bedrooms }} បន្ទប់គេង</span>
      <span>|</span>
      <span>ជាន់{{ property.floor }}</span>
    </div>
    <p v-if="property.createdAt" class="mt-1 text-xs text-gray-400">
      {{ property.createdAt }}
    </p>

    <!-- Info heading -->
    <h2 class="mt-6 mb-3 text-base font-semibold text-gray-900">ព័ត៌មានទូទៅ</h2>

    <!-- Detail Rows -->
    <div class="divide-y divide-gray-100">
      <!-- Row 1 -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-y-4 gap-x-4 py-4">
        <div>
          <p class="text-xs text-gray-400 mb-1">របៀបលក់ដូរ</p>
          <p class="text-sm text-gray-900">{{ property.dealType }}</p>
        </div>
        <div>
          <p class="text-xs text-gray-400 mb-1">បន្ទប់គេង</p>
          <p class="text-sm text-gray-900">{{ property.bedrooms }}</p>
        </div>
        <div>
          <p class="text-xs text-gray-400 mb-1">បន្ទប់ទឹក</p>
          <p class="text-sm text-gray-900">{{ property.bathrooms }}</p>
        </div>
        <div>
          <p class="text-xs text-gray-400 mb-1">ជាន់</p>
          <p class="text-sm text-gray-900">{{ property.floor }}</p>
        </div>
      </div>

      <!-- Row 2 -->
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-4 gap-x-4 py-4">
        <div>
          <p class="text-xs text-gray-400 mb-1">ទំហំបន្ទប់</p>
          <p class="text-sm text-gray-900">{{ property.size }}</p>
        </div>
        <div>
          <p class="text-xs text-gray-400 mb-1">ស្ថានភាពបន្ទប់</p>
          <p class="text-sm text-gray-900">{{ property.condition }}</p>
        </div>
        <div>
          <p class="text-xs text-gray-400 mb-1">ពេលវេលាស្នាក់នៅបាន</p>
          <p class="text-sm text-gray-900">{{ property.availableFrom }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { MapPinIcon } from "@heroicons/vue/24/outline";

defineProps({
  property: {
    type: Object,
    required: true,
  },
  priceLabel: {
    type: String,
    default: "per night",
  },
});
</script>
