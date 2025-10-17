<template>
  <div class="fixed top-6 right-6 z-50 space-y-3 max-w-md w-full">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="toastClasses(toast.type)"
        class="w-full shadow-xl rounded-xl pointer-events-auto overflow-hidden backdrop-blur-sm"
      >
        <div class="p-5">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <svg v-if="toast.type === 'success'" class="h-7 w-7 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else-if="toast.type === 'error'" class="h-7 w-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else-if="toast.type === 'warning'" class="h-7 w-7 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <svg v-else class="h-7 w-7 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-4 flex-1">
              <p class="text-base font-semibold" :class="toastTextClass(toast.type)">
                {{ toast.message }}
              </p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
              <button
                @click="remove(toast.id)"
                :class="toastButtonClass(toast.type)"
                class="rounded-md inline-flex hover:opacity-70 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-opacity"
              >
                <span class="sr-only">Fechar</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { TransitionGroup } from 'vue'
import { useToast } from '../composables/useToast'

const { toasts, remove } = useToast()

const toastClasses = (type) => {
  const classes = {
    success: 'bg-green-50 border-l-4 border-green-500 ring-1 ring-green-200',
    error: 'bg-red-50 border-l-4 border-red-500 ring-1 ring-red-200',
    warning: 'bg-yellow-50 border-l-4 border-yellow-500 ring-1 ring-yellow-200',
    info: 'bg-blue-50 border-l-4 border-blue-500 ring-1 ring-blue-200'
  }
  return classes[type] || classes.info
}

const toastTextClass = (type) => {
  const classes = {
    success: 'text-green-900',
    error: 'text-red-900',
    warning: 'text-yellow-900',
    info: 'text-blue-900'
  }
  return classes[type] || classes.info
}

const toastButtonClass = (type) => {
  const classes = {
    success: 'text-green-600 focus:ring-green-500',
    error: 'text-red-600 focus:ring-red-500',
    warning: 'text-yellow-600 focus:ring-yellow-500',
    info: 'text-blue-600 focus:ring-blue-500'
  }
  return classes[type] || classes.info
}
</script>

<style scoped>
.toast-enter-active {
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toast-leave-active {
  transition: all 0.3s ease-in;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(120%) scale(0.9);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(120%) scale(0.8);
}

.toast-move {
  transition: all 0.3s ease;
}
</style>
