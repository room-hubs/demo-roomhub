<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="open"
        class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-[2px]"
        @click="$emit('close')"
      >
        <Transition
          enter-active-class="transform transition duration-300 ease-out"
          enter-from-class="translate-x-full"
          enter-to-class="translate-x-0"
          leave-active-class="transform transition duration-250 ease-in"
          leave-from-class="translate-x-0"
          leave-to-class="translate-x-full"
        >
          <aside
            v-if="open"
            class="fixed right-0 top-0 z-[101] flex h-full w-[85vw] max-w-sm flex-col rounded-l-2xl bg-white shadow-2xl sm:w-96"
            @click.stop
          >
            <!-- Header -->
            <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-4">
              <h2 class="text-lg font-bold text-gray-900">
                Menu
              </h2>

              <button
                @click="$emit('close')"
                class="rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>

            <!-- Content (scrollable) -->
            <div class="flex-1 overflow-y-auto px-5 py-5" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom))">

              <!-- Host -->
              <NuxtLink
                to="/host"
                class="mb-6 flex items-center justify-center rounded-xl bg-green-700 py-3.5 text-center text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 active:scale-[0.98]"
                @click="$emit('close')"
              >
                ដាក់បន្ទប់ជួល
              </NuxtLink>

              <!-- Languages -->
              <p class="mb-2 px-2 text-xs font-semibold tracking-wide text-gray-400 uppercase">
                Language
              </p>

              <div class="mb-6 overflow-hidden rounded-xl border border-gray-100">
                <button
                  v-for="(lang, index) in languages"
                  :key="lang.code"
                  @click="changeLanguage(lang)"
                  class="flex w-full items-center gap-3 px-3 py-3 text-left transition hover:bg-gray-50 active:bg-gray-100"
                  :class="{ 'border-t border-gray-100': index > 0 }"
                >
                  <img
                    :src="lang.flag"
                    class="h-5 w-7 shrink-0 rounded object-cover ring-1 ring-black/5"
                  />

                  <span class="flex-1 text-sm font-medium text-gray-700">
                    {{ lang.name }}
                  </span>

                  <CheckIcon
                    v-if="selected === lang.code"
                    class="h-5 w-5 shrink-0 text-green-600"
                  />
                </button>
              </div>

              <!-- Account -->
              <div class="overflow-hidden rounded-xl border border-gray-100">
                <NuxtLink
                  to="/login"
                  class="flex items-center gap-3 px-3 py-3 transition hover:bg-gray-50 active:bg-gray-100"
                  @click="$emit('close')"
                >
                  <UserCircleIcon class="h-5 w-5 shrink-0 text-gray-500" />
                  <span class="text-sm font-medium text-gray-700">ចូលគណនី</span>
                </NuxtLink>

                <NuxtLink
                  to="/register"
                  class="flex items-center gap-3 border-t border-gray-100 px-3 py-3 transition hover:bg-gray-50 active:bg-gray-100"
                  @click="$emit('close')"
                >
                  <UserPlusIcon class="h-5 w-5 shrink-0 text-gray-500" />
                  <span class="text-sm font-medium text-gray-700">បង្កើតគណនី</span>
                </NuxtLink>
              </div>

            </div>
          </aside>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'

import {
  XMarkIcon,
  UserCircleIcon,
  UserPlusIcon,
  CheckIcon
} from '@heroicons/vue/24/outline'

defineProps({
  open: Boolean
})

defineEmits(['close'])

const languages = [
  {
    code: 'kh',
    name: 'ភាសាខ្មែរ',
    flag: '/flags/kh.png'
  },
  {
    code: 'en',
    name: 'English',
    flag: '/flags/us.png'
  },
  {
    code: 'zh',
    name: '中文',
    flag: '/flags/cn.png'
  }
]

const selected = ref('kh')

function changeLanguage(lang) {
  selected.value = lang.code

  // Future Nuxt i18n
  // const { locale } = useI18n()
  // locale.value = lang.code
}
</script>