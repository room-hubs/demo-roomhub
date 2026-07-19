// Named middleware — use on pages that require a role
// Usage: definePageMeta({ middleware: ['auth', 'role'] })
export default defineNuxtRouteMiddleware((to) => {
  const { user } = useAuth()

  if (!user.value) {
    return navigateTo('/auth/login')
  }

  const role = user.value?.role ?? user.value?.roles?.[0]

  if (!role) {
    return navigateTo('/auth/select-role')
  }

  if (role === 'admin') {
    if (!to.path.startsWith('/admin')) return navigateTo('/admin')
    return
  }
})