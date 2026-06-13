<script setup>
definePageMeta({ layout: 'auth' })

const auth         = useAuth()
const selectedRole = ref(null)
const loading      = ref(false)
const error        = ref(null)
const pageReady    = ref(false)
// init
onMounted(async () => {
  // facebook OAuth hash artifact
  if (window.location.hash === '#_=_') {
    window.history.replaceState(null, '', window.location.href.replace('#_=_', ''))
  }

  // No token -> back to login
  const { token } = useAuth()
  if (!token.value) {
    return navigateTo('/auth/login', { replace: true })
  }

  // Fetch user only if not already in state
  if (!auth.user.value) {
    try {
      await auth.fetchUser()
    } catch {
      return navigateTo('/auth/login', { replace: true })
    }
  }

  const role = auth.user.value?.role

  // Already has a role -> skip to dashboard
  if (role && role !== null) {
    return navigateTo('/dashboard', { replace: true })
  }

  // No role → show role picker
  pageReady.value = true
})
// submit role user
const handleContinue = async () => {
  if (!selectedRole.value || loading.value) return

  // Guard: already has role
  const existingRole = auth.user.value?.role
  if (existingRole) {
    return navigateTo(existingRole === 'admin' ? '/admin' : '/dashboard', { replace: true })
  }

  error.value   = null
  loading.value = true

  try {
    const res  = await auth.updateRole(selectedRole.value)
    const role = res?.user?.role ?? res?.user?.roles?.[0] ?? selectedRole.value

    if (!role) throw new Error('Unable to determine role after assignment.')

    // All roles go to /dashboard
    await navigateTo('/dashboard', { replace: true })
  } catch (err) {
    error.value =
      err?.data?.message ||
      err?.message ||
      'Something went wrong. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">

    <!-- Loading state while init runs -->
    <div v-if="!pageReady" class="text-center text-gray-400 text-sm">
      Loading...
    </div>

    <div v-else class="w-full max-w-lg">

      <!-- Title -->
      <div class="text-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-900">
          How will you use the platform?
        </h1>
        <p class="text-gray-500 mt-2 text-sm">
          Choose a role to continue. You can't change this later.
        </p>
      </div>

      <!-- Role Cards -->
      <div class="grid grid-cols-2 gap-4 mb-6">

        <!-- Rental -->
        <button
          :class="[
            'flex flex-col items-start gap-3 p-6 rounded-xl border-2 text-left transition-all',
            selectedRole === 'rental'
              ? 'border-teal-500 bg-teal-50'
              : 'border-gray-200 bg-white hover:border-gray-300'
          ]"
          @click="selectedRole = 'rental'"
        >
          <div
            :class="[
              'w-12 h-12 rounded-lg flex items-center justify-center',
              selectedRole === 'rental' ? 'bg-teal-200' : 'bg-teal-100'
            ]"
          >
            <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z" />
            </svg>
          </div>
          <p class="font-medium text-gray-900">Rental</p>
          <p class="text-xs text-gray-500">Browse and book properties</p>
          <div class="flex gap-1">
            <span class="text-xs px-2 py-0.5 rounded-full bg-teal-100 text-teal-700">Browse</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-teal-100 text-teal-700">Book</span>
          </div>
        </button>

        <!-- Landlord -->
        <button
          :class="[
            'flex flex-col items-start gap-3 p-6 rounded-xl border-2 text-left transition-all',
            selectedRole === 'landlord'
              ? 'border-purple-500 bg-purple-50'
              : 'border-gray-200 bg-white hover:border-gray-300'
          ]"
          @click="selectedRole = 'landlord'"
        >
          <div
            :class="[
              'w-12 h-12 rounded-lg flex items-center justify-center',
              selectedRole === 'landlord' ? 'bg-purple-200' : 'bg-purple-100'
            ]"
          >
            <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 9.75L12 3l9 6.75V21a.75.75 0 01-.75.75H3.75A.75.75 0 013 21V9.75z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 21V12h6v9" />
            </svg>
          </div>
          <p class="font-medium text-gray-900">Landlord</p>
          <p class="text-xs text-gray-500">List and manage properties</p>
          <div class="flex gap-1">
            <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">List</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Manage</span>
          </div>
        </button>

      </div>

      <!-- Error -->
      <Transition name="fade">
        <p v-if="error" class="text-red-500 text-sm text-center mb-4">
          {{ error }}
        </p>
      </Transition>

      <!-- Submit -->
      <button
        :disabled="!selectedRole || loading"
        class="w-full py-3 rounded-xl font-medium text-white bg-gray-900 transition
               disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-700"
        @click="handleContinue"
      >
        <span v-if="loading">Processing...</span>
        <span v-else-if="selectedRole === 'rental'">Continue as Renter</span>
        <span v-else-if="selectedRole === 'landlord'">Continue as Landlord</span>
        <span v-else>Select a role to continue</span>
      </button>

    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from,
.fade-leave-to     { opacity: 0; }
</style>