<template>
  <div class="p-8">
    <h1 class="text-3xl font-bold mb-6">Landlord Properties</h1>
    
    <div class="bg-white rounded-2xl p-6 shadow">
      <h2 class="text-lg font-semibold mb-4">Role Debug Information</h2>
      
      <div class="space-y-3 text-sm">
        <p><strong>Current Role:</strong> 
          <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700">
            {{ currentRole || 'No role detected' }}
          </span>
        </p>
        <p><strong>User Object:</strong> {{ user ? JSON.stringify(user, null, 2) : 'null' }}</p>
        <p><strong>Is Landlord:</strong> {{ isLandlord ? 'Yes' : 'No' }}</p>
      </div>
    </div>

    <div v-if="currentRole === 'landlord'" class="mt-8">
      <p class="text-green-600 font-medium">✅ Landlord content is visible</p>
    </div>
    <div v-else>
      <p class="text-red-600">Role is not landlord</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'dashboard',
  middleware: 'auth'
})

const { user, isLandlord } = useAuth()

const currentRole = computed(() => user.value?.role)
</script>