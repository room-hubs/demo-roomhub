<template>
  <div class="flex items-center justify-center min-h-screen">
    <p v-if="errorMsg" class="text-red-500 text-sm">{{ errorMsg }}</p>
    <p v-else class="text-gray-500 text-sm">Signing you in...</p>
  </div>
</template>

<script setup>
definePageMeta({ layout: false })

const route  = useRoute()
const config = useRuntimeConfig()
const { setTokens, fetchUser, clearAuth } = useAuth()

const errorMsg = ref(null)

onMounted(async () => {
  if (route.query.error) {
    errorMsg.value = decodeURIComponent(route.query.error)
    await clearAuth()
    setTimeout(() => navigateTo('/auth/login', { replace: true }), 2000)
    return
  }

  const code = route.query.code?.toString()

  if (!code) {
    await clearAuth()
    return navigateTo('/auth/login', { replace: true })
  }

  try {
    const res = await $fetch('/auth/oauth/exchange', {
      baseURL: config.public.apiBase,
      method:  'POST',
      body:    { code },
    })

    setTokens(res)

    await new Promise(resolve => setTimeout(resolve, 50))

    const profile = await fetchUser()

    if (res.needs_role || !profile?.role) {
      return navigateTo('/auth/select-role', { replace: true })
    }

    return navigateTo('/dashboard', { replace: true })

  } catch (err) {
    console.error('OAuth exchange error:', err)
    errorMsg.value = err?.data?.message || 'Authentication failed. Redirecting...'
    await clearAuth()
    setTimeout(() => navigateTo('/auth/login', { replace: true }), 2000)
  }
})
</script>