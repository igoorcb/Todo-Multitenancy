import api from './api'

export default {
  async exportTasks(filters = {}) {
    const response = await api.post('/api/exports/tasks', filters)
    return response.data
  },

  async checkStatus(filename) {
    const response = await api.get(`/api/exports/tasks/${filename}/status`)
    return response.data
  },

  async download(filename) {
    const response = await api.get(`/api/exports/tasks/${filename}/download`, {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', filename)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  }
}
