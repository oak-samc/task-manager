<template>
  <div class="tasks-view">
    <div class="header-section">
      <div class="project-header">
        <h1>{{ pageTitle }}</h1>

        <div v-if="projectId && currentProject" class="project-actions">
          <button
            @mousedown.stop
            @click="editProject(currentProject)"
            class="btn btn-edit"
            title="Editar projeto"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M18.5 2.49998C18.8978 2.10216 19.4374 1.87866 20 1.87866C20.5626 1.87866 21.1022 2.10216 21.5 2.49998C21.8978 2.89781 22.1213 3.43737 22.1213 3.99998C22.1213 4.56259 21.8978 5.10216 21.5 5.49998L12 15L8 16L9 12L18.5 2.49998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <button
            @mousedown.stop
            @click="deleteProject(currentProject)"
            class="btn btn-delete"
            title="Excluir projeto"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
      </div>
      <div class="header-actions">
        <button @click="showTaskListForm = true" class="btn btn-secondary btn-new-list">
          + Nova lista
        </button>

        <button @click="showCreateForm = true" class="btn btn-primary btn-new-task" :disabled="taskLists.length === 0">
          + Nova tarefa
        </button>
      </div>
    </div>

    <!-- Create Task List Form -->
    <div v-if="showTaskListForm" class="card form-card">
      <h2>Criar nova lista</h2>
      <form @submit.prevent="createTaskList">
        <div class="form-group">
          <label class="form-label">Nome da lista</label>
          <input
            v-model="newTaskList.name"
            type="text"
            class="form-input"
            placeholder="Digite o nome da lista (ex: A Fazer, Em Revisão)"
            required
          />
        </div>
        <div class="form-group" v-if="!projectId">
          <label class="form-label">Projeto</label>
          <select v-model="newTaskList.project_id" class="form-select" required>
            <option value="">Selecione um projeto</option>
            <option v-for="project in projects" :key="project.id" :value="project.id">
              {{ project.name }}
            </option>
          </select>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Criando...' : 'Criar lista' }}
          </button>
          <button type="button" @click="showTaskListForm = false" class="btn btn-secondary">
            Cancelar
          </button>
        </div>
      </form>
    </div>

    <!-- Create Task Form -->
    <div v-if="showCreateForm" class="card form-card">
      <h2>Criar Nova tarefa</h2>
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
          <label class="form-label">Lista</label>
          <select v-model="newTask.task_list_id" class="form-select" :disabled="taskLists.length === 0" required>
            <option value="" disabled hidden>Selecione uma lista</option>
            <option v-for="list in taskLists" :key="list.id" :value="list.id">
              {{ list.name }}
            </option>
          </select>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading || taskLists.length === 0">
            {{ loading ? 'Criando...' : 'Criar tarefa' }}
          </button>
          <button type="button" @click="cancelCreate" class="btn btn-secondary">
            Cancelar
          </button>
        </div>
      </form>
    </div>


    <div v-if="editingProject" class="card form-card">
      <h2>Editar Projeto</h2>
      <form @submit.prevent="updateProject">
        <div class="form-group">
          <label class="form-label">Nome do projeto</label>
          <input
            v-model="editingProject.name"
            type="text"
            class="form-input"
            placeholder="Digite o nome do projeto"
            required
          />
        </div>
        <div class="form-group">
          <label class="form-label">Descrição</label>
          <textarea
            v-model="editingProject.description"
            class="form-textarea"
            placeholder="Digite a descrição do projeto (opcional)"
            rows="3"
          ></textarea>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Salvando...' : 'Salvar alterações' }}
          </button>
          <button type="button" @click="cancelEditProject" class="btn btn-secondary">
            Cancelar
          </button>
        </div>
      </form>
    </div>


    <div class="kanban-container">
      <div class="scroll-indicator scroll-left" v-if="canScrollLeft && columns.length > 0" @click="scrollLeft">
        ◀
      </div>
      <div v-if="columns.length === 0" class="empty-board">
        <span class="empty-create-list" @click="showTaskListForm = true">+ Criar lista</span>
      </div>
      <div v-else class="kanban-board" :class="{ 'drag-active': draggedColumn }" ref="kanbanBoard" @scroll="checkScroll">
      <div
        class="kanban-column"
        v-for="column in columns"
        :key="column.id"
        :class="{
          'column-dragging': draggedColumn && draggedColumn.id === column.id,
          'drag-over-left': dragOverColumn === `${column.id}-left`,
          'drag-over-right': dragOverColumn === `${column.id}-right`,
          'drag-over-center': dragOverColumn === `${column.id}-center`
        }"
        @dragover="onColumnDragOver($event, column)"
        @dragleave="onColumnDragLeave($event, column)"
        @drop="onColumnDrop($event, column)"
      >
        <div
          class="column-header"
          draggable="true"
          @dragstart="onColumnDragStart($event, column)"
          @dragend="onColumnDragEnd($event)"
        >
          <div class="drag-handle" title="Arrastar para reordenar">⋮⋮</div>
          <h3>{{ column.label }}</h3>
          <div class="column-actions">
            <span class="task-count">{{ getTasksByColumn(column.id).length }}</span>
            <button @mousedown.stop @click="editTaskList(column)" class="btn btn-edit" title="Editar Lista">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18.5 2.49998C18.8978 2.10216 19.4374 1.87866 20 1.87866C20.5626 1.87866 21.1022 2.10216 21.5 2.49998C21.8978 2.89781 22.1213 3.43737 22.1213 3.99998C22.1213 4.56259 21.8978 5.10216 21.5 5.49998L12 15L8 16L9 12L18.5 2.49998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <button @mousedown.stop @click="deleteTaskList(column.id)" class="btn btn-delete" title="Excluir Lista">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>
        </div>
        <div
          class="column-content"
          :class="{ 'drag-over-column': draggedTask && draggedTask.task_list_id !== column.id }"
          @drop="onDrop($event, column.id, column.type)"
          @dragover.prevent="onDragOverColumn($event, column.id)"
          @dragenter.prevent="onDragEnterColumn($event, column.id)"
          @dragleave="onDragLeaveColumn($event, column.id)"
        >
          <div
            v-for="task in getTasksByColumn(column.id)"
            :key="task.id"
            class="task-card"
            :class="{
              'drag-over-before': dragOverTask === `${task.id}-before`,
              'drag-over-after': dragOverTask === `${task.id}-after`
            }"
            draggable="true"
            @dragstart="onDragStart($event, task)"
            @dragend="onTaskDragEnd($event)"
            @dragover="onDragOverTask($event, task)"
            @dragleave="onDragLeaveTask($event, task)"
            @drop="onDropOnTask($event, task)"
          >
            <div class="task-header">
              <h4>{{ task.title }}</h4>
              <div class="task-actions">
                <button @mousedown.stop @click="editTask(task)" class="btn btn-edit" title="Editar">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.5 2.49998C18.8978 2.10216 19.4374 1.87866 20 1.87866C20.5626 1.87866 21.1022 2.10216 21.5 2.49998C21.8978 2.89781 22.1213 3.43737 22.1213 3.99998C22.1213 4.56259 21.8978 5.10216 21.5 5.49998L12 15L8 16L9 12L18.5 2.49998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
                <button @mousedown.stop @click="deleteTask(task.id)" class="btn btn-delete" title="Excluir">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
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
          <div v-if="getTasksByColumn(column.id).length === 0" class="empty-column">
            No tasks
          </div>
        </div>
      </div>
      <div class="scroll-indicator scroll-right" v-if="canScrollRight && columns.length > 0" @click="scrollRight">
        ▶
      </div>
    </div>
    </div>


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
            <label class="form-label">Lista</label>
            <select v-model="editingTask.task_list_id" class="form-select">
              <option v-for="list in taskLists" :key="list.id" :value="list.id">
                {{ list.name }}
              </option>
            </select>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Atualizando...' : 'Atualizar tarefa' }}
            </button>
            <button type="button" @click="cancelEdit" class="btn btn-secondary">
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>


    <div v-if="loading && tasks.length === 0" class="loading">
      Carregando tarefas...
    </div>


    <div v-if="error" class="error">
      {{ error }}
    </div>


    <div v-if="showDeleteConfirm" class="modal-overlay" @click="cancelDeleteTask">
      <div class="modal-content" @click.stop>
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir esta tarefa?</p>
        <div class="modal-actions">
          <button @click="cancelDeleteTask" class="btn btn-secondary">Cancelar</button>
          <button @click="confirmDeleteTask" class="btn btn-danger">Excluir</button>
        </div>
      </div>
    </div>


    <div v-if="editingTaskList" class="modal-overlay" @click="cancelEditTaskList">
      <div class="modal-content" @click.stop>
        <h2>Editar Lista</h2>
        <form @submit.prevent="updateTaskList">
          <div class="form-group">
            <label class="form-label">Nome da Lista</label>
            <input
              v-model="editingTaskList.name"
              type="text"
              class="form-input"
              required
            />
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Atualizando...' : 'Atualizar lista' }}
            </button>
            <button type="button" @click="cancelEditTaskList" class="btn btn-secondary">
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>


    <div v-if="showDeleteTaskListConfirm" class="modal-overlay" @click="cancelDeleteTaskList">
      <div class="modal-content confirm-list-delete" @click.stop>
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir esta lista?</p>
        <p><strong>Atenção:</strong> Ao excluir esta lista, todas as tarefas dela serão apagadas.</p>
        <div class="modal-actions center">
          <button @click="cancelDeleteTaskList" class="btn btn-secondary">Cancelar</button>
          <button @click="confirmDeleteTaskList" class="btn btn-danger">Excluir lista</button>
        </div>
      </div>
    </div>


    <div v-if="showDeleteProjectConfirm" class="modal-overlay" @click="cancelDeleteProject">
      <div class="modal-content confirm-project-delete" @click.stop>
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir este projeto?</p>
        <p class="modal-danger">⚠️ Todas as listas e tarefas relacionadas também serão excluídas permanentemente.</p>
        <div class="modal-actions center">
          <button @click="cancelDeleteProject" class="btn btn-secondary">Cancelar</button>
          <button @click="confirmDeleteProject" class="btn btn-danger">{{ loading ? 'Excluindo...' : 'Excluir' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRoute } from 'vue-router'
import { tasksApi, projectsApi, taskListsApi } from '@/services/api'

const route = useRoute()
const projectId = computed(() => route.params.projectId)

const tasks = ref([])
const projects = ref([])
const taskLists = ref([])
const showTaskListForm = ref(false)
const editingTaskList = ref(null)
const showDeleteTaskListConfirm = ref(false)
const taskListToDelete = ref(null)
// Gerenciar edição de projeto
const editingProject = ref(null)
// Gerenciar exclusão de projeto
const showDeleteProjectConfirm = ref(false)
const projectToDelete = ref(null)
const loading = ref(false)
const error = ref('')
const showCreateForm = ref(false)
const showDeleteConfirm = ref(false)
const taskToDelete = ref(null)
const editingTask = ref(null)
const dragOverTask = ref(null)
const draggedTask = ref(null)
const draggedColumn = ref(null)
const dragOverColumn = ref(null)
const kanbanBoard = ref(null)
const canScrollLeft = ref(false)
const canScrollRight = ref(false)

const newTask = ref({
  title: '',
  description: '',
  status: 'pending',
  task_list_id: null,
  project_id: projectId.value || ''
})

const newTaskList = ref({
  name: '',
  project_id: projectId.value || '',
  user_id: null // opcional; será definido pelo auth quando houver
})

const statuses = [
  { value: 'pending', label: 'Pendente' },
  { value: 'in_progress', label: 'Em Andamento' },
  { value: 'completed', label: 'Concluído' }
]

// Definir colunas com listas do projeto
const columns = computed(() => {
  return taskLists.value.map(list => ({
    id: list.id,
    label: list.name,
    type: 'task_list'
  }))
})

// Projeto atual (computed)
const currentProject = computed(() => {
  if (projectId.value) {
    return projects.value.find(p => p.id == projectId.value)
  }
  return null
})

const pageTitle = computed(() => {
  if (projectId.value) {
    const project = projects.value.find(p => p.id == projectId.value)
    return project ? `${project.name}` : 'Tarefas do Projeto'
  }
  return 'Todas as Tarefas'
})

const getTasksByStatus = (status) => {
  return tasks.value
    .filter(task => task.status === status)
    .sort((a, b) => (a.position || 0) - (b.position || 0))
}

const loadTasks = async () => {
  loading.value = true
  error.value = ''
  try {
    let response
    if (projectId.value) {
      response = await tasksApi.getAll({
        project_id: projectId.value,
        use_task_lists: 'true'
      })
    } else {
      response = await tasksApi.getAll({ use_task_lists: 'true' })
    }
    tasks.value = response.data
  } catch (err) {
    error.value = 'Failed to load tasks. Please try again.'
  } finally {
    loading.value = false
  }
}

const loadTaskLists = async () => {
  try {
    let response
    if (projectId.value) {
      response = await taskListsApi.getAll({ project_id: projectId.value })
    } else {
      response = await taskListsApi.getAll()
    }
    taskLists.value = response.data

    // Se posições são inválidas, normalizar
    const hasValidPositions = taskLists.value.every(tl =>
      tl.position != null && tl.position > 0
    )

    if (!hasValidPositions) {
      await normalizeTaskListPositions()
    }

  } catch (err) {
  }
}

// Normalizar posições das listas de tarefas
const normalizeTaskListPositions = async () => {
  try {
    // Dar posições sequenciais baseadas na ordem atual
    const updateData = taskLists.value.map((list, index) => ({
      id: list.id,
      position: index + 1
    }))


    // Atualizar no backend
    await taskListsApi.reorder(updateData)

    // Atualizar localmente
    taskLists.value.forEach((list, index) => {
      list.position = index + 1
    })


  } catch (error) {
  }
}

// Buscar tarefas por lista
const getTasksByList = (listId) => {
  return tasks.value
    .filter(task => task.task_list_id === listId)
    .sort((a, b) => (a.position || 0) - (b.position || 0))
}

// Buscar tarefas por coluna
const getTasksByColumn = (listId) => {
  return tasks.value
    .filter(task => task.task_list_id === listId)
    .sort((a, b) => (a.position || 0) - (b.position || 0))
}

// Criar nova lista de tarefas
const createTaskList = async () => {
  // Validar nome
  if (!newTaskList.value.name.trim()) return

  // Validar projeto (obrigatório pela API)
  const pid = projectId.value || newTaskList.value.project_id
  if (!pid) {
    error.value = 'Selecione um projeto para criar a lista.'
    return
  }

  loading.value = true
  try {
    const listData = {
      name: newTaskList.value.name.trim(),
      project_id: Number(pid)
    }

    await taskListsApi.create(listData)

    // Resetar formulário
    newTaskList.value = {
      name: '',
      project_id: projectId.value || '',
      user_id: null
    }
    showTaskListForm.value = false

    // Recarregar dados
    await loadTaskLists()
  } catch (err) {
    const serverErrors = err?.response?.data?.errors
    if (serverErrors) {
      const firstError = Object.values(serverErrors)[0]?.[0]
      error.value = firstError || 'Falha ao criar lista. Verifique os campos.'
    } else {
      error.value = 'Falha ao criar lista. Tente novamente.'
    }
  } finally {
    loading.value = false
  }
}

const loadProjects = async () => {
  try {
    const response = await projectsApi.getAll()
    projects.value = response.data
  } catch (err) {
  }
}

// Editar lista de tarefas
const editTaskList = (column) => {
  editingTaskList.value = {
    id: column.id,
    name: column.label,
    project_id: projectId.value || '',
    user_id: null
  }
}

// Atualizar lista de tarefas
const updateTaskList = async () => {
  if (!editingTaskList.value.name.trim()) return

  loading.value = true
  try {
    await taskListsApi.update(editingTaskList.value.id, {
      name: editingTaskList.value.name
    })

    editingTaskList.value = null
    await loadTaskLists()
  } catch (err) {
    error.value = 'Failed to update task list. Please try again.'
  } finally {
    loading.value = false
  }
}

// Excluir lista de tarefas
const deleteTaskList = (listId) => {
  taskListToDelete.value = listId
  showDeleteTaskListConfirm.value = true
}

const confirmDeleteTaskList = async () => {
  if (!taskListToDelete.value) return

  loading.value = true
  error.value = ''
  try {
    await taskListsApi.delete(taskListToDelete.value)
    await loadTaskLists()
    await loadTasks()
  } catch (err) {
    error.value = 'Erro ao deletar lista. Tente novamente.'
  } finally {
    taskListToDelete.value = null
    showDeleteTaskListConfirm.value = false
    loading.value = false
  }
}

const cancelDeleteTaskList = () => {
  taskListToDelete.value = null
  showDeleteTaskListConfirm.value = false
}

const cancelEditTaskList = () => {
  editingTaskList.value = null
}

// Editar projeto
const editProject = (project) => {
  editingProject.value = { ...project }
}

const updateProject = async () => {
  if (!editingProject.value.name.trim()) return

  loading.value = true
  error.value = ''
  try {
    const response = await projectsApi.update(editingProject.value.id, {
      name: editingProject.value.name,
      description: editingProject.value.description || ''
    })

    // Atualizar projeto na lista local
    const index = projects.value.findIndex(p => p.id === editingProject.value.id)
    if (index !== -1) {
      projects.value[index] = response.data
    }

    editingProject.value = null
  } catch (err) {
    error.value = 'Erro ao atualizar projeto. Tente novamente.'
  } finally {
    loading.value = false
  }
}

const cancelEditProject = () => {
  editingProject.value = null
}

// Excluir projeto
const deleteProject = (project) => {
  projectToDelete.value = project
  showDeleteProjectConfirm.value = true
}

const confirmDeleteProject = async () => {
  if (!projectToDelete.value) return

  loading.value = true
  error.value = ''

  try {
    await projectsApi.delete(projectToDelete.value.id)

    // Remover projeto da lista local
    const index = projects.value.findIndex(p => p.id === projectToDelete.value.id)
    if (index !== -1) {
      projects.value.splice(index, 1)
    }


    // Redirecionar para lista de projetos ao deletar
    if (projectId.value && projectId.value == projectToDelete.value.id) {
      // Navegar para página de projetos
      window.location.href = '/projects' // ou use router.push se preferir
    }

    cancelDeleteProject()

  } catch (err) {
    error.value = 'Erro ao deletar projeto. Verifique se não há dependências.'
  } finally {
    loading.value = false
  }
}

const cancelDeleteProject = () => {
  showDeleteProjectConfirm.value = false
  projectToDelete.value = null
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

    // Calcular posição inicial (final da lista)
    const tasksInColumn = getTasksByColumn(taskData.task_list_id)
    const maxPosition = tasksInColumn.length > 0
      ? Math.max(...tasksInColumn.map(t => t.position || 0))
      : 0
    taskData.position = maxPosition + 1

    const response = await tasksApi.create(taskData)
    tasks.value.push(response.data)
    resetNewTask()
    showCreateForm.value = false
  } catch (err) {
    error.value = 'Failed to create task. Please try again.'
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
  } finally {
    loading.value = false
  }
}

