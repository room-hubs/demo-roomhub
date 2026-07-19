<template>
  <div class="bg-white p-5 lg:p-6">
    <!-- Details -->
    <h2 class="text-lg font-bold text-gray-900 mb-4">ព័ត៌មានលម្អិត</h2>

    <div
      class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-8 text-sm text-gray-700 mb-8"
    >
      <p v-for="(item, i) in details" :key="i">
        <span class="text-gray-500">{{ item.label }}:</span>
        {{ item.value }}
      </p>
    </div>

    <!-- Rental Terms -->
    <h2 class="text-lg font-bold text-gray-900 mb-4">លក្ខខណ្ឌក្នុងការជួល</h2>

    <ul class="space-y-2 text-sm text-gray-700">
      <li v-for="(rule, i) in rules" :key="i" class="flex items-start gap-2">
        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mt-1.5 shrink-0"></span>
        <span>{{ rule }}</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({
  details: {
    type: Array,
    default: () => [
      { label: "តម្លៃ", value: "$90 ក្នុងមួយថ្ងៃ" },
      { label: "ប្រភេទ", value: "១ បន្ទប់គេង, ១ បន្ទប់ទឹក, ជាន់" },
      { label: "ជាន់", value: "ទី 5" },
      { label: "ទំហំ", value: "5m x 5m" },
      { label: "គ្រឿងសង្ហារឹមពេញលេញ", value: "" },
      { label: "ថ្ងៃត្រូវគ្រងតគគិតថ្លៃ", value: "" },
    ],
  },
  rules: {
    type: Array,
    default: () => ["បិទទ្វារមុនចេញ", "ហាមប្រើប្រាស់ស្រៀងញៀន", "ហាមធីកសំឡៅឡានខានអកជទៃ"],
  },
});
</script>
