<template>
  <div v-if="tasks.length === 0" class="text-center py-12">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma tarefa</h3>
    <p class="mt-1 text-sm text-gray-500">Comece criando uma nova tarefa.</p>
  </div>

  <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <TaskCard
      v-for="task in tasks"
      :key="task.id"
      :task="task"
      @edit="$emit('edit', task)"
      @delete="$emit('delete', task)"
    />
  </div>
</template>

<script setup>
import TaskCard from './TaskCard.vue'

defineProps({
  tasks: {
    type: Array,
    required: true
  }
})

defineEmits(['edit', 'delete'])
</script>
