<template>
  <div class="wrap">

    <!-- LEFT — auto slider -->
    <div class="left">
      <div class="brand">
        <div class="brand-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4e8ef7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4v5c0 5-4 9-8 10-4-1-8-5-8-10V7l8-4z"/></svg>
        </div>
        <span class="brand-name">AdminPanel</span>
      </div>

      <div class="slides-track">
        <div class="slides" :style="{ transform: `translateX(-${current * 100}%)` }">
          <div class="slide" v-for="(slide, i) in slides" :key="i">
            <div class="slide-icon">
              <component :is="slide.icon" />
            </div>
            <div class="slide-title">{{ slide.title }}</div>
            <div class="slide-desc">{{ slide.desc }}</div>
          </div>
        </div>
      </div>

      <div class="dots">
        <button
          v-for="(_, i) in slides"
          :key="i"
          class="dot"
          :class="{ active: current === i }"
          @click="goTo(i)"
          :aria-label="`Go to slide ${i + 1}`"
        />
      </div>
    </div>

    <!-- RIGHT — form -->
    <div class="right">
      <div class="form-header">
        <h2>Admin access</h2>
        <p>Sign in to manage the platform securely.</p>
      </div>

      <div v-if="error" class="error-box">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ error }}
      </div>

      <div class="field">
        <label>Email</label>
        <div class="field-wrap">
          <svg class="field-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
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
          <svg class="field-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input
            type="password"
            v-model="form.password"
            placeholder="Enter password"
            required
          />
        </div>
      </div>

      <button class="btn" :disabled="loading" @click="handleLogin">
        <svg v-if="!loading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        {{ loading ? 'Signing in…' : 'Sign in' }}
      </button>

      <p class="footer-note">Protected area · Authorized personnel only</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'auth' })

import { ref, reactive, onMounted, onUnmounted } from 'vue'

const { login, token, user } = useAuth()

if (token.value && user.value?.role === 'admin') {
  await navigateTo('/admin', { replace: true })
}

const slides = [
  {
    title: 'Full platform visibility',
    desc: 'Monitor users, listings, and activity across the entire platform in one place.',
  },
  {
    title: 'Manage every account',
    desc: 'Approve, suspend, or update any landlord or tenant account instantly.',
  },
  {
    title: 'Secure admin access',
    desc: 'Role-based access control keeps your platform safe and auditable.',
  },
]

const current = ref(0)
let timer = null

function goTo(n) {
  current.value = n
}

function next() {
  current.value = (current.value + 1) % slides.length
}

onMounted(() => { timer = setInterval(next, 3500) })
onUnmounted(() => clearInterval(timer))

const form = reactive({ email: '', password: '' })
const loading = ref(false)
const error = ref(null)

const handleLogin = async () => {
  error.value = null
  loading.value = true

  try {
    const res = await login({ email: form.email, password: form.password })

    if (res?.user?.role !== 'admin') {
      error.value = 'Access denied. Admins only.'
      return
    }

    await navigateTo('/admin', { replace: true })
  } catch (err) {
    error.value = err?.data?.message || err?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.wrap {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
}

/* ─── LEFT ─── */
.left {
  position: relative;
  background: #0c1a2e;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.brand {
  position: absolute;
  top: 24px;
  left: 24px;
  display: flex;
  align-items: center;
  gap: 10px;
  z-index: 2;
}

.brand-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #1a3a6b;
  display: flex;
  align-items: center;
  justify-content: center;
}

.brand-name {
  font-size: 13px;
  font-weight: 500;
  color: #8fa3bf;
}

.slides-track {
  flex: 1;
  overflow: hidden;
}

.slides {
  display: flex;
  height: 100%;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide {
  min-width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 20px;
  padding: 80px 48px 80px;
  text-align: center;
}

.slide-icon {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: #1a3a6b;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4e8ef7;
  font-size: 28px;
}

.slide-title {
  font-size: 20px;
  font-weight: 500;
  color: #e8f0fe;
  line-height: 1.4;
}

.slide-desc {
  font-size: 13px;
  color: #8fa3bf;
  line-height: 1.7;
  max-width: 240px;
}

.dots {
  position: absolute;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
}

.dot {
  width: 6px;
  height: 6px;
  border-radius: 9999px;
  background: #2a3f5f;
  border: none;
  cursor: pointer;
  padding: 0;
  transition: background 0.3s, width 0.3s;
}

.dot.active {
  width: 20px;
  background: #4e8ef7;
}

/* ─── RIGHT ─── */
.right {
  padding: 64px 56px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: white;
}

.form-header {
  margin-bottom: 32px;
}

.form-header h2 {
  font-size: 22px;
  font-weight: 500;
  color: #0f172a;
  margin-bottom: 6px;
}

.form-header p {
  font-size: 13px;
  color: #64748b;
}

.error-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fef2f2;
  border: 0.5px solid #fca5a5;
  border-radius: 8px;
  padding: 10px 14px;
  font-size: 13px;
  color: #b91c1c;
  margin-bottom: 20px;
}

.field {
  margin-bottom: 20px;
}

.field label {
  display: block;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #64748b;
  margin-bottom: 8px;
}

.field-wrap {
  position: relative;
}

.field-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}

.field input {
  width: 100%;
  height: 44px;
  padding: 0 14px 0 40px;
  border: 0.5px solid #e2e8f0;
  border-radius: 8px;
  background: #f8fafc;
  color: #0f172a;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.field input:focus {
  border-color: #4e8ef7;
  box-shadow: 0 0 0 3px rgba(78, 142, 247, 0.1);
  background: white;
}

.field input::placeholder {
  color: #cbd5e1;
}

.btn {
  width: 100%;
  height: 44px;
  border-radius: 8px;
  border: none;
  background: #1a3a6b;
  color: #e8f0fe;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 8px;
  transition: background 0.2s;
}

.btn:hover:not(:disabled) {
  background: #24518f;
}

.btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

.footer-note {
  margin-top: 24px;
  text-align: center;
  font-size: 12px;
  color: #94a3b8;
}

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .wrap {
    grid-template-columns: 1fr;
  }

  .left {
    min-height: 240px;
  }

  .right {
    padding: 40px 24px;
  }
}
</style>