const deleteTask = (taskId) => {
  taskToDelete.value = taskId
  showDeleteConfirm.value = true
}

const confirmDeleteTask = async () => {
  if (!taskToDelete.value) return

  try {
    await tasksApi.delete(taskToDelete.value)
    tasks.value = tasks.value.filter(t => t.id !== taskToDelete.value)
    showDeleteConfirm.value = false
    taskToDelete.value = null
  } catch (err) {
    error.value = 'Falha ao excluir tarefa. Tente novamente.'
  }
}

const cancelDeleteTask = () => {
  showDeleteConfirm.value = false
  taskToDelete.value = null
}

const onDragStart = (event, task) => {
  // Verificar se há texto selecionado - solução mais simples
  const selection = window.getSelection()
  if (selection && selection.toString().trim().length > 0) {
    // Limpar seleção e permitir drag
    selection.removeAllRanges()
  }

  // Verificar se o clique foi em botões de ação
  const target = event.target
  if (target && (target.closest('.btn') || target.closest('.task-actions'))) {
    event.preventDefault()
    return false
  }

  // Adicionar dados extras para debug
  const taskDataWithDebug = {
    ...task,
    originalColumn: task.task_list_id || task.status,
    dragStartTime: Date.now()
  }

  event.dataTransfer.setData('text/plain', JSON.stringify(taskDataWithDebug))
  event.dataTransfer.effectAllowed = 'move'
  draggedTask.value = task

  // Ativar auto-scroll para tasks

  // Iniciar scroll automático se necessário
  startAutoScroll()

}

