<script setup>
definePageMeta({ layout: false });

const { user, logout } = useAuth();

const sidebarOpen = ref(true);

const navItems = computed(() => {
  const isLandlord = user.value?.role === "landlord";
  return [
    { label: "Dashboard", icon: "i-lucide-layout-dashboard", to: "/dashboard" },
    {
      label: isLandlord ? "My Listings" : "Browse Properties",
      icon: "i-lucide-building-2",
      to: isLandlord ? "/dashboard/listings" : "/dashboard/browse",
    },
    {
      label: isLandlord ? "Bookings" : "Saved",
      icon: isLandlord ? "i-lucide-calendar" : "i-lucide-heart",
      to: isLandlord ? "/dashboard/bookings" : "/dashboard/saved",
    },
    { label: "Profile", icon: "i-lucide-user", to: "/dashboard/profile" },
    { label: "Settings", icon: "i-lucide-settings", to: "/dashboard/settings" },
  ];
});

const initials = computed(() => {
  if (!user.value?.name) return "?";
  return user.value.name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
});

const handleLogout = async () => {
  await logout();
};
</script>

<template>
  <div class="flex min-h-screen flex-col bg-gray-100">
    <!-- ── Top bar ── -->
    <header
      class="z-20 flex h-[60px] flex-shrink-0 items-center justify-between bg-gradient-to-r from-[#0b3a5b] to-[#0e4a72] px-4 text-white"
    >
      <div class="flex items-center gap-3.5">
        <button
          class="flex h-[34px] w-[34px] items-center justify-center rounded-lg text-white transition-colors hover:bg-white/10"
          @click="sidebarOpen = !sidebarOpen"
        >
          <UIcon name="i-lucide-menu" class="size-5" />
        </button>
        <div class="flex items-center gap-2 text-base font-bold tracking-wide">
          <UIcon name="i-lucide-building" class="size-5" />
          <span>RentEasy</span>
        </div>
      </div>

      <div class="flex items-center gap-2.5">
        <div
          class="flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5 text-xs font-medium"
        >
          <UIcon
            :name="user?.role === 'landlord' ? 'i-lucide-home' : 'i-lucide-search'"
            class="size-3.5"
          />
          {{ user?.role === "landlord" ? "Landlord" : "Rental" }}
        </div>

        <button
          class="flex h-[34px] w-[34px] items-center justify-center rounded-lg text-white transition-colors hover:bg-white/10"
        >
          <UIcon name="i-lucide-settings" class="size-5" />
        </button>

        <div
          class="flex cursor-pointer items-center gap-2 rounded-[10px] bg-white/10 py-1.5 pl-1.5 pr-2.5 transition-colors hover:bg-white/20"
        >
          <div
            class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center rounded-full bg-teal-500 text-xs font-semibold text-white"
          >
            {{ initials }}
          </div>
          <div class="leading-tight">
            <p class="text-[13px] font-semibold text-white">{{ user?.name }}</p>
            <span class="text-[11px] text-white/65">
              {{ user?.role === "landlord" ? "Landlord" : "Rental member" }}
            </span>
          </div>
          <UIcon name="i-lucide-chevron-right" class="size-4 text-white/60" />
        </div>
      </div>
    </header>

    <div class="flex min-h-0 flex-1">
      <!-- ── Sidebar ── -->
      <aside
        class="flex flex-shrink-0 flex-col justify-between border-r border-gray-200 bg-gray-50 py-3.5 transition-[width] duration-150 ease-in-out max-md:fixed max-md:bottom-0 max-md:left-0 max-md:top-[60px] max-md:z-30 max-md:shadow-md"
        :class="sidebarOpen ? 'w-[220px]' : 'w-[68px] max-md:w-0 max-md:overflow-hidden'"
      >
        <nav class="flex flex-col gap-4 pt-2">
          <NuxtLink
            v-for="item in navItems"
            :key="item.to"
            :to="item.to"
            active-class="active"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-cyan-50 hover:text-cyan-500 [&.active]:bg-gray-100 [&.active]:text-cyan-500 [&.active]:font-semibold"
          >
            <UIcon :name="item.icon" class="size-5 flex-shrink-0 text-green-500" />
            <span v-if="sidebarOpen">{{ item.label }}</span>
          </NuxtLink>
        </nav>

        <div class="px-2.5">
          <button
            class="flex w-full items-center gap-3 whitespace-nowrap rounded-lg px-3 py-2.5 text-[13px] font-medium text-gray-500 transition-colors hover:bg-red-50 hover:text-red-600"
            @click="handleLogout"
          >
            <UIcon name="i-lucide-log-out" class="size-4" />
            <span v-if="sidebarOpen" class="max-md:hidden">Sign out</span>
          </button>
        </div>
      </aside>

      <!-- ── Main content ── -->
      <main class="flex-1 overflow-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
