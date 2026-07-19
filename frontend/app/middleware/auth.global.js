export default defineNuxtRouteMiddleware((to) => {
  const { token } = useAuth()

  const isPublic =
    to.path === '/' ||
    to.path.startsWith('/auth') ||
    to.path.startsWith('/listing') ||
    to.path.startsWith('/detail') ||
    to.path.startsWith('/rooms/') ||
    to.path.startsWith('/host')  ||
    to.path.startsWith('/admin/login/') ||
    to.path.startsWith('/test-location')

  if (isPublic) return

  // wait until auth is actually initialized
  if (!token?.value) {
    return navigateTo('/auth/login', { replace: true })
  }
})