// =========================
// FORGOT PASSWORD (PHONE)
// =========================
export const useForgotPassword = () => {
  const config = useRuntimeConfig();

  const loading = useState("forgot_loading", () => false);
  const error = useState("forgot_error", () => null);
  const step = useState("forgot_step", () => 1);
  const resetToken = useState("forgot_token", () => "");
  const phone = useState("forgot_phone", () => "");

  // =========================
  // STEP 1: SEND OTP SMS
  // =========================
  const checkPhoneAndSendOtp = async (value) => {
    loading.value = true;
    error.value = null;

    try {
      await $fetch("/auth/forgot-password/check-phone", {
        baseURL: config.public.apiBase,
        method: "POST",
        body: { phone: value },
      });

      phone.value = value;
      step.value = 2;
    } catch (err) {
      error.value = err?.data?.message || "Phone number not found";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // =========================
  // STEP 2: VERIFY OTP
  // =========================
  const verifyOtp = async (otp) => {
    loading.value = true;
    error.value = null;

    try {
      const res = await $fetch("/auth/forgot-password/verify-otp", {
        baseURL: config.public.apiBase,
        method: "POST",
        body: {
          phone: phone.value,
          otp,
        },
      });

      resetToken.value = res.reset_token;
      step.value = 3;
    } catch (err) {
      error.value = err?.data?.message || "Invalid OTP";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // =========================
  // RESEND OTP
  // =========================
  const resendOtp = async () => {
    return checkPhoneAndSendOtp(phone.value);
  };

  // =========================
  // STEP 3: RESET PASSWORD
  // =========================
  const resetPassword = async (password, confirmPassword) => {
    loading.value = true;
    error.value = null;

    try {
      await $fetch("/auth/forgot-password/reset-password-by-phone", {
        baseURL: config.public.apiBase,
        method: "POST",
        body: {
          phone: phone.value,
          reset_token: resetToken.value,
          password,
          password_confirmation: confirmPassword,
        },
      });

      step.value = 4;
    } catch (err) {
      error.value = err?.data?.message || "Reset failed";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // =========================
  // RESET STATE
  // =========================
  const reset = () => {
    step.value = 1;
    error.value = null;
    loading.value = false;
    resetToken.value = "";
    phone.value = "";
  };

  return {
    step,
    loading,
    error,
    phone,

    checkPhoneAndSendOtp,
    verifyOtp,
    resendOtp,
    resetPassword,
    reset,
  };
};

// ─── Main Auth Composable ─────────────────────────────────────────────────────
export const useAuth = () => {
  const config = useRuntimeConfig();

  // =========================
  // STATE
  // =========================
  const authInitialized = useState("auth_initialized", () => false);

  const token = useCookie("access_token", {
    secure: process.env.NODE_ENV === "production",
    sameSite: "lax",
    maxAge: 60 * 60,
  });

  const refreshToken = useCookie("refresh_token", {
    secure: process.env.NODE_ENV === "production",
    sameSite: "lax",
    maxAge: 60 * 60 * 24 * 30,
  });

  const user = useState("user", () => null);
  const isSetupMode = useState("auth_setup_mode", () => false);
  const loading = useState("auth_loading", () => false);
  const error = useState("auth_error", () => null);

  // =========================
  // HEADERS
  // =========================
  const authHeaders = computed(() => ({
    Authorization: `Bearer ${token.value}`,
  }));

  // =========================
  // HELPERS
  // =========================
  const normalizeUser = (data) => {
    if (!data) return null;
    return {
      ...data,
      role: data.role ?? data.roles?.[0] ?? null,
    };
  };

  const setTokens = (res = {}) => {
    if (res.access_token) token.value = res.access_token;
    else if (res.token) token.value = res.token;

    if (res.refresh_token) refreshToken.value = res.refresh_token;
    else if (res.refreshToken) refreshToken.value = res.refreshToken;
  };

  const withLoading = async (fn) => {
    loading.value = true;
    error.value = null;
    try {
      return await fn();
    } finally {
      loading.value = false;
    }
  };

  const resolveDestination = (role) => {
    if (!role) return "/auth/select-role";
    if (role === "admin") return "/admin";
    if (role === "landlord") return "/dashboard/landlord";
    return "/dashboard/rental";
  };

  // =========================
  // CLEAR AUTH
  // =========================
  const clearAuth = async () => {
    token.value = null;
    refreshToken.value = null;
    user.value = null;
    isSetupMode.value = false;
    loading.value = false;
    error.value = null;
    authInitialized.value = false;
  };

  // =========================
  // REFRESH TOKEN
  // =========================
  const tryRefreshToken = async () => {
    if (!refreshToken.value) {
      await clearAuth();
      return false;
    }

    refreshPromise ??= (async () => {
      try {
        const res = await $fetch("/auth/refresh", {
          baseURL: config.public.apiBase,
          method: "POST",
          body: { refresh_token: refreshToken.value },
        });

        setTokens(res);
        return true;
      } catch (e) {
        await clearAuth();
        return false;
      } finally {
        refreshPromise = null;
      }
    })();

    return refreshPromise;
  };

  // =========================
  // FETCH USER
  // =========================
  const fetchUser = async () => {
    if (!token.value) return null;

    const doFetch = () =>
      $fetch("/auth/me", {
        baseURL: config.public.apiBase,
        headers: authHeaders.value,
      });

    return withLoading(async () => {
      try {
        const res = await doFetch();
        user.value = normalizeUser(res);
        return user.value;
      } catch (err) {
        if (err?.response?.status !== 401) {
          await clearAuth();
          throw err;
        }

        const refreshed = await tryRefreshToken();
        if (!refreshed) throw err;

        const res = await doFetch();
        user.value = normalizeUser(res);
        return user.value;
      }
    });
  };

  // =========================
  // REGISTER
  // =========================
  const register = async (data) => {
    return withLoading(async () => {
      try {
        const res = await $fetch("/auth/register", {
          baseURL: config.public.apiBase,
          method: "POST",
          body: data,
        });

        setTokens(res);
        user.value = normalizeUser(res.user);
        authInitialized.value = true;

        await nextTick();

        if (res.needs_role) {
          return navigateTo("/auth/select-role", { replace: true });
        }

        return navigateTo("/", { replace: true });
      } catch (err) {
        error.value = err?.data?.message;
        throw err;
      }
    });
  };

  // =========================
  // LOGIN
  // =========================
  const login = async (data) => {
    return withLoading(async () => {
      try {
        const res = await $fetch("/auth/login", {
          baseURL: config.public.apiBase,
          method: "POST",
          body: data,
        });

        setTokens(res);
        user.value = normalizeUser(res.user);
        isSetupMode.value = false;
        authInitialized.value = true;

        await nextTick();

        return navigateTo(resolveDestination(user.value?.role), {
          replace: true,
        });
      } catch (err) {
        error.value = err?.data?.message || "Login failed";
        throw err;
      }
    });
  };

  // =========================
  // ROLE UPDATE
  // =========================
  const updateRole = async (role) => {
    return withLoading(async () => {
      try {
        const res = await $fetch("/auth/role", {
          baseURL: config.public.apiBase,
          method: "POST",
          headers: authHeaders.value,
          body: { role },
        });

        setTokens(res);
        user.value = normalizeUser(res.user);
        isSetupMode.value = false;

        return res;
      } catch (err) {
        error.value = err?.data?.message || "Failed to assign role";
        throw err;
      }
    });
  };

  // =========================
  // INIT AUTH
  // =========================
  const initAuth = async () => {
    if (authInitialized.value && user.value) return;

    if (!token.value && !refreshToken.value) {
      await clearAuth();
      return;
    }

    try {
      await fetchUser();
      authInitialized.value = true;
    } catch {
      await clearAuth();
    }
  };

  // =========================
  // LOGOUT
  // =========================
  const logout = async () => {
    try {
      if (token.value) {
        await $fetch("/auth/logout", {
          baseURL: config.public.apiBase,
          method: "POST",
          headers: authHeaders.value,
        });
      }
    } catch (err) {
      console.error(err);
    } finally {
      await clearAuth();
      await navigateTo("/auth/login", { replace: true });
    }
  };

  // =========================
  // OAUTH
  // =========================
  const loginWithGoogle = () => {
    window.location.href = `${config.public.apiBase}/auth/google/redirect`;
  };

  const loginWithFacebook = () => {
    window.location.href = `${config.public.apiBase}/auth/facebook/redirect`;
  };

  // =========================
  // RETURN
  // =========================
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
  };
};