// 🔧 NOVA FUNÇÃO: Finalizar drag de task
const onTaskDragEnd = (event) => {

  // Limpar estados de drag
  draggedTask.value = null
  dragOverTask.value = null

  // Parar auto-scroll
  stopAutoScroll()

  // Restaurar cursor
  document.body.style.cursor = ''
}



const onDragOverTask = (event, task) => {
  event.preventDefault()
  event.stopPropagation()

  if (draggedTask.value && draggedTask.value.id !== task.id) {
    // Calcular se está na metade superior ou inferior da task
    const rect = event.currentTarget.getBoundingClientRect()
    const mouseY = event.clientY
    const taskCenterY = rect.top + rect.height / 2

    // Definir zona de inserção (acima ou abaixo)
    const insertPosition = mouseY < taskCenterY ? 'before' : 'after'

    dragOverTask.value = `${task.id}-${insertPosition}`
  }
}

const onDragLeaveTask = (event, task) => {
  event.preventDefault()
  event.stopPropagation()

  // Remover indicador visual quando sair da task
  if (dragOverTask.value && dragOverTask.value.startsWith(task.id.toString())) {
    dragOverTask.value = null
  }
}

const onDropOnTask = async (event, targetTask) => {
  event.preventDefault()
  event.stopPropagation()

  // Verificar se é drag de coluna - ignorar
  try {
    const rawData = event.dataTransfer.getData('text/plain')
    const dragData = JSON.parse(rawData)
    if (dragData.type === 'column') {
      return
    }
  } catch (err) {
    // Continuar com lógica normal se não conseguir parsear
  }

  if (!draggedTask.value || draggedTask.value.id === targetTask.id) {
    dragOverTask.value = null
    return
  }

  // 🔧 CORREÇÃO: Pegar a task atualizada do estado local, não do draggedTask
  const sourceTask = tasks.value.find(t => t.id === draggedTask.value.id) || draggedTask.value

  // Determinar posição de inserção baseada no dragOverTask
  const insertPosition = dragOverTask.value ?
    dragOverTask.value.split('-')[1] : 'before'

  try {
    // Verificar task_list_id em vez de status
    if (sourceTask.task_list_id === targetTask.task_list_id) {
      await handleSameColumnReorder(sourceTask, targetTask, insertPosition)
    } else {
      await handleCrossColumnMove(sourceTask, targetTask, insertPosition)
    }
  } catch (err) {
    error.value = 'Failed to reorder tasks. Please try again.'
    // Recarregar para reverter mudanças visuais em caso de erro
    await loadTasks()
  }

  dragOverTask.value = null
  draggedTask.value = null
  // Parar auto-scroll ao finalizar drag de task
  stopAutoScroll()
}

