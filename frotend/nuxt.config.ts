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
      apiBase: 'http://localhost:8000/api'
    }
  },
  modules: ['@nuxt/ui']

})