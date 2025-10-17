<template>
  <div class="bg-white rounded-lg shadow p-4 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select
          id="status-filter"
          v-model="filters.status"
          @change="applyFilters"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        >
          <option value="">Todos</option>
          <option value="pending">Pendente</option>
          <option value="in_progress">Em Andamento</option>
          <option value="completed">Concluída</option>
        </select>
      </div>

      <div>
        <label for="priority-filter" class="block text-sm font-medium text-gray-700 mb-1">Prioridade</label>
        <select
          id="priority-filter"
          v-model="filters.priority"
          @change="applyFilters"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        >
          <option value="">Todos</option>
          <option value="low">Baixa</option>
          <option value="medium">Média</option>
          <option value="high">Alta</option>
        </select>
      </div>

      <div>
        <label for="search-filter" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
        <input
          id="search-filter"
          v-model="filters.search"
          @input="debouncedSearch"
          type="text"
          placeholder="Buscar tarefas..."
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500"
        />
      </div>
    </div>

    <div class="mt-4 flex justify-end">
      <button
        @click="clearFilters"
        class="text-sm text-primary-600 hover:text-primary-700 font-medium"
      >
        Limpar filtros
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['filter', 'clear'])

const filters = ref({
  status: '',
  priority: '',
  search: ''
})

let debounceTimeout = null

const applyFilters = () => {
  emit('filter', { ...filters.value })
}

const debouncedSearch = () => {
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}

const clearFilters = () => {
  filters.value = {
    status: '',
    priority: '',
    search: ''
  }
  emit('clear')
}
</script>
