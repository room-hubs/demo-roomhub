import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  srcDir: 'app/',

  css: ['~/assets/css/main.css'],
  
  vite: {
    plugins: [
      tailwindcss(),
    ],
  },

  devServer: {
    port: 3001  
  },

  runtimeConfig: {
    public: {
      apiBase:                   process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
      firebaseApiKey:            process.env.NUXT_PUBLIC_FIREBASE_API_KEY,
      firebaseAuthDomain:        process.env.NUXT_PUBLIC_FIREBASE_AUTH_DOMAIN,
      firebaseProjectId:         process.env.NUXT_PUBLIC_FIREBASE_PROJECT_ID,
      firebaseStorageBucket:     process.env.NUXT_PUBLIC_FIREBASE_STORAGE_BUCKET,
      firebaseMessagingSenderId: process.env.NUXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID,
      firebaseAppId:             process.env.NUXT_PUBLIC_FIREBASE_APP_ID,
      recaptchaSiteKey:          process.env.NUXT_PUBLIC_RECAPTCHA_SITE_KEY,
    }
  },
  modules: [
    '@nuxt/ui',
    '@nuxtjs/google-fonts'
  ],
  
  googleFonts: {
    families: {
      'Public+Sans': [400, 500, 700],
      'Noto+Sans': [400, 500, 700],
      'Noto+Sans+Khmer': [400, 500, 700],
      'Noto+Sans+SC': [400, 500, 700],
    },
    display: 'swap',
    prefetch: true,
  },

})