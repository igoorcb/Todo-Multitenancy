<template>
  <div class="px-6 py-4">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
      {{ task ? 'Editar Tarefa' : 'Criar Nova Tarefa' }}
    </h3>
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500"
          placeholder="Título da tarefa"
        />
      </div>

      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea
          id="description"
          v-model="form.description"
          rows="3"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500"
          placeholder="Descrição da tarefa"
        ></textarea>
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <div class="flex gap-2">
            <button
              type="button"
              @click="form.status = 'pending'"
              :class="[
                'flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-all',
                form.status === 'pending'
                  ? 'bg-primary-600 text-white shadow-sm'
                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
              ]"
            >
              Pendente
            </button>
            <button
              type="button"
              @click="form.status = 'in_progress'"
              :class="[
                'flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-all',
                form.status === 'in_progress'
                  ? 'bg-primary-600 text-white shadow-sm'
                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
              ]"
            >
              Em Progresso
            </button>
            <button
              type="button"
              @click="form.status = 'completed'"
              :class="[
                'flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-all',
                form.status === 'completed'
                  ? 'bg-primary-600 text-white shadow-sm'
                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
              ]"
            >
              Concluída
            </button>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Prioridade</label>
          <div class="flex gap-2">
            <button
              type="button"
              @click="form.priority = 'low'"
              :class="[
                'flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-all',
                form.priority === 'low'
                  ? 'bg-primary-600 text-white shadow-sm'
                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
              ]"
            >
              Baixa
            </button>
            <button
              type="button"
              @click="form.priority = 'medium'"
              :class="[
                'flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-all',
                form.priority === 'medium'
                  ? 'bg-primary-600 text-white shadow-sm'
                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
              ]"
            >
              Média
            </button>
            <button
              type="button"
              @click="form.priority = 'high'"
              :class="[
                'flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-all',
                form.priority === 'high'
                  ? 'bg-primary-600 text-white shadow-sm'
                  : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
              ]"
            >
              Alta
            </button>
          </div>
        </div>
      </div>

      <div>
        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento</label>
        <input
          id="due_date"
          v-model="form.due_date"
          type="date"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        />
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <button
          type="button"
          @click="$emit('cancel')"
          class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
          Cancelar
        </button>
        <button
          type="submit"
          class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
        >
          {{ task ? 'Atualizar' : 'Criar' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  task: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['save', 'cancel'])

const form = ref({
  title: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  due_date: ''
})

watch(() => props.task, (newTask) => {
  if (newTask) {
    form.value = {
      title: newTask.title || '',
      description: newTask.description || '',
      status: newTask.status || 'pending',
      priority: newTask.priority || 'medium',
      due_date: newTask.due_date || ''
    }
  } else {
    form.value = {
      title: '',
      description: '',
      status: 'pending',
      priority: 'medium',
      due_date: ''
    }
  }
}, { immediate: true })

const handleSubmit = () => {
  emit('save', form.value)
}
</script>
