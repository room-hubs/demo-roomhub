<script setup lang="ts">
interface Props {
  currentPage?: number
  totalPages?: number
}

const props = withDefaults(defineProps<Props>(), {
  currentPage: 1,
  totalPages: 15,
})

const emit = defineEmits<{
  (e: 'update:page', page: number): void
}>()

const pages = computed(() => {
  const current = props.currentPage
  const total = props.totalPages

  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }

  if (current <= 3) {
    return [1, 2, 3, 4, "...", total]
  }

  if (current >= total - 2) {
    return [1, "...", total - 3, total - 2, total - 1, total]
  }

  return [
    1,
    "...",
    current - 1,
    current,
    current + 1,
    "...",
    total,
  ]
})

function go(page: number) {
  if (page < 1 || page > props.totalPages) return
  emit("update:page", page)
}
</script>

<template>
  <nav
    class="flex items-center justify-center gap-2 py-6"
    aria-label="Pagination"
  >
    <!-- Previous -->
    <button
      class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 transition hover:border-green-600 hover:text-green-600 disabled:cursor-not-allowed disabled:opacity-40"
      :disabled="currentPage === 1"
      @click="go(currentPage - 1)"
    >
      <UIcon
        name="i-lucide-chevron-left"
        class="h-4 w-4"
      />
    </button>

    <!-- Pages -->
    <template
      v-for="(page, index) in pages"
      :key="index"
    >
      <span
        v-if="page === '...'"
        class="px-2 text-gray-400"
      >
        ...
      </span>

      <button
        v-else
        @click="go(Number(page))"
        class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-medium transition"
        :class="
          page === currentPage
            ? 'bg-green-600 text-white shadow-md'
            : 'text-gray-700 hover:bg-green-50 hover:text-green-600'
        "
      >
        {{ page }}
      </button>
    </template>

    <!-- Next -->
    <button
      class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 transition hover:border-green-600 hover:text-green-600 disabled:cursor-not-allowed disabled:opacity-40"
      :disabled="currentPage === totalPages"
      @click="go(currentPage + 1)"
    >
      <UIcon
        name="i-lucide-chevron-right"
        class="h-4 w-4"
      />
    </button>
  </nav>
</template>