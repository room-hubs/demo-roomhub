import { initializeApp, getApps } from 'firebase/app'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()

  // Initialize Firebase only once
  if (!getApps().length) {
    initializeApp({
      apiKey: config.public.firebaseApiKey,
      authDomain: config.public.firebaseAuthDomain,
      projectId: config.public.firebaseProjectId,
      storageBucket: config.public.firebaseStorageBucket,
      messagingSenderId: config.public.firebaseMessagingSenderId,
      appId: config.public.firebaseAppId,
    })
  }

  // NOTE:
  // - No reCAPTCHA needed
  // - No phone auth needed
  // - Firebase only used for OAuth (Google/Facebook if you still use them)
})