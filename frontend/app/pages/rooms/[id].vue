<template>
  <div class="bg-gray-50 min-h-screen mt-10">
    <PropertyGallery :images="property.images" />
    <!-- Content -->
    <div class="max-w-360 w-full h-auto mx-auto px-4 lg:px-6 mt-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-6">
          <PropertyTrust :rating="property.rating" :reviews="property.reviews" />
          <PropertyInfoCard :property="property" />
          <PropertyDetails :details="details" />
          <PropertyAmenities :amenities="amenities" />
          <PropertyNearby :amenities="amenities" />
          <PropertyReviews :reviews="reviews" />
          <div class="bg-white lg:p-1">
            <!-- Header -->
            <div class="bg-white lg:p-6">
              <!-- Header -->
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">ណែនាំសម្រាប់អ្នក</h2>
              </div>
              <!-- Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-5">
                <RoomCard
                  v-for="room in handpickedRooms"
                  :key="room.id"
                  :room="room"
                  :isFavorite="favs.has(room.id)"
                  @toggle-fav="toggleFav"
                />
                <PhotoCollageCard :images="collageImages" link="/rooms/all" />
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Contact / Host card -->
        <div class="lg:col-span-1 space-y-6">
          <PropertyContact
            name="Sok Sam"
            phone="010 577 309"
            whatsapp="85510577309"
            avatar="https://i.pravatar.cc/100"
          />
          <PropertyMap
            :lat="13.3671"
            :lng="103.8448"
            address="Siem Reap, Cambodia"
            :zoom="15"
          />
        </div>
      </div>
      <!-- Banner app -->
      <section>
        <div class="h-auto mt-5">
          <img class="w-full" src="/picture/15600001.jpg" alt="" />
        </div>
        <!-- banner design -->
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import PropertyContact from "~/components/property/PropertyContact.vue";
import PropertyDetails from "~/components/property/PropertyDetails.vue";
import PropertyInfoCard from "~/components/property/PropertyInfoCard.vue";
import PropertyMap from "~/components/property/PropertyMap.vue";
import PropertyTrust from "~/components/property/PropertyTrust.vue";
import PropertyAmenities from "~/components/property/PropertyAmenities.vue";
import PropertyNearby from "~/components/property/PropertyNearby.vue";
import PropertyReviews from "~/components/property/PropertyReviews.vue";
import PropertyGallery from "~/components/room/PropertyGallery.vue";
import RoomCard from "~/components/room/RoomCard.vue";
import PhotoCollageCard from "~/components/room/PhotoCollageCard.vue";

const property = {
  name: "Angkor Paradise Guesthouse",
  rating: 4.8,
  reviews: 124,
  location: "Siem Reap, Cambodia",
  price: 35,
  createdAt: "2026-06-20 16:19",

  dealType: "ជួលទាំងខ្នង",
  bedrooms: 1,
  bathrooms: 1,
  floor: "1,2,5",
  size: "5m x 5m",
  condition: "បន្ទប់ថ្មី",
  availableFrom: "2016-08-08",

  description:
    "Comfortable guesthouse located near major attractions with modern facilities and excellent service.",

  amenities: [
    "Free WiFi",
    "Air Conditioning",
    "Parking",
    "Restaurant",
    "Swimming Pool",
    "24/7 Reception",
  ],

  images: [
    "https://a0.muscache.com/im/pictures/miso/Hosting-913496747956034460/original/31ae8220-245e-45d0-a292-b3a2c0d12f21.jpeg?im_w=1200",
    "https://a0.muscache.com/im/pictures/miso/Hosting-913496747956034460/original/638f44ce-0085-4a1a-9383-4a2bc46eec2c.jpeg?im_w=720",
    "https://a0.muscache.com/im/pictures/miso/Hosting-913496747956034460/original/c179fae6-7a08-48da-be1d-37a377fac080.jpeg?im_w=720",
    "https://a0.muscache.com/im/pictures/miso/Hosting-913496747956034460/original/cf4299d3-ba37-47c4-8363-0973472f3eba.jpeg?im_w=720",
    "https://a0.muscache.com/im/pictures/miso/Hosting-913496747956034460/original/6e479d5a-2e57-4db3-894b-300b1198cbd2.jpeg?im_w=720",
  ],
};

// PropertyDetails data
const details = [
  { label: "តម្លៃ", value: "$35 ក្នុងមួយថ្ងៃ" },
  { label: "ប្រភេទ", value: "១ បន្ទប់គេង, ១ បន្ទប់ទឹក" },
  { label: "ជាន់", value: "1,2,5" },
  { label: "ទំហំ", value: "5m x 5m" },
];

// PropertyAmenities / PropertyNearby data
const amenities = [
  { label: "ចំណតឡាន" },
  { label: "ភ្ជាប់ទឹកនេត" },
  { label: "កាមេរ៉ាសុវត្ថិភាព" },
  { label: "wifi" },
];

// PropertyReviews data
const reviews = [
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
];

// Shared collage images
const collageImages = [
  "https://a0.muscache.com/im/pictures/BnbProperty/BnbProperty-1690121183119362156/original/52f7c15f-4f38-416d-be82-66b803954b2b.jpeg?im_w=1200",
  "https://a0.muscache.com/im/pictures/hosting/Hosting-1488739280980937482/original/aaf2bc5a-03f7-4c95-8cfd-f3bb43324fc6.jpeg?im_w=1200",
  "https://a0.muscache.com/im/pictures/hosting/Hosting-1488739280980937482/original/e1380406-2b0f-46c1-a458-b9cea060bffa.jpeg?im_w=720",
];

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

// The actual list rendered by RoomCard's v-for
const handpickedRooms = makeRooms(4);

// Favorites state for RoomCard
const favs = ref(new Set());

function toggleFav(roomId) {
  if (favs.value.has(roomId)) {
    favs.value.delete(roomId);
  } else {
    favs.value.add(roomId);
  }
  // force reactivity since Set mutation isn't tracked automatically
  favs.value = new Set(favs.value);
}
</script>
