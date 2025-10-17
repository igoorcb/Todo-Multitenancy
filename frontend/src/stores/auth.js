import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authService from '../services/auth.service'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const tenant = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(email, password) {
    const response = await authService.login(email, password)
    token.value = response.token
    user.value = response.user
    tenant.value = response.user.tenant
    localStorage.setItem('token', response.token)
  }

  async function register(data) {
    const response = await authService.register(data)
    token.value = response.token
    user.value = response.user
    tenant.value = response.user.tenant
    localStorage.setItem('token', response.token)
  }

  async function fetchUser() {
    try {
      const response = await authService.me()
      user.value = response
      tenant.value = response.tenant
    } catch (error) {
      logout()
      throw error
    }
  }

  function logout() {
    user.value = null
    token.value = null
    tenant.value = null
    localStorage.removeItem('token')
  }

  return {
    user,
    token,
    tenant,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser
  }
})
