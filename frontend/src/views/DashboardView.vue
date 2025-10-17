<template>
  <div class="min-h-screen bg-gray-50">
    <AppHeader />

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
          <h1 class="text-3xl font-bold text-gray-900">Minhas Tarefas</h1>
          <div class="flex gap-3">
            <button
              @click="handleExport"
              :disabled="exporting"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              {{ exporting ? 'Exportando...' : 'Exportar' }}
            </button>
            <button
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nova Tarefa
            </button>
          </div>
        </div>

        <TaskFilters @filter="handleFilter" @clear="handleClearFilters" />

        <LoadingSpinner v-if="tasksStore.loading && !tasks.length" />
        <TaskList v-else :tasks="tasks" @edit="openEditModal" @delete="openDeleteModal" />

        <div v-if="pagination.last_page > 1" class="mt-6 flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Mostrando {{ (pagination.current_page - 1) * pagination.per_page + 1 }} até
            {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} de
            {{ pagination.total }} resultados
          </div>
          <div class="flex gap-2">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Anterior
            </button>
            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Próximo
            </button>
          </div>
        </div>
      </div>
    </main>

    <TransitionRoot :show="showTaskModal" as="template">
      <Dialog as="div" class="relative z-10" @close="closeTaskModal">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in duration-200"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>

        <div class="fixed inset-0 z-10 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              enter-to="opacity-100 translate-y-0 sm:scale-100"
              leave="ease-in duration-200"
              leave-from="opacity-100 translate-y-0 sm:scale-100"
              leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
              <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <TaskForm
                  :task="selectedTask"
                  @save="handleSaveTask"
                  @cancel="closeTaskModal"
                />
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <ConfirmDialog
      :open="showDeleteModal"
      title="Excluir Tarefa"
      message="Tem certeza que deseja excluir esta tarefa? Esta ação não pode ser desfeita."
      @confirm="handleDeleteTask"
      @cancel="closeDeleteModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from '@headlessui/vue'
import { useTasksStore } from '../stores/tasks'
import { useAuthStore } from '../stores/auth'
import { useToast } from '../composables/useToast'
import exportService from '../services/export.service'
import AppHeader from '../components/AppHeader.vue'
import TaskList from '../components/TaskList.vue'
import TaskFilters from '../components/TaskFilters.vue'
import TaskForm from '../components/TaskForm.vue'
import ConfirmDialog from '../components/ConfirmDialog.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'

const tasksStore = useTasksStore()
const authStore = useAuthStore()
const toast = useToast()

const showTaskModal = ref(false)
const showDeleteModal = ref(false)
const selectedTask = ref(null)
const taskToDelete = ref(null)
const exporting = ref(false)

const tasks = computed(() => tasksStore.tasks)
const pagination = computed(() => tasksStore.pagination)

onMounted(async () => {
  if (!authStore.user) {
    await authStore.fetchUser()
  }
  await tasksStore.fetchTasks()
})

const openCreateModal = () => {
  selectedTask.value = null
  showTaskModal.value = true
}

const openEditModal = (task) => {
  selectedTask.value = { ...task }
  showTaskModal.value = true
}

const closeTaskModal = () => {
  showTaskModal.value = false
  selectedTask.value = null
}

const openDeleteModal = (task) => {
  taskToDelete.value = task
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  taskToDelete.value = null
}

const handleSaveTask = async (taskData) => {
  try {
    if (selectedTask.value?.id) {
      await tasksStore.updateTask(selectedTask.value.id, taskData)
      toast.success('Tarefa atualizada com sucesso!')
    } else {
      await tasksStore.createTask(taskData)
      toast.success('Tarefa criada com sucesso!')
    }
    closeTaskModal()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Falha ao salvar tarefa')
  }
}

const handleDeleteTask = async () => {
  try {
    await tasksStore.deleteTask(taskToDelete.value.id)
    toast.success('Tarefa excluída com sucesso!')
    closeDeleteModal()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Falha ao excluir tarefa')
  }
}

const handleFilter = (filters) => {
  tasksStore.setFilters(filters)
  tasksStore.fetchTasks(1)
}

const handleClearFilters = () => {
  tasksStore.clearFilters()
  tasksStore.fetchTasks(1)
}

const changePage = (page) => {
  tasksStore.fetchTasks(page)
}

const handleExport = async () => {
  exporting.value = true
  try {
    const response = await exportService.exportTasks(tasksStore.filters)
    toast.success('Exportação iniciada! Verificando status...')

    const checkInterval = setInterval(async () => {
      try {
        const status = await exportService.checkStatus(response.filename)
        if (status.status === 'completed') {
          clearInterval(checkInterval)
          await exportService.download(response.filename)
          toast.success('Tarefas exportadas com sucesso!')
          exporting.value = false
        } else if (status.status === 'failed') {
          clearInterval(checkInterval)
          toast.error('Falha na exportação. Por favor, tente novamente.')
          exporting.value = false
        }
      } catch (error) {
        clearInterval(checkInterval)
        toast.error('Falha ao verificar status da exportação')
        exporting.value = false
      }
    }, 2000)
  } catch (error) {
    toast.error(error.response?.data?.message || 'Falha ao exportar tarefas')
    exporting.value = false
  }
}
</script>
