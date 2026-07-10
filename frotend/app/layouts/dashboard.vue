<script setup>
definePageMeta({ layout: false });

const { user, logout } = useAuth();

const navItems = computed(() => {
  const isLandlord = user.value?.role === "landlord";
  return [
    { label: "Dashboard", icon: "i-lucide-home", to: "/dashboard" },
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
  <div class="wrap">
    <!-- ── Sidebar ── -->
    <aside class="side">
      <div class="logo">
        <UIcon name="i-lucide-building" class="size-5 text-muted" />
        RentEasy
      </div>

      <!-- Role badge -->
      <div class="role-badge" :class="user?.role">
        <UIcon
          :name="user?.role === 'landlord' ? 'i-lucide-home' : 'i-lucide-search'"
          class="size-3.5"
        />
        {{ user?.role === "landlord" ? "Landlord" : "Rental" }}
      </div>

      <!-- Nav -->
      <nav class="nav">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="nav-item"
          active-class="active"
        >
          <UIcon :name="item.icon" class="size-4" />
          {{ item.label }}
        </NuxtLink>

        <p class="nav-section">Account</p>

        <NuxtLink to="/dashboard/profile" class="nav-item">
          <UIcon name="i-lucide-user" class="size-4" /> Profile
        </NuxtLink>

        <NuxtLink to="/dashboard/settings" class="nav-item">
          <UIcon name="i-lucide-settings" class="size-4" /> Settings
        </NuxtLink>
      </nav>

      <!-- User + logout -->
      <div class="side-footer">
        <div class="user-row">
          <div class="avatar">{{ initials }}</div>
          <div class="user-info">
            <p>{{ user?.name }}</p>
            <span>{{ user?.role === "landlord" ? "Landlord" : "Rental member" }}</span>
          </div>
        </div>

        <button class="logout-btn" @click="handleLogout">
          <UIcon name="i-lucide-log-out" class="size-4" />
          Sign out
        </button>
      </div>
    </aside>

    <!-- ── Main content ── -->
    <main class="main">
      <slot />
    </main>
  </div>
</template>

<style scoped>
.wrap {
  display: grid;
  grid-template-columns: 220px 1fr;
  min-height: 100vh;
  background: var(--color-background-tertiary, #f5f5f5);
}

/* ── Sidebar ── */
.side {
  background: white;
  border-right: 0.5px solid #e5e7eb;
  display: flex;
  flex-direction: column;
}

.logo {
  padding: 20px;
  border-bottom: 0.5px solid #e5e7eb;
  font-size: 15px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
}

.role-badge {
  margin: 12px 16px;
  padding: 5px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
}

.role-badge.rental {
  background: #e1f5ee;
  color: #0f6e56;
}
.role-badge.landlord {
  background: #eeedfe;
  color: #3c3489;
}

.nav {
  padding: 8px 0;
  flex: 1;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 20px;
  font-size: 13px;
  color: #6b7280;
  text-decoration: none;
  transition: background 0.15s;
}

.nav-item:hover {
  background: #f9fafb;
}
.nav-item.active {
  background: #f3f4f6;
  color: #111827;
  font-weight: 500;
}

.nav-section {
  padding: 16px 20px 6px;
  font-size: 11px;
  color: #9ca3af;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}

/* ── Footer ── */
.side-footer {
  border-top: 0.5px solid #e5e7eb;
  padding: 12px 16px;
}

.user-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #dbeafe;
  color: #1d4ed8;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 500;
  flex-shrink: 0;
}

.user-info p {
  font-size: 13px;
  font-weight: 500;
  color: #111827;
}
.user-info span {
  font-size: 11px;
  color: #6b7280;
}

.logout-btn {
  width: 100%;
  padding: 7px;
  border-radius: 6px;
  border: 0.5px solid #e5e7eb;
  background: transparent;
  font-size: 13px;
  color: #6b7280;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.15s;
}

.logout-btn:hover {
  background: #fef2f2;
  color: #dc2626;
  border-color: #fca5a5;
}

/* ── Main ── */
.main {
  padding: 24px;
  overflow: auto;
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .wrap {
    grid-template-columns: 1fr;
  }
  .side {
    display: none;
  }
}
</style>
