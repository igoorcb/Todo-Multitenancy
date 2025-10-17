<template>
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <h1 class="text-xl font-bold text-gray-900">Gerenciador de Tarefas</h1>
          <div class="hidden sm:block border-l border-gray-300 pl-4">
            <p class="text-sm text-gray-600">{{ authStore.tenant?.name || 'Empresa' }}</p>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <div class="text-right">
            <p class="text-sm font-medium text-gray-900">{{ authStore.user?.name }}</p>
            <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
          </div>
          <button
            @click="handleLogout"
            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Sair
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useToast } from '../composables/useToast'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const handleLogout = () => {
  authStore.logout()
  toast.success('Logout realizado com sucesso')
  router.push('/login')
}
</script>
