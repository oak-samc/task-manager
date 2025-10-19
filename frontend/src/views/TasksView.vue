<template>
  <div class="tasks-view">
    <div class="header-section">
      <h1>{{ pageTitle }}</h1>
      <button @click="showCreateForm = true" class="btn btn-primary">
      + Nova Tarefa
    </button>
    </div>

    <!-- Create Task Form -->
    <div v-if="showCreateForm" class="card form-card">
      <h2>Criar Nova Tarefa</h2>
      <form @submit.prevent="createTask">
        <div class="form-group">
          <label class="form-label">Título</label>
          <input
            v-model="newTask.title"
            type="text"
            class="form-input"
            placeholder="Digite o título da tarefa"
            required
          />
        </div>
        <div class="form-group">
          <label class="form-label">Descrição</label>
          <textarea
            v-model="newTask.description"
            class="form-textarea"
            placeholder="Digite a descrição da tarefa (opcional)"
            rows="3"
          ></textarea>
        </div>
        <div class="form-group" v-if="!projectId">
          <label class="form-label">Projeto</label>
          <select v-model="newTask.project_id" class="form-select" required>
            <option value="">Selecione um projeto</option>
            <option v-for="project in projects" :key="project.id" :value="project.id">
              {{ project.name }}
            </option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select v-model="newTask.status" class="form-select">
            <option value="pending">Pendente</option>
            <option value="in_progress">Em Andamento</option>
            <option value="completed">Concluído</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Criando...' : 'Criar Tarefa' }}
          </button>
          <button type="button" @click="cancelCreate" class="btn btn-secondary">
            Cancelar
          </button>
        </div>
      </form>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board">
      <div class="kanban-column" v-for="status in statuses" :key="status.value">
        <div class="column-header">
          <h3>{{ status.label }}</h3>
          <span class="task-count">{{ getTasksByStatus(status.value).length }}</span>
        </div>
        <div 
          class="column-content"
          @drop="onDrop($event, status.value)"
          @dragover.prevent
          @dragenter.prevent
        >
          <div
            v-for="task in getTasksByStatus(status.value)"
            :key="task.id"
            class="task-card"
            draggable="true"
            @dragstart="onDragStart($event, task)"
          >
            <div class="task-header">
              <h4>{{ task.title }}</h4>
              <div class="task-actions">
                <button @click="editTask(task)" class="btn-icon" title="Editar">
                  ✏️
                </button>
                <button @click="deleteTask(task.id)" class="btn-icon" title="Excluir">
                  🗑️
                </button>
              </div>
            </div>
            <p v-if="task.description" class="task-description">
              {{ task.description }}
            </p>
            <div class="task-meta">
              <span class="project-name" v-if="task.project">{{ task.project.name }}</span>
              <span class="task-date">{{ formatDate(task.created_at) }}</span>
            </div>
          </div>
          <div v-if="getTasksByStatus(status.value).length === 0" class="empty-column">
            No tasks
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Task Modal -->
    <div v-if="editingTask" class="modal-overlay" @click="cancelEdit">
      <div class="modal-content" @click.stop>
        <h2>Editar Tarefa</h2>
        <form @submit.prevent="updateTask">
          <div class="form-group">
            <label class="form-label">Título</label>
            <input
              v-model="editingTask.title"
              type="text"
              class="form-input"
              required
            />
          </div>
          <div class="form-group">
            <label class="form-label">Descrição</label>
            <textarea
              v-model="editingTask.description"
              class="form-textarea"
              rows="3"
            ></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select v-model="editingTask.status" class="form-select">
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Andamento</option>
              <option value="completed">Concluído</option>
            </select>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Atualizando...' : 'Atualizar Tarefa' }}
            </button>
            <button type="button" @click="cancelEdit" class="btn btn-secondary">
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && tasks.length === 0" class="loading">
      Carregando tarefas...
    </div>

    <!-- Error State -->
    <div v-if="error" class="error">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { tasksApi, projectsApi } from '@/services/api'

const route = useRoute()
const projectId = computed(() => route.params.projectId)

const tasks = ref([])
const projects = ref([])
const loading = ref(false)
const error = ref('')
const showCreateForm = ref(false)
const editingTask = ref(null)

const newTask = ref({
  title: '',
  description: '',
  status: 'pending',
  project_id: projectId.value || ''
})

const statuses = [
  { value: 'pending', label: 'Pendente' },
  { value: 'in_progress', label: 'Em Andamento' },
  { value: 'completed', label: 'Concluído' }
]

const pageTitle = computed(() => {
  if (projectId.value) {
    const project = projects.value.find(p => p.id == projectId.value)
    return project ? `${project.name} - Tarefas` : 'Tarefas do Projeto'
  }
  return 'Todas as Tarefas'
})

const getTasksByStatus = (status) => {
  return tasks.value.filter(task => task.status === status)
}

