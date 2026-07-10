<template>
  <div class="bg-white p-5 lg:p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">សេវាកម្មនិងបរិក្ខា</h2>

    <div class="space-y-3">
      <div
        v-for="(row, i) in rows"
        :key="i"
        class="flex flex-wrap items-center divide-x divide-gray-200 text-sm text-gray-700"
      >
        <div
          v-for="(item, j) in row"
          :key="j"
          class="flex items-center gap-2 px-4 first:pl-0"
        >
          <component :is="item.icon" class="w-4 h-4 text-gray-700 shrink-0" />
          <span>{{ item.label }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { TruckIcon, TvIcon, VideoCameraIcon, WifiIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
  amenities: {
    type: Array,
    default: () => [
      { label: "ចំណតឡាន", icon: TruckIcon },
      { label: "ភ្ជាប់ទឹកនេត", icon: TvIcon },
      { label: "កាមេរ៉ាសុវត្ថិភាព", icon: VideoCameraIcon },
      { label: "wifi", icon: WifiIcon },
      { label: "ចំណតឡាន", icon: TruckIcon },
      { label: "ភ្ជាប់ទឹកនេត", icon: TvIcon },
      { label: "កាមេរ៉ាសុវត្ថិភាព", icon: VideoCameraIcon },
      { label: "wifi", icon: WifiIcon },
    ],
  },
  perRow: {
    type: Number,
    default: 4,
  },
});

// Split the flat amenities array into chunks of `perRow` for the divider rows
const rows = computed(() => {
  const chunks = [];
  for (let i = 0; i < props.amenities.length; i += props.perRow) {
    chunks.push(props.amenities.slice(i, i + props.perRow));
  }
  return chunks;
});
</script>
