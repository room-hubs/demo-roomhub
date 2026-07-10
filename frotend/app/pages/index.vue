<template>
  <div class="bg-gray-50 min-h-screen">
    <HeroSection />
    <main
      class="mx-auto w-full max-w-5xl lg:max-w-7xl xl:max-w-350 2xl:max-w-400 px-4 sm:px-6 lg:px-8 py-10 sm:py-12 lg:py-16 space-y-12 sm:space-y-16 lg:space-y-20"
    >
      <!-- Promotion -->
      <section>
        <PromotionSection />
      </section>

      <!-- Recently Added -->
      <section class="w-full">
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-2xl font-bold">ចូលមើលថ្មីៗនេះ</h2>
          <RouterLink to="/listing" class="text-sm font-medium hover:underline">
            View All
          </RouterLink>
        </div>

        <div
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-5"
        >
          <RoomCard
            v-for="room in recentRooms"
            :key="room.id"
            :room="room"
            :isFavorite="favs.has(room.id)"
            @toggle-fav="toggleFav"
          />
          <PhotoCollageCard :images="collageImages" link="/rooms/all" />
        </div>
      </section>

      <!-- New Listings -->
      <section>
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-2xl font-bold">ការផុសថ្មីៗ</h2>
          <RouterLink to="/listing" class="text-sm font-medium hover:underline">
            View All
          </RouterLink>
        </div>

        <div
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-5"
        >
          <RoomCard
            v-for="room in newRooms"
            :key="room.id"
            :room="room"
            :isFavorite="favs.has(room.id)"
            @toggle-fav="toggleFav"
          />
          <PhotoCollageCard :images="collageImages" link="/rooms/all" />
        </div>
      </section>

      <!-- Banner app -->
      <section>
        <div class="h-auto">
          <img class="w-full" src="/picture/105000001.png" alt="" />
        </div>
        <!-- banner design -->
      </section>

      <!-- Popular Rooms -->
      <section>
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-2xl font-bold">បន្ទប់ស្នាក់នៅ ពេញនិយម</h2>
          <RouterLink to="/rooms/all" class="text-sm font-medium hover:underline">
            View All
          </RouterLink>
        </div>
        <div
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-5"
        >
          <RoomCard
            v-for="room in popularRooms"
            :key="room.id"
            :room="room"
            :isFavorite="favs.has(room.id)"
            @toggle-fav="toggleFav"
          />
          <PhotoCollageCard :images="collageImages" link="/rooms/all" />
        </div>
      </section>

      <!-- Handpicked for You -->
      <section>
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-2xl font-bold">ជម្រើសពិសេសសម្រាប់អ្នក</h2>
          <RouterLink to="/rooms/all" class="text-sm font-medium hover:underline">
            View All
          </RouterLink>
        </div>
        <div
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-5"
        >
          <RoomCard
            v-for="room in handpickedRooms"
            :key="room.id"
            :room="room"
            :isFavorite="favs.has(room.id)"
            @toggle-fav="toggleFav"
          />
          <PhotoCollageCard :images="collageImages" link="/rooms/all" />
        </div>
      </section>

      <!-- Popular Destinations -->
      <section>
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-2xl font-bold">ទីតាំងដែលពេញនិយម</h2>
          <RouterLink to="/rooms/all" class="text-sm font-medium hover:underline">
            View All
          </RouterLink>
        </div>
        <div
          class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 2xl:grid-cols-9 gap-4"
        >
          <DestinationCard
            v-for="item in destinations"
            :key="item.id"
            :title="item.title"
            :count="item.count"
            :image="item.image"
            :link="item.link"
          />
        </div>
      </section>
      <!-- Banner app -->
      <section>
        <div class="h-auto">
          <img class="w-full" src="/picture/15600001.jpg" alt="" />
        </div>
        <!-- banner design -->
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue";
import HeroSection from "~/components/hero/HeroSection.vue";
import PhotoCollageCard from "~/components/room/PhotoCollageCard.vue";
import DestinationCard from "~/components/room/DestinationCard.vue";
import PromotionSection from "~/components/promotional/PromotionSection.vue";

const favs = ref(new Set());
const toggleFav = (id) => {
  const next = new Set(favs.value);
  next.has(id) ? next.delete(id) : next.add(id);
  favs.value = next;
};

// Shared collage images (reused across all sections)
const collageImages = [
  "https://a0.muscache.com/im/pictures/BnbProperty/BnbProperty-1690121183119362156/original/52f7c15f-4f38-416d-be82-66b803954b2b.jpeg?im_w=1200",
  "https://a0.muscache.com/im/pictures/hosting/Hosting-1488739280980937482/original/aaf2bc5a-03f7-4c95-8cfd-f3bb43324fc6.jpeg?im_w=1200",
  "https://a0.muscache.com/im/pictures/hosting/Hosting-1488739280980937482/original/e1380406-2b0f-46c1-a458-b9cea060bffa.jpeg?im_w=720",
];

// Helper to build mock room data consistently
function makeRooms(count, offset = 0) {
  return Array.from({ length: count }, (_, i) => ({
    id: offset + i + 1,
    title: `បន្ទប់ជួលនៅ បុរី១០០ខ្នង`,
    location: `ទឹកថ្លា,សែនសុខ,ភ្នំពេញ`,
    bedrooms: 5,
    floor: `1,2,5`,
    date: `2026-06-20`,
    time: `16:19`,
    price: 90 + (offset + i) * 10,
    image:
      "https://a0.muscache.com/im/pictures/hosting/Hosting-1239558906468787551/original/e5d41d10-9bd0-4ccd-a164-cf5e38f0337c.jpeg",
  }));
}

// Distinct data per section (was previously all sharing one `rooms` array)
const newRooms = makeRooms(9, 0);
const popularRooms = makeRooms(9, 100);
const handpickedRooms = makeRooms(9, 200);

const recentRooms = Array.from({ length: 4 }, (_, i) => ({
  id: 300 + i + 1,
  title: "Modern City Guesthouse",
  location: "Phnom Penh",
  price: 35,
  rating: 4.8,
  reviews: 24,
  guests: 2,
  beds: 1,
  baths: 1,
  bedrooms: 1,
  floor: "1",
  date: "2026-07-01",
  time: "09:00",
  image: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1200",
}));

// Previously missing — this was undefined and would have crashed the page
const destinations = Array.from({ length: 8 }, (_, i) => ({
  id: i + 1,
  title: `ចំនួនទីតាំងជាកន្លែងជួល`,
  count: 1908,
  image: "https://images.unsplash.com/photo-1503917988258-f87a78e3c995?w=600",
  link: `/rooms?location=${i + 1}`,
}));
</script>