// Nova função: Reordenar dentro da mesma coluna
const handleSameColumnReorder = async (sourceTask, targetTask, insertPosition = 'before') => {

  // 🔧 CORREÇÃO: Usar task_list_id em vez de status
  const tasksInColumn = getTasksByColumn(sourceTask.task_list_id)
  const sourceIndex = tasksInColumn.findIndex(t => t.id === sourceTask.id)
  const targetIndex = tasksInColumn.findIndex(t => t.id === targetTask.id)

  if (sourceIndex === -1 || targetIndex === -1) return

  // Se arrastando para a mesma posição, não fazer nada
  if (sourceIndex === targetIndex ||
      (insertPosition === 'before' && sourceIndex === targetIndex - 1) ||
      (insertPosition === 'after' && sourceIndex === targetIndex + 1)) {
    return
  }

  let newPosition

  if (insertPosition === 'before') {
    // Inserir ANTES da target task
    if (targetIndex === 0) {
      // Inserir no início da lista
      newPosition = (targetTask.position || 1) - 1
    } else {
      // Inserir entre a task anterior e a target
      const prevTask = tasksInColumn[targetIndex - 1]
      newPosition = ((prevTask.position || 0) + (targetTask.position || 1)) / 2
    }
  } else {
    // Inserir DEPOIS da target task
    if (targetIndex === tasksInColumn.length - 1) {
      // Inserir no final da lista
      newPosition = (targetTask.position || 0) + 1
    } else {
      // Inserir entre a target e a próxima task
      const nextTask = tasksInColumn[targetIndex + 1]
      newPosition = ((targetTask.position || 0) + (nextTask.position || 1)) / 2
    }
  }

  // Atualizar posição via API
  await tasksApi.reorder([{
    id: sourceTask.id,
    position: newPosition,
    task_list_id: sourceTask.task_list_id || null
  }])

  // Atualizar UI local
  const allTasksIndex = tasks.value.findIndex(t => t.id === sourceTask.id)
  if (allTasksIndex !== -1) {
    tasks.value[allTasksIndex].position = newPosition
  }

  // 🔧 CORREÇÃO: Reordenar array local para refletir mudança (por task_list_id e position)
  tasks.value.sort((a, b) => {
    if (a.task_list_id !== b.task_list_id) {
      return (a.task_list_id || 0) - (b.task_list_id || 0)
    }
    return (a.position || 0) - (b.position || 0)
  })
}

