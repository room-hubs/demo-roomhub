export default defineNuxtPlugin(() => {
    const config       = useRuntimeConfig()
    const accessToken  = useCookie('access_token',  { maxAge: 60 * 60 })
    const refreshToken = useCookie('refresh_token', { maxAge: 60 * 60 * 24 * 30 })
    const router       = useRouter()
    // logout
    const logout = () => {
        accessToken.value  = null
        refreshToken.value = null
        router.push('/auth/login')
    }
    // refresh  token
    const doRefresh = async () => {
        try {
            const data = await $fetch('/auth/refresh', {
                baseURL: config.public.apiBase,
                method:  'POST',
                body:    { refresh_token: refreshToken.value },
            })

            accessToken.value  = data.access_token
            refreshToken.value = data.refresh_token

            return data.access_token
        } catch {
            logout()
            return null
        }
    }
    // fetch api
    const apiFetch = async (url, options = {}) => {
        try {
            return await $fetch(url, {
                baseURL: config.public.apiBase,
                ...options,
                headers: {
                    ...options.headers,
                    Authorization: accessToken.value
                        ? `Bearer ${accessToken.value}`
                        : undefined,
                },
            })
        } catch (error) {
            if (error?.response?.status !== 401) {
                throw error
            }

            if (! refreshToken.value) {
                logout()
                return
            }

            const newToken = await doRefresh()

            if (! newToken) return

            // Retry original request with new token
            return await $fetch(url, {
                baseURL: config.public.apiBase,
                ...options,
                headers: {
                    ...options.headers,
                    Authorization: `Bearer ${newToken}`,
                },
            })
        }
    }

    return {
        provide: { api: apiFetch },
    }
})