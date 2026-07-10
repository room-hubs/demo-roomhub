<template>
  <div class="wrap">
    <div class="right">
      <div class="form-header">
        <h2>Admin access</h2>
        <p>Sign in to manage the platform securely.</p>
      </div>
      <div v-if="error" class="error-box">
        <svg
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <circle cx="12" cy="12" r="10" />
          <line x1="12" y1="8" x2="12" y2="12" />
          <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
        {{ error }}
      </div>

      <div class="field">
        <label>Email</label>
        <div class="field-wrap">
          <svg
            class="field-icon"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <rect x="2" y="4" width="20" height="16" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
          </svg>
          <input
            type="email"
            v-model="form.email"
            placeholder="admin@example.com"
            required
          />
        </div>
      </div>

      <div class="field">
        <label>Password</label>
        <div class="field-wrap">
          <svg
            class="field-icon"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <rect x="3" y="11" width="18" height="11" rx="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
          </svg>
          <input
            type="password"
            v-model="form.password"
            placeholder="Enter password"
            required
          />
        </div>
      </div>

      <button class="btn" :disabled="loading" @click="handleLogin">
        <svg
          v-if="!loading"
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
          <polyline points="10 17 15 12 10 7" />
          <line x1="15" y1="12" x2="3" y2="12" />
        </svg>
        {{ loading ? "Signing in…" : "Sign in" }}
      </button>

      <p class="footer-note">Protected area · Authorized personnel only</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "auth" });

import { ref, reactive, onMounted, onUnmounted } from "vue";

const { login, token, user } = useAuth();

if (token.value && user.value?.role === "admin") {
  await navigateTo("/admin", { replace: true });
}

const slides = [
  {
    title: "Full platform visibility",
    desc:
      "Monitor users, listings, and activity across the entire platform in one place.",
  },
  {
    title: "Manage every account",
    desc: "Approve, suspend, or update any landlord or tenant account instantly.",
  },
  {
    title: "Secure admin access",
    desc: "Role-based access control keeps your platform safe and auditable.",
  },
];

const current = ref(0);
let timer = null;

function goTo(n) {
  current.value = n;
}

function next() {
  current.value = (current.value + 1) % slides.length;
}

onMounted(() => {
  timer = setInterval(next, 3500);
});
onUnmounted(() => clearInterval(timer));

const form = reactive({ email: "", password: "" });
const loading = ref(false);
const error = ref(null);

const handleLogin = async () => {
  error.value = null;
  loading.value = true;

  try {
    const res = await login({ email: form.email, password: form.password });

    if (res?.user?.role !== "admin") {
      error.value = "Access denied. Admins only.";
      return;
    }

    await navigateTo("/admin", { replace: true });
  } catch (err) {
    error.value = err?.data?.message || err?.message || "Login failed";
  } finally {
    loading.value = false;
  }
};
</script>
