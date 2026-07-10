<script setup>
definePageMeta({ layout: "auth" });

const { login, loginWithGoogle, loginWithFacebook, loading, error } = useAuth();

const form = reactive({
  email: "",
  password: "",
});

const localError = ref("");

const handleGoogle = () => {
  localError.value = "";
  loginWithGoogle();
};

const handleFacebook = () => {
  localError.value = "";
  loginWithFacebook();
};

const handleLogin = async () => {
  localError.value = "";

  try {
    await login(form);
  } catch (err) {
    localError.value =
      err?.data?.message || error.value || "Login failed. Please try again.";
  }
};
</script>

<template>
  <div class="w-full">
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
      <p class="text-sm text-gray-500 mt-1">Sign in to Room Hub Marketplace</p>
    </div>

    <!-- Error -->
    <p v-if="localError || error" class="text-sm text-red-500 text-center mb-4">
      {{ localError || error }}
    </p>

    <!-- Social Login -->
    <div class="space-y-2.5 mb-5">
      <button
        @click="handleGoogle"
        :disabled="loading"
        class="w-full flex items-center justify-center gap-3 border border-gray-200 py-2.5 rounded-full hover:bg-gray-50 transition disabled:opacity-50"
      >
        <span class="text-red-500 text-lg font-bold">G</span>
        Continue with Google
      </button>

      <button
        @click="handleFacebook"
        :disabled="loading"
        class="w-full flex items-center justify-center gap-3 border border-gray-200 py-2.5 rounded-full hover:bg-gray-50 transition disabled:opacity-50"
      >
        <span class="text-blue-600 text-lg font-bold">f</span>
        Continue with Facebook
      </button>
    </div>

    <!-- Divider -->
    <div class="flex items-center gap-2 mb-5">
      <div class="flex-1 h-px bg-gray-200"></div>
      <span class="text-xs text-gray-400">OR</span>
      <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleLogin" class="space-y-3">
      <input
        v-model="form.email"
        type="email"
        placeholder="Email address"
        required
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
      />

      <input
        v-model="form.password"
        type="password"
        placeholder="Password"
        required
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
      />

      <div class="flex justify-end">
        <NuxtLink
          to="/auth/forgot-password"
          class="text-xs text-green-600 hover:underline"
        >
          Forgot password?
        </NuxtLink>
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-green-600 text-white py-3 rounded-full font-medium hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="loading">Signing in...</span>
        <span v-else>Sign In</span>
      </button>
    </form>

    <!-- Footer -->
    <div class="text-center mt-5">
      <NuxtLink to="/auth/sign-up" class="text-sm text-green-600 hover:underline">
        Don't have an account? Sign up
      </NuxtLink>
    </div>
  </div>
</template>