const loadTasks = async () => {
  loading.value = true
  error.value = ''
  try {
    let response
    if (projectId.value) {
      response = await projectsApi.getTasks(projectId.value)
    } else {
      response = await tasksApi.getAll()
    }
    tasks.value = response.data
  } catch (err) {
    error.value = 'Failed to load tasks. Please try again.'
    console.error('Error loading tasks:', err)
  } finally {
    loading.value = false
  }
}

const loadProjects = async () => {
  try {
    const response = await projectsApi.getAll()
    projects.value = response.data
  } catch (err) {
    console.error('Error loading projects:', err)
  }
}

const createTask = async () => {
  if (!newTask.value.title.trim()) return
  
  loading.value = true
  error.value = ''
  try {
    const taskData = { ...newTask.value }
    if (projectId.value) {
      taskData.project_id = projectId.value
    }
    
    const response = await tasksApi.create(taskData)
    tasks.value.push(response.data)
    resetNewTask()
    showCreateForm.value = false
  } catch (err) {
    error.value = 'Failed to create task. Please try again.'
    console.error('Error creating task:', err)
  } finally {
    loading.value = false
  }
}

const editTask = (task) => {
  editingTask.value = { ...task }
}

const updateTask = async () => {
  if (!editingTask.value) return
  
  loading.value = true
  error.value = ''
  try {
    const response = await tasksApi.update(editingTask.value.id, editingTask.value)
    const index = tasks.value.findIndex(t => t.id === editingTask.value.id)
    if (index !== -1) {
      tasks.value[index] = response.data
    }
    editingTask.value = null
  } catch (err) {
    error.value = 'Failed to update task. Please try again.'
    console.error('Error updating task:', err)
  } finally {
    loading.value = false
  }
}

const deleteTask = async (taskId) => {
  if (!confirm('Are you sure you want to delete this task?')) return
  
  try {
    await tasksApi.delete(taskId)
    tasks.value = tasks.value.filter(t => t.id !== taskId)
  } catch (err) {
    error.value = 'Failed to delete task. Please try again.'
    console.error('Error deleting task:', err)
  }
}

const onDragStart = (event, task) => {
  event.dataTransfer.setData('text/plain', JSON.stringify(task))
}

const onDrop = async (event, newStatus) => {
  event.preventDefault()
  const taskData = JSON.parse(event.dataTransfer.getData('text/plain'))
  
  if (taskData.status === newStatus) return
  
  try {
    const updatedTask = { ...taskData, status: newStatus }
    const response = await tasksApi.update(taskData.id, updatedTask)
    const index = tasks.value.findIndex(t => t.id === taskData.id)
    if (index !== -1) {
      tasks.value[index] = response.data
    }
  } catch (err) {
    error.value = 'Failed to update task status. Please try again.'
    console.error('Error updating task status:', err)
  }
}

const cancelCreate = () => {
  showCreateForm.value = false
  resetNewTask()
}

const cancelEdit = () => {
  editingTask.value = null
}

const resetNewTask = () => {
  newTask.value = {
    title: '',
    description: '',
    status: 'pending',
    project_id: projectId.value || ''
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(async () => {
  await Promise.all([loadTasks(), loadProjects()])
})
</script>

<style scoped>
.tasks-view {
  max-width: 1400px;
  margin: 0 auto;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header-section h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
}

.form-card {
  margin-bottom: 2rem;
}

.form-card h2 {
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.form-actions {
  display: flex;
  gap: 1rem;
}

.kanban-board {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  min-height: 600px;
}

.kanban-column {
  background: #f8fafc;
  border-radius: 0.5rem;
  padding: 1rem;
}

.column-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e2e8f0;
}

.column-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #374151;
}

.task-count {
  background: #6b7280;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 1rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.column-content {
  min-height: 500px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.task-card {
  background: white;
  border-radius: 0.375rem;
  padding: 1rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  border: 1px solid #e5e7eb;
  cursor: grab;
  transition: transform 0.2s, box-shadow 0.2s;
}

.task-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.task-card:active {
  cursor: grabbing;
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.5rem;
}

.task-header h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  flex: 1;
}

.task-actions {
  display: flex;
  gap: 0.25rem;
}

.btn-icon {
  background: none;
  border: none;
  padding: 0.25rem;
  cursor: pointer;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.btn-icon:hover {
  background: #f3f4f6;
}

.task-description {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 0.75rem;
  line-height: 1.4;
}

.task-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.75rem;
  color: #9ca3af;
}

.project-name {
  background: #eff6ff;
  color: #2563eb;
  padding: 0.125rem 0.375rem;
  border-radius: 0.25rem;
  font-weight: 500;
}

.empty-column {
  text-align: center;
  color: #9ca3af;
  font-style: italic;
  padding: 2rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 0.5rem;
  padding: 2rem;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-content h2 {
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #6b7280;
}

.error {
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
  padding: 1rem;
  border-radius: 0.375rem;
  margin-bottom: 1rem;
}

@media (max-width: 1024px) {
  .kanban-board {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .kanban-board {
    grid-template-columns: 1fr;
  }
  
  .header-section {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>