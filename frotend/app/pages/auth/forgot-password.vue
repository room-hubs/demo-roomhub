<script setup>
import { reactive, ref, onUnmounted } from "vue";

definePageMeta({ layout: "auth" });

const {
  step,
  loading,
  error,
  checkPhoneAndSendOtp,
  verifyOtp,
  resendOtp,
  resetPassword,
  reset,
} = useForgotPassword();

// =========================
// FORM STATE
// =========================
const form = reactive({
  phone: "",
  otp: "",
  password: "",
  password_confirmation: "",
});

// =========================
// RESEND TIMER
// =========================
const resendCountdown = ref(0);
let resendTimer = null;

const startResendCountdown = () => {
  resendCountdown.value = 60;

  clearInterval(resendTimer);
  resendTimer = setInterval(() => {
    resendCountdown.value--;

    if (resendCountdown.value <= 0) {
      clearInterval(resendTimer);
      resendCountdown.value = 0;
    }
  }, 1000);
};

// =========================
// STEP 1: SEND OTP
// =========================
const handleSend = async () => {
  if (!form.phone?.trim()) return;

  await checkPhoneAndSendOtp(form.phone.trim());
  startResendCountdown();
};

// =========================
// STEP 2: VERIFY OTP
// =========================
const handleVerifyOtp = async () => {
  if (!form.otp) return;

  await verifyOtp(form.otp);
};

// =========================
// RESEND OTP
// =========================
const handleResendOtp = async () => {
  if (resendCountdown.value > 0) return;

  await resendOtp();
  startResendCountdown();
};

// =========================
// STEP 3: RESET PASSWORD
// =========================
const handleResetPassword = async () => {
  if (!form.password || !form.password_confirmation) return;

  await resetPassword(form.password, form.password_confirmation);
};

// =========================
// CLEANUP
// =========================
onUnmounted(() => {
  clearInterval(resendTimer);
  reset();
});
</script>

<template>
  <div class="w-full">
    <!-- ERROR -->
    <p v-if="error" class="text-sm text-red-500 text-center mb-4">
      {{ error }}
    </p>

    <!-- STEP 1 -->
    <template v-if="step === 1">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Forgot password?</h2>
        <p class="text-sm text-gray-500 mt-1">
          Enter your phone number to receive an OTP
        </p>
      </div>

      <input
        v-model="form.phone"
        type="tel"
        placeholder="012 345 678"
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
      />

      <button
        @click="handleSend"
        :disabled="loading"
        class="w-full mt-4 bg-green-600 text-white py-3 rounded-full font-medium hover:bg-green-700 transition disabled:opacity-50"
      >
        {{ loading ? "Sending..." : "Send OTP" }}
      </button>

      <div class="text-center mt-5">
        <NuxtLink to="/auth/login" class="text-sm text-green-600 hover:underline">
          ← Back to login
        </NuxtLink>
      </div>
    </template>

    <!-- STEP 2 -->
    <template v-if="step === 2">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Enter OTP</h2>
        <p class="text-sm text-gray-500 mt-1">We sent a code to your phone</p>
      </div>

      <input
        v-model="form.otp"
        type="text"
        maxlength="6"
        placeholder="------"
        class="w-full px-4 py-2.5 text-center text-2xl tracking-widest rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none"
      />

      <button
        @click="handleVerifyOtp"
        :disabled="loading"
        class="w-full mt-4 bg-green-600 text-white py-3 rounded-full font-medium hover:bg-green-700 transition disabled:opacity-50"
      >
        {{ loading ? "Verifying..." : "Verify OTP" }}
      </button>

      <div class="text-center mt-4">
        <button
          @click="handleResendOtp"
          :disabled="resendCountdown > 0 || loading"
          class="text-sm text-green-600 hover:underline disabled:text-gray-400"
        >
          {{ resendCountdown > 0 ? `Resend in ${resendCountdown}s` : "Resend OTP" }}
        </button>
      </div>
    </template>

    <!-- STEP 3 -->
    <template v-if="step === 3">
      <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Set new password</h2>
        <p class="text-sm text-gray-500 mt-1">Must be at least 8 characters</p>
      </div>

      <input
        v-model="form.password"
        type="password"
        placeholder="New password"
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 mb-3 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
      />

      <input
        v-model="form.password_confirmation"
        type="password"
        placeholder="Confirm password"
        class="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none text-sm"
      />

      <button
        @click="handleResetPassword"
        :disabled="loading"
        class="w-full mt-4 bg-green-600 text-white py-3 rounded-full font-medium hover:bg-green-700 transition disabled:opacity-50"
      >
        {{ loading ? "Resetting..." : "Reset Password" }}
      </button>
    </template>

    <!-- STEP 4 -->
    <template v-if="step === 4">
      <div class="text-center py-6">
        <div
          class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center"
        >
          <span class="text-green-600 text-3xl">✓</span>
        </div>

        <h2 class="text-2xl font-bold text-gray-900">Password reset successful</h2>

        <p class="text-sm text-gray-500 mt-2 mb-6">
          You can now login with your new password
        </p>

        <NuxtLink
          to="/auth/login"
          class="inline-block bg-green-600 text-white px-6 py-3 rounded-full font-medium hover:bg-green-700 transition"
        >
          Back to login
        </NuxtLink>
      </div>
    </template>
  </div>
</template>
