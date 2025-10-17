<template>
  <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200 p-6">
    <div class="flex justify-between items-start mb-3">
      <h3 class="text-lg font-semibold text-gray-900 flex-1">{{ task.title }}</h3>
      <div class="flex gap-2 ml-2">
        <button
          @click="$emit('edit', task)"
          class="text-gray-400 hover:text-primary-600 transition-colors"
          title="Editar"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </button>
        <button
          @click="$emit('delete', task)"
          class="text-gray-400 hover:text-red-600 transition-colors"
          title="Excluir"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>

    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ task.description }}</p>

    <div class="flex flex-wrap gap-2 mb-4">
      <span :class="statusClasses">{{ statusLabels[task.status] }}</span>
      <span :class="priorityClasses">{{ priorityLabels[task.priority] }}</span>
    </div>

    <div v-if="task.due_date" class="flex items-center text-sm text-gray-500">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      {{ formatDate(task.due_date) }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
})

defineEmits(['edit', 'delete'])

const statusLabels = {
  pending: 'Pendente',
  in_progress: 'Em Andamento',
  completed: 'Concluída'
}

const priorityLabels = {
  low: 'Baixa',
  medium: 'Média',
  high: 'Alta'
}

const statusClasses = computed(() => {
  const baseClasses = 'px-2 py-1 text-xs font-medium rounded-full'
  const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800'
  }
  return `${baseClasses} ${statusColors[props.task.status]}`
})

const priorityClasses = computed(() => {
  const baseClasses = 'px-2 py-1 text-xs font-medium rounded-full'
  const priorityColors = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-orange-100 text-orange-800',
    high: 'bg-red-100 text-red-800'
  }
  return `${baseClasses} ${priorityColors[props.task.priority]}`
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>