// Nova função: Mover entre colunas
const handleCrossColumnMove = async (sourceTask, targetTask, insertPosition = 'before') => {

  // 🔧 CORREÇÃO: Usar task_list_id em vez de status
  const tasksInTargetColumn = getTasksByColumn(targetTask.task_list_id)
  const targetIndex = tasksInTargetColumn.findIndex(t => t.id === targetTask.id)

  let newPosition

  if (insertPosition === 'before') {
    // Inserir ANTES da target task
    if (targetIndex === 0) {
      newPosition = (targetTask.position || 1) - 1
    } else {
      const prevTask = tasksInTargetColumn[targetIndex - 1]
      newPosition = ((prevTask.position || 0) + (targetTask.position || 1)) / 2
    }
  } else {
    // Inserir DEPOIS da target task
    if (targetIndex === tasksInTargetColumn.length - 1) {
      newPosition = (targetTask.position || 0) + 1
    } else {
      const nextTask = tasksInTargetColumn[targetIndex + 1]
      newPosition = ((targetTask.position || 0) + (nextTask.position || 1)) / 2
    }
  }

  // 🔧 CORREÇÃO: Atualizar task_list_id e posição via API
  await tasksApi.reorder([{
    id: sourceTask.id,
    position: newPosition,
    task_list_id: targetTask.task_list_id  // Nova lista!
  }])

  // 🔧 CORREÇÃO: Atualizar task_list_id via update (para manter compatibilidade)
  const updatedTask = { ...sourceTask, task_list_id: targetTask.task_list_id, position: newPosition }
  const response = await tasksApi.update(sourceTask.id, updatedTask)

  // Atualizar UI local
  const index = tasks.value.findIndex(t => t.id === sourceTask.id)
  if (index !== -1) {
    tasks.value[index] = response.data
  }
}

