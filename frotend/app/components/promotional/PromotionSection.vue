<template>
  <section>
    <div class="mx-auto">
      <!-- Heading -->
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold text-gray-900">កម្រងព័ត៌មានថ្មីៗ</h2>

        <NuxtLink
          to="/promotion"
          class="flex items-center gap-1 text-sm font-semibold text-green-700 hover:text-green-800"
        >
          View All
          <ChevronRightIcon class="h-4 w-4" />
        </NuxtLink>
      </div>

      <div class="grid gap-6 lg:grid-cols-12">
        <!-- Banner -->
        <div class="lg:col-span-7">
          <PromotionBanner :promotion="banner" />
        </div>

        <!-- List -->
        <div class="space-y-4 lg:col-span-5">
          <PromotionCard v-for="item in promotions" :key="item.id" :promotion="item" />
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ChevronRightIcon } from "@heroicons/vue/24/outline";

import PromotionBanner from "./PromotionBanner.vue";
import PromotionCard from "./PromotionCard.vue";

const banner = {
  image: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1200",
  title: "Mid Year Sale",
  discount: "20%",
};

const promotions = [
  {
    id: 1,
    image: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=500",
    title: "Modern Apartment",
    location: "Phnom Penh",
    bedroom: 5,
    bathroom: 2,
    floor: 5,
    price: 100,
    date: "2026-06-20",
  },
  {
    id: 2,
    image: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=500",
    title: "Modern Apartment",
    location: "Phnom Penh",
    bedroom: 5,
    bathroom: 2,
    floor: 5,
    price: 100,
    date: "2026-06-20",
  },
  {
    id: 3,
    image: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=500",
    title: "Modern Apartment",
    location: "Phnom Penh",
    bedroom: 5,
    bathroom: 2,
    floor: 5,
    price: 100,
    date: "2026-06-20",
  },
];
</script>
