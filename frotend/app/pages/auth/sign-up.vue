<script setup>
definePageMeta({ layout: "auth" });

const { register, loginWithGoogle, loginWithFacebook, loading, error } = useAuth();

const form = reactive({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirmation: "",
});

const formError = ref(null);

const rules = [
  [() => !form.name.trim(), "Full name is required."],
  [() => !form.email.trim(), "Email address is required."],
  [() => !form.password, "Password is required."],
  [() => form.password.length < 6, "Password must be at least 6 characters."],
  [() => form.password !== form.password_confirmation, "Passwords do not match."],
];

const validate = () => {
  for (const [check, msg] of rules) {
    if (check()) return msg;
  }
  return null;
};

const handleRegister = async () => {
  formError.value = validate();
  if (formError.value) return;

  try {
    await register(form);
    // navigation is handled by register() based on whether the user needs a role
  } catch {
    // error is surfaced via useAuth's error state
  }
};
</script>

<template>
  <div class="w-full">
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
      <p class="text-sm text-gray-500 mt-1">Join Room Hub Marketplace</p>
    </div>

    <!-- Errors -->
    <Transition name="fade">
      <p v-if="formError || error" class="text-sm text-red-500 text-center mb-4">
        {{ formError || error }}
      </p>
    </Transition>

    <!-- Social Login -->
    <div class="space-y-2.5 mb-5">
      <button
        type="button"
        :disabled="loading"
        class="w-full flex items-center justify-center gap-3 border cursor-pointer border-gray-200 py-2.5 rounded-full hover:bg-gray-50 transition disabled:opacity-50"
        @click="loginWithGoogle"
      >
        <span class="text-red-500 text-lg font-bold">G</span>
        Continue with Google
      </button>

      <button
        type="button"
        :disabled="loading"
        class="w-full flex items-center justify-center gap-3 border cursor-pointer border-gray-200 py-2.5 rounded-full hover:bg-gray-50 transition disabled:opacity-50"
        @click="loginWithFacebook"
      >
        <span class="text-blue-600 text-lg font-bold">f</span>
        Continue with Facebook
      </button>
    </div>

    <!-- Divider -->
    <div class="flex items-center gap-2 mb-5">
      <div class="flex-1 h-px bg-gray-200" />
      <span class="text-xs text-gray-400">OR</span>
      <div class="flex-1 h-px bg-gray-200" />
    </div>

    <!-- Form -->
    <form class="space-y-3" @submit.prevent="handleRegister">
      <input
        v-model="form.name"
        type="text"
        placeholder="Full name"
        autocomplete="name"
        required
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
      />

      <input
        v-model="form.email"
        type="email"
        placeholder="Email address"
        autocomplete="email"
        required
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
      />

      <input
        v-model="form.phone"
        type="tel"
        placeholder="Phone number (optional)"
        autocomplete="tel"
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
      />

      <input
        v-model="form.password"
        type="password"
        placeholder="Password"
        autocomplete="new-password"
        required
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
      />

      <input
        v-model="form.password_confirmation"
        type="password"
        placeholder="Confirm password"
        autocomplete="new-password"
        required
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition text-sm"
      />

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-green-600 text-white py-3 rounded-full font-medium hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed mt-1"
      >
        {{ loading ? "Creating account…" : "Create Account" }}
      </button>
    </form>

    <!-- Footer -->
    <p class="text-center text-sm mt-5">
      <NuxtLink to="/auth/login" class="text-green-600 hover:underline">
        Already have an account? Sign in
      </NuxtLink>
    </p>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
