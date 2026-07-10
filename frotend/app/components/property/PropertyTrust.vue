<template>
  <div class="bg-white p-4 lg:p-5">
    <div class="flex items-center gap-4 lg:gap-6 flex-wrap">
      <!-- Guest Favorite -->
      <div class="flex items-center gap-2 shrink-0">
        <LaurelIcon class="w-6 h-6 text-gray-800" />
        <span class="text-sm font-semibold text-gray-900 leading-tight text-center">
          Guest<br />favorite
        </span>
        <LaurelIcon class="w-6 h-6 text-gray-800 -scale-x-100" />
      </div>

      <!-- Description -->
      <p class="text-sm text-gray-800 flex-1 min-w-40">
        {{ description }}
      </p>

      <!-- Divider -->
      <div class="h-9 w-px bg-gray-200 shrink-0"></div>

      <!-- Rating -->
      <div class="text-center shrink-0">
        <p class="text-lg font-bold text-gray-900 leading-tight">{{ ratingDisplay }}</p>
        <div class="flex justify-center text-gray-900 gap-0.5">
          <StarIcon v-for="n in 5" :key="n" class="w-3 h-3" />
        </div>
      </div>

      <!-- Divider -->
      <div class="h-9 w-px bg-gray-200 shrink-0"></div>

      <!-- Reviews -->
      <div class="text-center shrink-0">
        <p class="text-lg font-bold text-gray-900 leading-tight">{{ reviews }}</p>
        <p class="text-xs text-gray-500">Reviews</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, h } from "vue";
import { StarIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
  rating: { type: [Number, String], default: 0 },
  reviews: { type: [Number, String], default: 0 },
  description: {
    type: String,
    default: "One of the most loved homes on Airbnb, according to guests",
  },
});

const ratingDisplay = computed(() => Number(props.rating).toFixed(2));

// Simple laurel branch icon (stem + alternating leaves), since heroicons has no equivalent
const LaurelIcon = (props, { attrs }) =>
  h(
    "svg",
    {
      viewBox: "0 0 24 24",
      fill: "none",
      stroke: "currentColor",
      "stroke-width": "1.5",
      "stroke-linecap": "round",
      ...attrs,
    },
    [
      h("path", { d: "M20 4C16 6 12 10 10 20" }),
      h("ellipse", {
        cx: "16.5",
        cy: "6.5",
        rx: "1.6",
        ry: "0.9",
        transform: "rotate(35 16.5 6.5)",
      }),
      h("ellipse", {
        cx: "14",
        cy: "9.5",
        rx: "1.6",
        ry: "0.9",
        transform: "rotate(35 14 9.5)",
      }),
      h("ellipse", {
        cx: "12",
        cy: "13",
        rx: "1.6",
        ry: "0.9",
        transform: "rotate(35 12 13)",
      }),
      h("ellipse", {
        cx: "10.5",
        cy: "16.5",
        rx: "1.6",
        ry: "0.9",
        transform: "rotate(35 10.5 16.5)",
      }),
    ]
  );
</script>
