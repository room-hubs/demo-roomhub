<script setup>
import { ref } from "vue";

import { handpickedRooms } from "~/data/rooms";
import RoomPagination from "~/components/room/RoomPagination.vue";
import PromotionSidebar from "~/components/promotional/PromotionSidebar.vue";

const favs = ref(new Set());

const toggleFav = (id) => {
  if (favs.value.has(id)) {
    favs.value.delete(id);
  } else {
    favs.value.add(id);
  }
};
</script>
<template>
  <main class="min-h-screen bg-gray-50">
    <div class="mx-auto max-w-360 px-4 py-8">
      <!-- Search -->
      <SearchMainSearch class="mb-10" />
      <!-- Header -->
      <section class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Available Rooms</h1>

          <p class="mt-1 text-gray-500">
            Showing
            <span class="font-semibold text-green-600">
              {{ handpickedRooms.length }}
            </span>
            rooms
          </p>
        </div>

        <button
          class="hidden rounded-xl border border-gray-200 px-4 py-2 text-sm hover:border-green-500 hover:text-green-600 md:block"
        >
          Sort by ▼
        </button>
      </section>

      <!-- Content -->
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Rooms -->
        <section class="lg:col-span-9">
          <div
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 min-[1800px]:grid-cols-4"
          >
            <RoomCard
              v-for="room in handpickedRooms"
              :key="room.id"
              :room="room"
              :isFavorite="favs.has(room.id)"
              @toggle-fav="toggleFav"
            />
          </div>
        </section>

        <!-- Sidebar -->
        <aside class="hidden lg:block lg:col-span-3">
          <div class="sticky top-24">
            <PromotionSidebar />
          </div>
        </aside>
      </div>

      <!-- Pagination -->
      <div class="mt-14 flex justify-center">
        <RoomPagination />
      </div>
    </div>
  </main>
</template>