const onDrop = async (event, columnId, columnType) => {
  event.preventDefault()

  let taskData
  try {
    const rawData = event.dataTransfer.getData('text/plain')
    taskData = JSON.parse(rawData)

    // Verificar se é drag de coluna - ignorar
    if (taskData.type === 'column') {
      return
    }

  } catch (err) {
    draggedTask.value = null
    // Parar auto-scroll ao finalizar drag de task
    stopAutoScroll()
    return
  }

  // Verificar se é a mesma coluna - corrigir lógica
  const currentColumn = taskData.task_list_id
  if (columnType === 'task_list' && currentColumn === columnId) {
    draggedTask.value = null
    // Parar auto-scroll ao finalizar drag de task
    stopAutoScroll()
    return
  }

  try {
    // Calcular posição no final da nova coluna
    const tasksInNewColumn = getTasksByColumn(columnId)
    const maxPosition = tasksInNewColumn.length > 0
      ? Math.max(...tasksInNewColumn.map(t => t.position || 0))
      : 0
    const newPosition = maxPosition + 1


    // Preparar dados de atualização
    const updatedTask = {
      ...taskData,
      position: newPosition
    }

    // Definir status ou task_list_id baseado no tipo de coluna
    if (columnType === 'status') {
      updatedTask.status = columnId
      updatedTask.task_list_id = null
    } else {
      updatedTask.task_list_id = columnId
      updatedTask.status = 'pending' // Status padrão para listas customizadas
    }

    // Primeiro, chama reorder para salvar posição e referência
    const reorderData = {
      id: taskData.id,
      position: newPosition
    }

    if (columnType === 'status') {
      reorderData.status = columnId
    } else {
      reorderData.task_list_id = columnId
    }

    await tasksApi.reorder([reorderData])

    // Atualizar na lista local - garantir que todos os campos sejam atualizados
    const index = tasks.value.findIndex(t => t.id === taskData.id)
    if (index !== -1) {
      tasks.value[index] = {
        ...tasks.value[index],
        position: reorderData.position,
        task_list_id: reorderData.task_list_id || tasks.value[index].task_list_id,
        status: reorderData.status || tasks.value[index].status
      }
      // UI local atualizada
    }
  } catch (err) {
    error.value = 'Failed to update task. Please try again.'
  }

  draggedTask.value = null
  // Parar auto-scroll ao finalizar drag de task
  stopAutoScroll()
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
    // Usar string vazia para ativar o placeholder do <select>
    task_list_id: '',
    project_id: projectId.value || ''
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

// Feedback visual para colunas
const onDragOverColumn = (event, columnId) => {
  event.preventDefault()
  event.dataTransfer.dropEffect = 'move'
}

const onDragEnterColumn = (event, columnId) => {
  event.preventDefault()
}

const onDragLeaveColumn = (event, columnId) => {
  // Simplificado
}

// Funções de scroll horizontal
const checkScroll = () => {
  if (!kanbanBoard.value) return

  const container = kanbanBoard.value
  canScrollLeft.value = container.scrollLeft > 0
  canScrollRight.value = container.scrollLeft < (container.scrollWidth - container.clientWidth)
}

const scrollLeft = () => {
  if (!kanbanBoard.value) return
  kanbanBoard.value.scrollBy({ left: -320, behavior: 'smooth' })
}

const scrollRight = () => {
  if (!kanbanBoard.value) return
  kanbanBoard.value.scrollBy({ left: 320, behavior: 'smooth' })
}

// Funções de drag-and-drop para colunas
const onColumnDragStart = (event, column) => {
  // Verificar se o clique foi em botões de ação, inputs ou tasks
  const target = event.target
  if (target && (
    target.closest('.btn') ||
    target.closest('.column-actions') ||
    target.closest('input') ||
    target.closest('button') ||
    target.closest('.task-card') ||           // Task sendo arrastada
    target.closest('.column-content')         // Área de conteúdo das tasks
  )) {
    event.preventDefault()
    return false
  }

  // Verificar se há task sendo arrastada
  if (draggedTask.value) {
    event.preventDefault()
    return false
  }

  draggedColumn.value = column
  event.dataTransfer.setData('text/plain', JSON.stringify({
    type: 'column',
    column: column
  }))
  event.dataTransfer.effectAllowed = 'move'

  // Iniciar auto-scroll
  startAutoScroll()

  // Log removido para evitar ruído no console
}

const onColumnDragEnd = (event) => {
  draggedColumn.value = null
  dragOverColumn.value = null

  // Parar auto-scroll
  stopAutoScroll()

  // Limpar todas as classes de drag-over
  document.querySelectorAll('.kanban-column').forEach(col => {
    col.classList.remove('drag-over-left', 'drag-over-right')
  })
}

const onColumnDragOver = (event, targetColumn) => {
  event.preventDefault()

  if (!draggedColumn.value || draggedColumn.value.id === targetColumn.id) {
    return
  }

  const rect = event.currentTarget.getBoundingClientRect()
  const midPoint = rect.left + rect.width / 2
  const mouseX = event.clientX
  const threshold = rect.width * 0.3 // 30% da largura da coluna para cada lado

  // Limpar classes anteriores
  dragOverColumn.value = null
  document.querySelectorAll('.kanban-column').forEach(col => {
    col.classList.remove('drag-over-left', 'drag-over-right', 'drag-over-center')
  })

  // Determinar zona do drop
  if (mouseX < rect.left + threshold) {
    // Zona esquerda - inserir antes
    dragOverColumn.value = `${targetColumn.id}-left`
    event.currentTarget.classList.add('drag-over-left')
  } else if (mouseX > rect.right - threshold) {
    // Zona direita - inserir depois
    dragOverColumn.value = `${targetColumn.id}-right`
    event.currentTarget.classList.add('drag-over-right')
  } else {
    // Zona central - trocar posição
    dragOverColumn.value = `${targetColumn.id}-center`
    event.currentTarget.classList.add('drag-over-center')
  }
}

const onColumnDragLeave = (event, column) => {
  // Só limpar se realmente saiu da área da coluna
  const rect = event.currentTarget.getBoundingClientRect()
  const x = event.clientX
  const y = event.clientY

  if (x < rect.left || x > rect.right || y < rect.top || y > rect.bottom) {
    dragOverColumn.value = null
  }
}

const onColumnDrop = async (event, targetColumn) => {
  event.preventDefault()

  // Verificar se é drag de task - ignorar
  try {
    const rawData = event.dataTransfer.getData('text/plain')
    const dragData = JSON.parse(rawData)
    if (dragData.type !== 'column') {
      return
    }
  } catch (err) {
    // Continuar se não conseguir parsear
  }

  if (!draggedColumn.value || draggedColumn.value.id === targetColumn.id) {
    return
  }

  const rect = event.currentTarget.getBoundingClientRect()
  const mouseX = event.clientX
  const columnWidth = rect.width
  const columnLeft = rect.left

  // Simplificar: esquerda (30%), centro-swap (40%), direita (30%)
  const leftZone = columnLeft + (columnWidth * 0.3)
  const rightZone = columnLeft + (columnWidth * 0.7)

  let dropSide
  if (mouseX < leftZone) {
    dropSide = 'before'
  } else if (mouseX > rightZone) {
    dropSide = 'after'
  } else {
    // Zona central (40%) - sempre trocar posições
    dropSide = 'swap'
  }


  try {
    await reorderColumns(draggedColumn.value, targetColumn, dropSide)
  } catch (err) {
    error.value = 'Falha ao reordenar listas. Tente novamente.'
  }

  onColumnDragEnd(event)
}

// Auto-scroll durante drag
let autoScrollInterval = null

const startAutoScroll = () => {
  if (autoScrollInterval) return // Evitar múltiplos intervalos

  autoScrollInterval = setInterval(() => {
    // Funciona tanto para colunas quanto para tasks
    if (!draggedColumn.value && !draggedTask.value) return

    const kanbanContainer = kanbanBoard.value
    if (!kanbanContainer) return

    // Obter posição do mouse relativa ao container
    const rect = kanbanContainer.getBoundingClientRect()
    const scrollThreshold = 120 // Reduzido para ser mais responsivo

    // Verificar se há evento de mouse ativo para obter posição
    if (window.lastMouseEvent) {
      const mouseX = window.lastMouseEvent.clientX

      let scrollSpeed = 0

      if (mouseX < rect.left + scrollThreshold) {
        // Scroll para esquerda - velocidade suave e progressiva
        const distanceFromEdge = Math.max(0, mouseX - rect.left)
        const proximityFactor = Math.max(0, (scrollThreshold - distanceFromEdge) / scrollThreshold)
        scrollSpeed = -Math.round(proximityFactor * proximityFactor * 40 + 5) // Curva quadrática mais suave

      } else if (mouseX > rect.right - scrollThreshold) {
        // Scroll para direita - velocidade suave e progressiva
        const distanceFromEdge = Math.max(0, rect.right - mouseX)
        const proximityFactor = Math.max(0, (scrollThreshold - distanceFromEdge) / scrollThreshold)
        scrollSpeed = Math.round(proximityFactor * proximityFactor * 40 + 5) // Curva quadrática mais suave
      }

      if (scrollSpeed !== 0) {
        kanbanContainer.scrollBy({ left: scrollSpeed, behavior: 'auto' })
      }
    }
  }, 16) // 60fps - mais suave e estável
}

const stopAutoScroll = () => {
  if (autoScrollInterval) {
    clearInterval(autoScrollInterval)
    autoScrollInterval = null
  }

  // Restaurar cursor padrão
  document.body.style.cursor = ''
  window.lastMouseEvent = null
}

// Capturar posição do mouse globalmente durante drag
const trackMouseDuringDrag = (event) => {
  // 🔧 MELHORADO: Funciona tanto para colunas quanto para tasks
  if (draggedColumn.value || draggedTask.value) {
    window.lastMouseEvent = event

    // Feedback visual adicional - mostrar velocidade de scroll no cursor
    const rect = kanbanBoard.value?.getBoundingClientRect()
    if (rect) {
      const scrollThreshold = 150
      let cursorStyle = draggedTask.value ? 'grabbing' : 'grabbing'

      if (event.clientX < rect.left + scrollThreshold) {
        const proximityFactor = Math.max(0, (scrollThreshold - (event.clientX - rect.left)) / scrollThreshold)
        if (proximityFactor > 0.7) cursorStyle = 'w-resize'
      } else if (event.clientX > rect.right - scrollThreshold) {
        const proximityFactor = Math.max(0, (scrollThreshold - (rect.right - event.clientX)) / scrollThreshold)
        if (proximityFactor > 0.7) cursorStyle = 'e-resize'
      }

      document.body.style.cursor = cursorStyle
    }
  }
}

// Reordenar colunas
const reorderColumns = async (sourceColumn, targetColumn, position) => {

  const currentLists = [...taskLists.value].sort((a, b) => (a.position || 0) - (b.position || 0))

  const sourceIndex = currentLists.findIndex(list => list.id === sourceColumn.id)
  const targetIndex = currentLists.findIndex(list => list.id === targetColumn.id)


  if (sourceIndex === -1 || targetIndex === -1) {
    return
  }

  let updateData = []

  if (position === 'swap') {
    // Trocar posições diretamente
    const sourcePosition = currentLists[sourceIndex].position
    const targetPosition = currentLists[targetIndex].position

    updateData = currentLists.map(list => {
      if (list.id === sourceColumn.id) {
        return { id: list.id, position: targetPosition }
      } else if (list.id === targetColumn.id) {
        return { id: list.id, position: sourcePosition }
      } else {
        return { id: list.id, position: list.position }
      }
    })
  } else {
    // Lógica normal de reordenação
    // Remover o item da posição original
    const reorderedLists = [...currentLists]
    const [movedItem] = reorderedLists.splice(sourceIndex, 1)

    // Inserir na nova posição
    let insertIndex = targetIndex
    if (position === 'before') {
      // Inserir antes da target
      insertIndex = targetIndex
    } else if (position === 'after') {
      // Inserir depois da target
      insertIndex = targetIndex + 1
    }

    // Ajustar índice se o item original estava antes da posição de inserção
    if (sourceIndex < insertIndex) {
      insertIndex--
    }

    reorderedLists.splice(insertIndex, 0, movedItem)

    // Gerar posições sequenciais inteiras
    updateData = reorderedLists.map((list, index) => ({
      id: list.id,
      position: index + 1
    }))
  }


  try {
    // Atualizar via API - enviar no formato correto
    await taskListsApi.reorder(updateData)
  } catch (error) {
    throw error
  }

  // Atualizar UI local - FORÇAR REATIVIDADE

  // Criar uma nova referência do array para garantir reatividade
  const updatedLists = [...taskLists.value]

  updateData.forEach(item => {
    const listIndex = updatedLists.findIndex(list => list.id === item.id)
    if (listIndex !== -1) {
      updatedLists[listIndex] = { ...updatedLists[listIndex], position: item.position }
    }
  })

  // Reordenar array e atribuir nova referência
  updatedLists.sort((a, b) => (a.position || 0) - (b.position || 0))

  // Forçar reatividade com nova referência
  taskLists.value = updatedLists
 
}

onMounted(async () => {
  await Promise.all([loadProjects(), loadTaskLists()])
  await loadTasks()

  // Adicionar event listeners para rastrear mouse durante drag
  document.addEventListener('dragover', trackMouseDuringDrag)
  document.addEventListener('dragenter', trackMouseDuringDrag)
  document.addEventListener('mousemove', trackMouseDuringDrag)

  // Verificar scroll após carregar
  setTimeout(() => {
    checkScroll()
  }, 100)
})

onBeforeUnmount(() => {
  // Limpar event listeners
  document.removeEventListener('dragover', trackMouseDuringDrag)
  document.removeEventListener('dragenter', trackMouseDuringDrag)
  document.removeEventListener('mousemove', trackMouseDuringDrag)

  // Parar auto-scroll se estiver ativo
  stopAutoScroll()
})

// Watcher para recarregar quando mudar de projeto
watch(() => projectId.value, async () => {
  await Promise.all([loadTaskLists(), loadTasks()])

  // Reset form values
  newTask.value.project_id = projectId.value || ''
  newTaskList.value.project_id = projectId.value || ''

  // Verificar scroll após mudança
  setTimeout(() => {
    checkScroll()
  }, 100)
})

// Watcher para verificar scroll quando listas mudarem
watch(() => taskLists.value.length, () => {
  setTimeout(() => {
    checkScroll()
  }, 100)
})
</script>

<style scoped>
.tasks-view {
  max-width: 1400px;
  margin: 0 auto;
}

.header-section {
  display: grid;
  grid-template-columns: 1fr auto; /* título+ações do projeto | botões principais */
  align-items: center;
  column-gap: 1rem;
  margin-bottom: 2rem;
}

/* Cabeçalho do projeto */
.project-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
  min-width: 0; /* permite que o título encolha e aplique ellipsis */
}

