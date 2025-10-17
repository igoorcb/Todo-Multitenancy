import api from './api'

export default {
  async getTasks(filters = {}) {
    const params = new URLSearchParams()

    if (filters.status) params.append('status', filters.status)
    if (filters.priority) params.append('priority', filters.priority)
    if (filters.search) params.append('search', filters.search)
    if (filters.page) params.append('page', filters.page)

    const response = await api.get(`/api/tasks?${params.toString()}`)
    return response.data
  },

  async getTask(id) {
    const response = await api.get(`/api/tasks/${id}`)
    return response.data
  },

  async createTask(data) {
    const response = await api.post('/api/tasks', data)
    return response.data
  },

  async updateTask(id, data) {
    const response = await api.put(`/api/tasks/${id}`, data)
    return response.data
  },

  async deleteTask(id) {
    const response = await api.delete(`/api/tasks/${id}`)
    return response.data
  }
}
