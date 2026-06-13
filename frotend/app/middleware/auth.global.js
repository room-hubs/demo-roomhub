export default defineNuxtRouteMiddleware((to) => {
  const { token, user } = useAuth()

  const publicRoutes = [
    '/auth/login',
    '/auth/sign-up',
    '/auth/callback',
    '/auth/error',
    '/admin/login',
    '/',
    '/room-detail',


  ]

  if (publicRoutes.includes(to.path)) return

  if (!token.value) {
    return navigateTo('/auth/login', { replace: true })
  }
})