/* Ações do projeto */
.project-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-shrink: 0; /* ícones não encolhem e não empurram o layout */
}

.project-edit-btn,
.project-delete-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

.project-edit-btn:hover {
  background-color: #e0f2fe;
}

.project-delete-btn:hover {
  background-color: #ffebee;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-shrink: 0; /* impede que os botões encolham quando o título é longo */
}

.header-section h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  /* Truncar em uma linha para preservar ações à direita */
  flex: 1;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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
  display: flex;
  gap: 1.5rem;
  min-height: 600px;
  overflow-x: auto;
  overflow-y: hidden;
  padding-bottom: 1rem;
  /* Scroll horizontal */
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
}

.kanban-column {
  background: #f8fafc;
  border-radius: 0.5rem;
  padding: 1rem;
  min-width: 320px;
  max-width: 320px;
  flex-shrink: 0;
  transition: all 0.2s;
}

.column-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e2e8f0;
  cursor: grab;
  transition: all 0.2s ease;
  position: relative;
}

.column-header:active {
  cursor: grabbing;
}

.column-header.dragging {
  opacity: 0.5;
  transform: rotate(2deg);
}

.kanban-column.column-dragging {
  opacity: 0.5;
  transform: rotate(2deg);
}

.kanban-column.drag-over-left {
  border-left: 4px solid #3b82f6;
  background-color: rgba(59, 130, 246, 0.1);
}

.kanban-column.drag-over-right {
  border-right: 4px solid #3b82f6;
  background-color: rgba(59, 130, 246, 0.1);
}

.kanban-column.drag-over-center {
  border: 2px solid #10b981;
  background-color: rgba(16, 185, 129, 0.1);
  transform: scale(1.02);
}

/* Indicadores de scroll durante drag */
.kanban-board::before,
.kanban-board::after {
  content: '';
  position: fixed;
  top: 0;
  bottom: 0;
  width: 150px;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.2s;
  z-index: 1000;
}

.kanban-board::before {
  left: 0;
  background: linear-gradient(to right, rgba(59, 130, 246, 0.1), transparent);
}

.kanban-board::after {
  right: 0;
  background: linear-gradient(to left, rgba(59, 130, 246, 0.1), transparent);
}

