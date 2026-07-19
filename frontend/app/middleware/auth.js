// Named middleware — use on pages that require a logged-in user
// Usage: definePageMeta({ middleware: 'auth' })
export default defineNuxtRouteMiddleware(() => {
  const { token } = useAuth()

  if (!token.value) {
    return navigateTo('/auth/login')
  }
})