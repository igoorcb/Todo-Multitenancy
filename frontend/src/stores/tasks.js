import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import taskService from '../services/task.service'

export const useTasksStore = defineStore('tasks', () => {
  const tasks = ref([])
  const loading = ref(false)
  const filters = ref({
    status: '',
    priority: '',
    search: ''
  })
  const pagination = ref({
    current_page: 1,
    per_page: 10,
    total: 0,
    last_page: 1
  })

  const filteredTasks = computed(() => {
    return tasks.value
  })

  async function fetchTasks(page = 1) {
    loading.value = true
    try {
      const response = await taskService.getTasks({
        ...filters.value,
        page
      })
      tasks.value = response.data
      pagination.value = {
        current_page: response.current_page,
        per_page: response.per_page,
        total: response.total,
        last_page: response.last_page
      }
    } finally {
      loading.value = false
    }
  }

  async function createTask(data) {
    loading.value = true
    try {
      const response = await taskService.createTask(data)
      await fetchTasks(pagination.value.current_page)
      return response
    } finally {
      loading.value = false
    }
  }

  async function updateTask(id, data) {
    loading.value = true
    try {
      const response = await taskService.updateTask(id, data)
      await fetchTasks(pagination.value.current_page)
      return response
    } finally {
      loading.value = false
    }
  }

  async function deleteTask(id) {
    loading.value = true
    try {
      await taskService.deleteTask(id)
      await fetchTasks(pagination.value.current_page)
    } finally {
      loading.value = false
    }
  }

  function setFilters(newFilters) {
    filters.value = { ...filters.value, ...newFilters }
  }

  function clearFilters() {
    filters.value = {
      status: '',
      priority: '',
      search: ''
    }
  }

  return {
    tasks,
    loading,
    filters,
    pagination,
    filteredTasks,
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
    setFilters,
    clearFilters
  }
})