.kanban-board.drag-active::before,
.kanban-board.drag-active::after {
  opacity: 1;
}

.drag-handle {
  color: #9ca3af;
  font-size: 1rem;
  margin-right: 0.5rem;
  transition: color 0.2s;
  cursor: grab;
}

.column-header:hover .drag-handle {
  color: #6b7280;
}

.column-header h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #374151;
  flex: 1;
  /* Evitar que o título invada a área dos botões */
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.column-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-shrink: 0;
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
  position: relative;
}

.task-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.task-card:active {
  cursor: grabbing;
}

.task-card.drag-over-before::before {
  content: '';
  position: absolute;
  top: -3px;
  left: 0;
  right: 0;
  height: 3px;
  background-color: #2563eb;
  border-radius: 2px;
  z-index: 10;
}

.task-card.drag-over-after::after {
  content: '';
  position: absolute;
  bottom: -3px;
  left: 0;
  right: 0;
  height: 3px;
  background-color: #2563eb;
  border-radius: 2px;
  z-index: 10;
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
  min-width: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-break: break-word;
}

.task-actions {
  display: flex;
  gap: 0.5rem;
  flex-shrink: 0;
}

/* Botões de ação padronizados com ProjectsView */
.btn-edit,
.btn-delete {
  background-color: transparent;
  color: #6b7280;
  border: 1px solid #d1d5db;
  padding: 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 2.5rem;
  height: 2.5rem;
  flex: none;
}

.btn-edit:hover {
  background-color: #f0f9ff;
  color: #0284c7;
  border-color: #bae6fd;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-delete:hover {
  background-color: #fef2f2;
  color: #dc2626;
  border-color: #fecaca;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.task-description {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 0.75rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-break: break-word;
  overflow-wrap: anywhere;
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

.empty-board {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  color: #9ca3af;
}

.empty-create-list {
  font-style: italic;
  font-weight: 500;
  color: #4b5563;
  cursor: pointer;
}

.empty-create-list:hover {
  color: #111827;
}

.drag-over-column {
  background-color: #eff6ff !important;
  border: 2px dashed #2563eb !important;
  border-radius: 0.5rem;
}

/* Estilo personalizado para scroll horizontal */
.kanban-board::-webkit-scrollbar {
  height: 8px;
}

.kanban-board::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.kanban-board::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.kanban-board::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Container para indicadores de scroll */
.kanban-container {
  position: relative;
  width: 100%;
}

.scroll-indicator {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid #e2e8f0;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  font-size: 14px;
  color: #64748b;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.scroll-indicator:hover {
  background: white;
  color: #2563eb;
  transform: translateY(-50%) scale(1.05);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.scroll-left {
  left: 10px;
}

.scroll-right {
  right: 10px;
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

/* Modal de exclusão de projeto */
.project-to-delete {
  font-weight: bold;
  font-size: 1.1rem;
  color: #dc2626;
  margin: 1rem 0;
  padding: 0.5rem;
  background-color: #fef2f2;
  border-radius: 0.375rem;
  text-align: center;
}

.warning-box {
  background-color: #fff3cd;
  border: 1px solid #ffeaa7;
  border-radius: 0.5rem;
  padding: 1rem;
  margin: 1rem 0;
}

.warning-box p {
  margin: 0.5rem 0;
  color: #856404;
}

.warning-box ul {
  margin: 0.5rem 0;
  padding-left: 1.5rem;
  color: #856404;
}

.warning-box li {
  margin: 0.25rem 0;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

@media (max-width: 768px) {
  .kanban-column {
    min-width: 280px;
    max-width: 280px;
  }

  .kanban-board {
    gap: 1rem;
  }

  .scroll-indicator {
    width: 35px;
    height: 35px;
    font-size: 12px;
  }

  .scroll-left {
    left: 5px;
  }

  .scroll-right {
    right: 5px;
  }
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 90%;
}

.modal-content h3 {
  margin: 0 0 1rem 0;
  color: #374151;
  font-size: 1.25rem;
}

.modal-content p {
  margin: 0 0 1.5rem 0;
  color: #6b7280;
  line-height: 1.5;
}

.modal-danger {
  color: #dc2626;
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

/* Modal exclusão de lista */
.confirm-list-delete h3,
.confirm-list-delete p {
  text-align: center;
}

.confirm-list-delete .modal-subtext {
  color: #9ca3af;
  margin-top: -0.75rem;
}

.confirm-list-delete .modal-actions.center {
  justify-content: center;
}

.modal-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #dc2626;
  margin-bottom: 0.5rem;
}

.btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
}

.btn-danger {
  background-color: #dc2626;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-danger:hover {
  background-color: #b91c1c;
}

/* Textarea */
.form-textarea {
  resize: vertical;
  min-height: 80px;
  max-height: 200px;
  width: 100%;
  box-sizing: border-box;
}

@media (max-width: 768px) {
  .kanban-board {
    grid-template-columns: 1fr;
  }

  .header-section {
    display: grid;
    grid-template-columns: 1fr;
    row-gap: 1rem;
    align-items: stretch;
  }

  .form-actions {
    flex-direction: column;
  }

  .drag-handle {
    font-size: 1.125rem;
    margin-right: 0.375rem;
  }
}

/* Botão Nova Lista */
.btn-new-list {
  background-color: #059669 !important;
  color: white !important;
  padding: 0.6rem 1.2rem !important;
  font-size: 0.95rem !important;
  font-weight: 600 !important;
  border: none !important;
  transition: all 0.2s ease !important;
}

.btn-new-list:hover {
  background-color: #047857 !important;
  transform: translateY(-1px) !important;
  box-shadow: 0 4px 8px rgba(5, 150, 105, 0.3) !important;
}

.btn-new-list:active {
  transform: translateY(0) !important;
  box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2) !important;
}

/* Botão Nova Tarefa */
.btn-new-task {
  background-color: #1d4ed8 !important;
  color: white !important;
  padding: 0.6rem 1.2rem !important;
  font-size: 0.95rem !important;
  font-weight: 600 !important;
  border: none !important;
  transition: all 0.2s ease !important;
}

.btn-new-task:hover {
  background-color: #1e40af !important;
  transform: translateY(-1px) !important;
  box-shadow: 0 4px 8px rgba(29, 78, 216, 0.3) !important;
}

.btn-new-task:active {
  transform: translateY(0) !important;
  box-shadow: 0 2px 4px rgba(29, 78, 216, 0.2) !important;
}
</style>
