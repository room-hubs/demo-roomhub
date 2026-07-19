<template>
  <div class="bg-white p-5 lg:p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">ទីតាំង</h2>

    <div class="relative rounded-xl overflow-hidden" :style="{ height: `${height}px` }">
      <iframe
        v-if="mapSrc"
        :src="mapSrc"
        class="w-full h-full border-0"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
      ></iframe>

      <div
        v-else
        class="w-full h-full flex items-center justify-center bg-gray-100 text-sm text-gray-400"
      >
        Map preview unavailable
      </div>

      <!-- Custom center marker -->
      <div
        v-if="mapSrc"
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none"
      >
        <div
          class="w-11 h-11 rounded-full bg-gray-900 flex items-center justify-center shadow-lg ring-4 ring-white/70"
        >
          <HomeIcon class="w-5 h-5 text-white" />
        </div>
      </div>
    </div>

    <p v-if="address" class="mt-3 text-sm text-gray-500 flex items-center gap-1">
      <MapPinIcon class="w-4 h-4 shrink-0" />
      {{ address }}
    </p>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { HomeIcon, MapPinIcon } from "@heroicons/vue/24/solid";
import { MapPinIcon as MapPinOutline } from "@heroicons/vue/24/outline";

const props = defineProps({
  lat: { type: [Number, String], default: null },
  lng: { type: [Number, String], default: null },
  address: { type: String, default: "" },
  zoom: { type: Number, default: 15 },
  height: { type: Number, default: 360 },
});

// Google Maps embed without requiring an API key (output=embed).
// Falls back to searching by address if lat/lng aren't provided.
const mapSrc = computed(() => {
  const query = props.lat && props.lng ? `${props.lat},${props.lng}` : props.address;

  if (!query) return "";

  return `https://maps.google.com/maps?q=${encodeURIComponent(query)}&z=${
    props.zoom
  }&output=embed`;
});
</script>
