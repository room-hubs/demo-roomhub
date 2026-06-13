<script setup>
definePageMeta({ layout: 'auth' })

const { register, loginWithGoogle, loginWithFacebook, loading, error } = useAuth()

const form = reactive({
  name:                  '',
  email:                 '',
  phone:                 '',
  password:              '',
  password_confirmation: '',
})

const formError = ref(null)

const rules = [
  [() => !form.name.trim(),                              'Full name is required.'],
  [() => !form.email.trim(),                             'Email address is required.'],
  [() => !form.password,                                 'Password is required.'],
  [() => form.password.length < 6,                       'Password must be at least 6 characters.'],
  [() => form.password !== form.password_confirmation,   'Passwords do not match.'],
]

const validate = () => {
  for (const [check, msg] of rules) {
    if (check()) return msg
  }
  return null
}

const handleRegister = async () => {
  formError.value = validate()
  if (formError.value) return

  try {
    await register(form)
    navigateTo('/auth/select-role') 
  } catch {
    // error is surfaced via useAuth's error state
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-lg p-8">

      <!-- Header -->
      <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
        <p class="text-sm text-gray-500 mt-1">Join Room Hub Marketplace</p>
      </div>

      <!-- Errors -->
      <Transition name="fade">
        <p
          v-if="formError || error"
          class="text-sm text-red-500 text-center mb-4"
        >
          {{ formError || error }}
        </p>
      </Transition>

      <!-- Social Login -->
      <div class="space-y-3 mb-6">
        <button
          type="button"
          :disabled="loading"
          class="w-full flex items-center justify-center gap-3 border border-gray-300 py-3 rounded-xl hover:bg-gray-50 transition disabled:opacity-50"
          @click="loginWithGoogle"
        >
          <span class="text-red-500 text-xl font-bold">G</span>
          Continue with Google
        </button>

        <button
          type="button"
          :disabled="loading"
          class="w-full flex items-center justify-center gap-3 border border-gray-300 py-3 rounded-xl hover:bg-gray-50 transition disabled:opacity-50"
          @click="loginWithFacebook"
        >
          <span class="text-blue-600 text-xl font-bold">f</span>
          Continue with Facebook
        </button>
      </div>

      <!-- Divider -->
      <div class="flex items-center gap-2 mb-6">
        <div class="flex-1 h-px bg-gray-200" />
        <span class="text-xs text-gray-400">OR</span>
        <div class="flex-1 h-px bg-gray-200" />
      </div>

      <!-- Form -->
      <form class="space-y-4" @submit.prevent="handleRegister">

        <input
          v-model="form.name"
          type="text"
          placeholder="Full name"
          autocomplete="name"
          required
          class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
        />

        <input
          v-model="form.email"
          type="email"
          placeholder="Email address"
          autocomplete="email"
          required
          class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
        />

        <input
          v-model="form.phone"
          type="tel"
          placeholder="Phone number (optional)"
          autocomplete="tel"
          class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
        />

        <input
          v-model="form.password"
          type="password"
          placeholder="Password"
          autocomplete="new-password"
          required
          class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
        />

        <input
          v-model="form.password_confirmation"
          type="password"
          placeholder="Confirm password"
          autocomplete="new-password"
          required
          class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition"
        />

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-gray-900 text-white py-3.5 rounded-xl font-medium hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed mt-2"
        >
          {{ loading ? 'Creating account…' : 'Create Account' }}
        </button>

      </form>

      <!-- Footer -->
      <p class="text-center text-sm mt-6">
        <NuxtLink to="/auth/login" class="text-blue-600 hover:underline">
          Already have an account? Sign in
        </NuxtLink>
      </p>

    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from,
.fade-leave-to    { opacity: 0; }
</style>