<template>
  <div class="w-full min-h-screen bg-white">
    <!-- Hero section -->
    <section class="relative w-full h-[300px] overflow-hidden">
      <div class="absolute inset-0 bg-gray-300"></div>
      <div class="relative z-10 flex flex-col items-center justify-center h-full px-4"></div>
    </section>

    <div class="grid p-12 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-5">
      <NuxtLink
        v-for="room in rooms"
        :key="room.id"
        class="cursor-pointer group"
        to="/room-detail"
      >
        <!-- Image -->
        <div class="relative rounded-xl overflow-hidden aspect-[4/3]" :style="{ background: room.color }">
          <div class="w-full h-full flex items-center justify-center">
            <!-- <component :is="BedDoubleIcon" class="w-12 h-12 text-black/20" /> -->
          </div>

          <!-- Guest favourite badge -->
          <div class="absolute top-2.5 left-2.5 bg-white text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
            <!-- <StarIcon class="w-3 h-3 text-red-500" /> -->
            Guest favourite
          </div>

          <!-- Favourite button -->
          <button
            @click.stop="toggleFav(room.id)"
            class="absolute top-2 right-2 w-8 h-8 flex items-center justify-center"
            :aria-label="`Save ${room.title}`"
          >
            <!-- <HeartIcon
              class="w-5 h-5 transition-colors drop-shadow"
              :class="favs.has(room.id) ? 'text-red-500 fill-red-500' : 'text-white'"
            /> -->
          </button>
        </div>

        <!-- Card info — now INSIDE NuxtLink, sibling to the image div -->
        <div class="mt-2.5 px-0.5">
          <p class="text-sm font-medium text-gray-900 truncate">{{ room.title }}</p>
          <p class="text-xs text-gray-500 mt-0.5">{{ room.guests }} guests · 1 bedroom</p>
          <div class="flex items-center justify-between mt-1">
            <p class="text-sm text-gray-900">
              <span class="font-medium">${{ room.price }}</span>
              for {{ room.nights }} nights
            </p>
            <div class="flex items-center gap-1 text-xs text-gray-900">
              <!-- <StarIcon class="w-3 h-3" /> -->
              {{ room.rating }}
            </div>
          </div>
        </div>

      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
// import { HeartIcon, StarIcon, BedDoubleIcon } from '@heroicons/vue/24/outline'

const favs = ref(new Set())
const toggleFav = (id) => {
  const next = new Set(favs.value)
  next.has(id) ? next.delete(id) : next.add(id)
  favs.value = next
}

const types  = ['Apartment', 'Studio', 'Suite', 'Villa', 'Penthouse', 'Condo']
const locs   = ['Phnom Penh', 'Siem Reap', 'Sihanoukville', 'Kampot', 'Kep', 'Battambang']
const colors = ['#DCDCDC']

const rooms = Array.from({ length: 25 }, (_, i) => ({
  id:     i + 1,
  title:  `${types[i % types.length]} in ${locs[i % locs.length]}`,
  guests: Math.floor(1 + (i % 4)),
  price:  25 + ((i * 17) % 175),
  nights: 2 + (i % 3),
  rating: (4.5 + (i % 10) * 0.05).toFixed(2),
  color:  colors[i % colors.length],
}))
</script>