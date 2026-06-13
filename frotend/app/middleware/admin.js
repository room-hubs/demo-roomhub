// Named middleware — use on pages that require admin role
// Usage: definePageMeta({ middleware: 'admin' })
export default defineNuxtRouteMiddleware(async (to) => {
  const { token, user, fetchUser, clearAuth } = useAuth()

  if (!token.value) {
    return navigateTo('/auth/login')
  }

  if (!user.value) {
    try {
      await fetchUser()
    } catch {
      await clearAuth()
      return navigateTo('/auth/login')
    }   
  }

  const role = user.value?.role ?? user.value?.roles?.[0]

  if (role !== 'admin') {
    return navigateTo('/')
  }
})