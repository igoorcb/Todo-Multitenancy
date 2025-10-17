import { ref } from 'vue'

const toasts = ref([])
let nextId = 0

export function useToast() {
  const show = (message, type = 'info', duration = 3000) => {
    const id = nextId++
    toasts.value.push({ id, message, type })

    if (duration > 0) {
      setTimeout(() => {
        remove(id)
      }, duration)
    }
  }

  const success = (message, duration = 3000) => {
    show(message, 'success', duration)
  }

  const error = (message, duration = 5000) => {
    show(message, 'error', duration)
  }

  const info = (message, duration = 3000) => {
    show(message, 'info', duration)
  }

  const warning = (message, duration = 3000) => {
    show(message, 'warning', duration)
  }

  const remove = (id) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  return {
    toasts,
    success,
    error,
    info,
    warning,
    remove
  }
}
