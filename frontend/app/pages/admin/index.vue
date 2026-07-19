<script setup>
import { sub } from 'date-fns'
import HomeDateRangePicker from '~/components/admin/home/HomeDateRangePicker.vue'
import HomePeriodSelect from '~/components/admin/home/HomePeriodSelect.vue'
import HomeSales from '~/components/admin/home/HomeSales.vue'
import HomeStats from '~/components/admin/home/HomeStats.vue'
// import HomeChart from '~/components/admin/home/HomeChart.vue'

definePageMeta({ layout: 'admin' })

const { isNotificationsSlideoverOpen } = useDashboard()
const { logout, user } = useAuth()

const items = [[
  {
    label: 'New mail',
    icon: 'i-lucide-send',
    to: '/inbox',
  },
  {
    label: 'New customer',
    icon: 'i-lucide-user-plus',
    to: '/customers',
  },
]]

const userMenuItems = [[
  {
    label: 'Logout',
    icon: 'i-lucide-log-out',
    onSelect: () => logout(),
  },
]]

const range = shallowRef({
  start: sub(new Date(), { days: 14 }),
  end: new Date(),
})

const period = ref('daily')
</script>

<template>
  <UDashboardPanel id="home">
    <template #header>
      <UDashboardNavbar title="Home" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UTooltip text="Notifications" :shortcuts="['N']">
            <UButton
              color="neutral"
              variant="ghost"
              square
              @click="isNotificationsSlideoverOpen = true"
            >
              <UChip color="error" inset>
                <UIcon name="i-lucide-bell" class="size-5 shrink-0" />
              </UChip>
            </UButton>
          </UTooltip>

          <UDropdownMenu :items="items">
            <UButton icon="i-lucide-plus" size="md" class="rounded-full" />
          </UDropdownMenu>

          <UDropdownMenu :items="userMenuItems">
            <UButton
              color="neutral"
              variant="ghost"
              :label="user?.name"
              trailing-icon="i-lucide-chevron-down"
            />
          </UDropdownMenu>

          <UTooltip text="Logout">
            <UButton
              color="error"
              variant="ghost"
              icon="i-lucide-log-out"
              square
              @click="logout()"
            />
          </UTooltip>
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <HomeDateRangePicker v-model="range" class="-ms-1" />
          <HomePeriodSelect v-model="period" :range="range" />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <HomeStats :period="period" :range="range" />

      <ClientOnly>
        <HomeChart :period="period" :range="range" />
        <template #fallback>
          <div class="h-96 flex items-center justify-center text-muted text-sm">
            Loading chart...
          </div>
        </template>
      </ClientOnly>

      <HomeSales :period="period" :range="range" />
    </template>
  </UDashboardPanel>
</template>