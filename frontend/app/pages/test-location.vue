<template>
  <ClientOnly>
    <div class="p-2">
      <h2 class="font-bold mb-2">RoomHub Real Marketplace Map</h2>

      <div ref="mapRef" style="height: 600px; width: 100%"></div>
    </div>
  </ClientOnly>
</template>

<script setup>
import { ref, onMounted } from "vue";

const mapRef = ref(null);

/* =========================
   50 REALISTIC ROOM DATA
========================= */
const rooms = [
  { id: 1, name: "BKK1 Studio A", lat: 11.5566, lng: 104.9282 },
  { id: 2, name: "BKK1 Studio B", lat: 11.5578, lng: 104.9295 },
  { id: 3, name: "Toul Kork Apartment", lat: 11.5698, lng: 104.8932 },
  { id: 4, name: "BKK2 Room", lat: 11.5532, lng: 104.9311 },
  { id: 5, name: "Daun Penh Room", lat: 11.5739, lng: 104.9226 },
  { id: 6, name: "Russian Market Room", lat: 11.5452, lng: 104.9176 },
  { id: 7, name: "Sen Sok Condo", lat: 11.6153, lng: 104.8864 },
  { id: 8, name: "Chbar Ampov House", lat: 11.5123, lng: 104.9452 },
  { id: 9, name: "Mean Chey Flat", lat: 11.5342, lng: 104.9003 },
  { id: 10, name: "Olympic Room", lat: 11.5621, lng: 104.9132 },

  { id: 11, name: "BKK1 Luxury", lat: 11.5588, lng: 104.9271 },
  { id: 12, name: "TK Studio 2", lat: 11.5682, lng: 104.8955 },
  { id: 13, name: "Sen Sok Budget Room", lat: 11.6181, lng: 104.8842 },
  { id: 14, name: "Royal University Area", lat: 11.5629, lng: 104.9156 },
  { id: 15, name: "Olympic Area 2", lat: 11.561, lng: 104.9102 },

  { id: 16, name: "Siem Reap Center", lat: 13.3613, lng: 103.8608 },
  { id: 17, name: "Pub Street Room", lat: 13.3532, lng: 103.8555 },
  { id: 18, name: "Angkor Apartment", lat: 13.3678, lng: 103.8592 },
  { id: 19, name: "Siem Reap Budget", lat: 13.355, lng: 103.8615 },
  { id: 20, name: "SR Riverside", lat: 13.3601, lng: 103.8682 },

  { id: 21, name: "Sihanouk Beach Room", lat: 10.6111, lng: 103.5222 },
  { id: 22, name: "Otres Villa", lat: 10.5792, lng: 103.5433 },
  { id: 23, name: "Sihanouk Condo", lat: 10.6254, lng: 103.5201 },
  { id: 24, name: "Beachfront Room", lat: 10.6088, lng: 103.5189 },
  { id: 25, name: "Beach Hostel", lat: 10.6022, lng: 103.5302 },

  { id: 26, name: "BKK Mini Room", lat: 11.557, lng: 104.929 },
  { id: 27, name: "TK Budget Room", lat: 11.569, lng: 104.892 },
  { id: 28, name: "Olympic Studio", lat: 11.5625, lng: 104.912 },
  { id: 29, name: "Mean Chey 2", lat: 11.5355, lng: 104.901 },
  { id: 30, name: "Daun Penh 2", lat: 11.5745, lng: 104.9235 },

  { id: 31, name: "SR Hostel 2", lat: 13.3566, lng: 103.8566 },
  { id: 32, name: "Angkor Stay", lat: 13.369, lng: 103.858 },
  { id: 33, name: "SR Apartment", lat: 13.362, lng: 103.8625 },
  { id: 34, name: "Pub Street Stay", lat: 13.352, lng: 103.854 },
  { id: 35, name: "SR River Room", lat: 13.3618, lng: 103.8675 },

  { id: 36, name: "Beach Room 2", lat: 10.61, lng: 103.521 },
  { id: 37, name: "Otres Budget", lat: 10.5805, lng: 103.542 },
  { id: 38, name: "Sihanouk Hostel", lat: 10.6035, lng: 103.529 },
  { id: 39, name: "Sea View Room", lat: 10.607, lng: 103.5175 },
  { id: 40, name: "Beach Apartment", lat: 10.6125, lng: 103.5235 },

  { id: 41, name: "TK Apartment", lat: 11.5712, lng: 104.8891 },
  { id: 42, name: "Sen Sok Condo 2", lat: 11.6162, lng: 104.8851 },
  { id: 43, name: "Chbar Ampov 2", lat: 11.5134, lng: 104.9462 },
  { id: 44, name: "Russian Market 2", lat: 11.5466, lng: 104.9188 },
  { id: 45, name: "BKK Studio 4", lat: 11.5561, lng: 104.9289 },

  { id: 46, name: "TK Studio 3", lat: 11.5702, lng: 104.8912 },
  { id: 47, name: "Sen Sok Room", lat: 11.6189, lng: 104.883 },
  { id: 48, name: "Olympic Flat", lat: 11.563, lng: 104.911 },
  { id: 49, name: "Mean Chey Studio", lat: 11.536, lng: 104.902 },
  { id: 50, name: "Daun Penh Studio", lat: 11.575, lng: 104.924 },
];

onMounted(async () => {
  const L = await import("leaflet");
  await import("leaflet/dist/leaflet.css");

  /* =========================
     CREATE MAP
  ========================= */
  const map = L.map(mapRef.value, {
    zoomControl: true,
  });

  /* BASE MAP */
  L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap",
  }).addTo(map);

  /* =========================
     MARKERS
  ========================= */
  const markers = [];

  rooms.forEach((room) => {
    const marker = L.marker([room.lat, room.lng])
    .addTo(map).bindPopup(`
        <div style="min-width:160px">
          <b>${room.name}</b><br/>
          <small>Room ID: ${room.id}</small>
        </div>
      `);

    markers.push(marker);
  });

  /* =========================
     AUTO FIT ALL ROOMS
  ========================= */
  const group = L.featureGroup(markers);
  map.fitBounds(group.getBounds(), {
    padding: [50, 50],
  });
});
</script>
