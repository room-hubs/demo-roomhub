let refreshPromise  = null
let authInitialized = false

export const useAuth = () => {
  const config = useRuntimeConfig()

  // cookies
  const token        = useCookie('access_token',  { secure: process.env.NODE_ENV === 'production', sameSite: 'lax', maxAge: 60 * 60 })
  const refreshToken = useCookie('refresh_token', { secure: process.env.NODE_ENV === 'production', sameSite: 'lax', maxAge: 60 * 60 * 24 * 30 })

  // state
  const user        = useState('user',            () => null)
  const isSetupMode = useState('auth_setup_mode', () => false)
  const loading     = useState('auth_loading',    () => false)
  const error       = useState('auth_error',      () => null)

  // helpers
  const authHeaders = computed(() => ({
    Authorization: `Bearer ${token.value}`,
  }))

  const normalizeUser = (data) => {
    if (!data) return null
    return {
      ...data,
      role: data.role ?? data.roles?.[0] ?? null,
    }
  }

  const setTokens = ({ access_token, refresh_token } = {}) => {
    if (access_token)  token.value        = access_token
    if (refresh_token) refreshToken.value = refresh_token
  }

  const withLoading = async (fn) => {
    loading.value = true
    error.value   = null
    try {
      return await fn()
    } finally {
      loading.value = false
    }
  }

  const resolveDestination = (role) => {
    if (!role)               return '/auth/select-role'
    if (role === 'admin')    return '/admin'
    if (role === 'landlord') return '/dashboard/landlord'
    return '/dashboard/rental'
  }
  // clear auth
  const clearAuth = async () => {
    token.value        = null
    refreshToken.value = null
    user.value         = null
    isSetupMode.value  = false
    loading.value      = false
    error.value        = null
    authInitialized    = false
    await nextTick()
  }
  // refresh token
  const tryRefreshToken = async () => {
    if (!refreshToken.value) {
      await clearAuth()
      return false
    }

    refreshPromise ??= (async () => {
      try {
        const res = await $fetch('/auth/refresh', {
          baseURL: config.public.apiBase,
          method:  'POST',
          body:    { refresh_token: refreshToken.value },
        })
        setTokens(res)
        return true
      } catch {
        await clearAuth()
        return false
      } finally {
        refreshPromise = null
      }
    })()

    return refreshPromise
  }

  // fetch user
  const fetchUser = async () => {
    if (!token.value) return null

    const doFetch = () => $fetch('/auth/me', {
      baseURL: config.public.apiBase,
      headers: authHeaders.value,
    })

    return withLoading(async () => {
      try {
        const res  = await doFetch()
        user.value = normalizeUser(res)
        return user.value
      } catch (err) {
        if (err?.response?.status !== 401) {
          await clearAuth()
          throw err
        }

        const refreshed = await tryRefreshToken()
        if (!refreshed) throw err

        const res  = await doFetch()
        user.value = normalizeUser(res)
        return user.value
      }
    })
  }

  // register function
  const register = async (data) => {
    return withLoading(async () => {
      try {
        const res = await $fetch('/auth/register', {
          baseURL: config.public.apiBase,
          method:  'POST',
          body:    data,
        })

        setTokens(res)
        user.value        = normalizeUser(res.user)
        isSetupMode.value = true
        authInitialized   = true

        await nextTick()
        return navigateTo('/auth/select-role', { replace: true })
      } catch (err) {
        error.value = err?.data?.message || 'Registration failed. Please try again.'
        throw err
      }
    })
  }

  // login function
  const login = async (data) => {
    return withLoading(async () => {
      try {
        const res = await $fetch('/auth/login', {
          baseURL: config.public.apiBase,
          method:  'POST',
          body:    data,
        })

        setTokens(res)
        user.value        = normalizeUser(res.user)
        isSetupMode.value = false
        authInitialized   = true

        await nextTick()
        return navigateTo(resolveDestination(user.value?.role), { replace: true })
      } catch (err) {
        error.value = err?.data?.message || 'Login failed. Check your credentials.'
        throw err
      }
    })
  }

  // update role of user 
  const updateRole = async (role) => {
    return withLoading(async () => {
      try {
        const res = await $fetch('/auth/role', {
          baseURL: config.public.apiBase,
          method:  'POST',
          headers: authHeaders.value,
          body:    { role },
        })

        setTokens(res)
        user.value        = normalizeUser(res.user)
        isSetupMode.value = false

        return res
      } catch (err) {
        error.value = err?.data?.message || 'Failed to assign role.'
        throw err
      }
    })
  }
  // init auth
  const initAuth = async () => {
    if (authInitialized && user.value) return

    if (!token.value && !refreshToken.value) {
      await clearAuth()
      return
    }

    try {
      await fetchUser()
      authInitialized = true
    } catch {
      await clearAuth()
    }
  }
  // social login
  const loginWithGoogle = () => {
    window.location.href = `${config.public.apiBase}/auth/google/redirect`
  }

  const loginWithFacebook = () => {
    window.location.href = `${config.public.apiBase}/auth/facebook/redirect`
  }

  // logout function 
  const logout = async () => {
    try {
      if (token.value) {
        await $fetch('/auth/logout', {
          baseURL: config.public.apiBase,
          method:  'POST',
          headers: authHeaders.value,
        })
      }
    } catch (err) {
      console.error('[useAuth] logout error:', err)
    } finally {
      await clearAuth()
      await navigateTo('/auth/login', { replace: true })
    }
  }

  return {
    token,
    refreshToken,
    user,
    isSetupMode,
    loading,
    error,
    setTokens,
    fetchUser,
    register,
    login,
    updateRole,
    initAuth,
    logout,
    clearAuth,
    tryRefreshToken,
    loginWithGoogle,
    loginWithFacebook,
  }
}