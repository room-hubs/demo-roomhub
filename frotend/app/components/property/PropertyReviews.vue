<template>
  <div class="bg-white p-5 lg:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-bold text-gray-900">ការវាយតម្លៃការជួល</h2>

      <NuxtLink
        to="#"
        class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 underline shrink-0"
      >
        <span>មើលទាំងអស់</span>
        <ChevronRightIcon class="w-4 h-4" />
      </NuxtLink>
    </div>

    <!-- Reviews -->
    <div class="divide-y divide-gray-100">
      <div
        v-for="(review, i) in visibleReviews"
        :key="i"
        class="py-4 first:pt-0 last:pb-0"
      >
        <div class="flex items-start gap-3">
          <!-- Avatar -->
          <div class="w-9 h-9 rounded-full bg-gray-300 overflow-hidden shrink-0">
            <img
              v-if="review.avatar"
              :src="review.avatar"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900">
              {{ review.name }}
            </p>

            <div class="flex text-amber-500 mt-0.5">
              <StarIcon
                v-for="n in 5"
                :key="n"
                class="w-3.5 h-3.5"
                :class="
                  n <= review.rating ? 'fill-amber-500' : 'fill-gray-200 text-gray-200'
                "
              />
            </div>

            <p class="text-sm text-gray-700 mt-1.5 truncate">
              {{ review.comment }}
            </p>

            <p class="text-xs text-gray-400 mt-1">
              {{ review.date }}
            </p>
          </div>

          <!-- Reply toggle -->
          <button
            type="button"
            class="text-sm text-gray-500 hover:text-gray-700 shrink-0 whitespace-nowrap"
            @click="toggleReply(i)"
          >
            {{ openReplyIndex === i ? "បិទ" : "ឆ្លើយតប" }}
          </button>
        </div>

        <!-- Reply Form -->
        <div v-if="openReplyIndex === i" class="mt-3 ml-12">
          <textarea
            v-model="replyText"
            rows="3"
            placeholder="សរសេរការឆ្លើយតបរបស់អ្នក..."
            class="w-full text-sm border border-gray-200 rounded-xl p-3 resize-none focus:outline-none focus:ring-2 focus:ring-gray-300"
          ></textarea>

          <div class="flex items-center gap-2 mt-2">
            <button
              type="button"
              class="bg-gray-900 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-xl transition disabled:opacity-40 disabled:cursor-not-allowed"
              :disabled="!replyText.trim()"
              @click="submitReply(review, i)"
            >
              ផ្ញើ
            </button>

            <button
              type="button"
              class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-xl transition"
              @click="cancelReply"
            >
              បោះបង់
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Show all / collapse -->
    <div v-if="showToggleButton" class="mt-4 text-center">
      <button
        type="button"
        class="text-sm font-medium text-gray-900 border border-gray-200 rounded-xl px-4 py-2 hover:bg-gray-50 transition"
        @click="expanded = !expanded"
      >
        {{ expanded ? "បង្រួម" : `មើលមតិទាំងអស់ (${props.reviews.length})` }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { ChevronRightIcon } from "@heroicons/vue/24/outline";
import { StarIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
  reviews: {
    type: Array,
    default: () => [
      {
        name: "SORN SOY",
        rating: 4,
        avatar: "",
        comment: "បន្ទប់ស្អាតមានអនាម័យល........",
        date: "2026-04-04 13:44:09",
      },
      {
        name: "SORN SOY",
        rating: 4,
        avatar: "",
        comment: "បន្ទប់ស្អាតមានអនាម័យល........",
        date: "2026-04-04 13:44:09",
      },
      {
        name: "DARA PICH",
        rating: 5,
        avatar: "",
        comment: "ជម្រើសល្អសម្រាប់ស្នាក់នៅ........",
        date: "2026-04-02 10:12:00",
      },
      {
        name: "CHENDA LY",
        rating: 3,
        avatar: "",
        comment: "សេវាកម្មល្អប៉ុន្តែថ្លៃ........",
        date: "2026-03-28 09:05:00",
      },
      {
        name: "SORN SOY",
        rating: 4,
        avatar: "",
        comment: "សេវាកម្មល្អប៉ុន្តែថ្លៃ........",
        date: "2026-03-28 09:05:00",
      },
    ],
  },
  limit: {
    type: Number,
    default: 3,
  },
});

const emit = defineEmits(["reply"]);

/* State */
const expanded = ref(false);
const openReplyIndex = ref(null);
const replyText = ref("");

/* Computed */
const showToggleButton = computed(() => props.reviews.length > props.limit);

const visibleReviews = computed(() =>
  expanded.value ? props.reviews : props.reviews.slice(0, props.limit)
);

/* Reply logic */
function toggleReply(index) {
  if (openReplyIndex.value === index) {
    cancelReply();
  } else {
    openReplyIndex.value = index;
    replyText.value = "";
  }
}

function cancelReply() {
  openReplyIndex.value = null;
  replyText.value = "";
}

function submitReply(review, index) {
  emit("reply", {
    review,
    index,
    text: replyText.value.trim(),
  });

  cancelReply();
}
</script>
