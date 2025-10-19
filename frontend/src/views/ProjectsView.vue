<template>
  <div class="projects-view">
    <div class="header-section">
      <h1>Projetos</h1>
    <button @click="showCreateForm = true" class="btn btn-primary">
      + Novo Projeto
    </button>
    </div>

    <!-- Create Project Form -->
    <div v-if="showCreateForm" class="card form-card">
      <h2>Criar Novo Projeto</h2>
      <form @submit.prevent="createProject">
        <div class="form-group">
          <label class="form-label">Nome do Projeto</label>
          <input 
            type="text" 
            v-model="newProject.name"
            class="form-input"
            required
            placeholder="Digite o nome do projeto"
          />
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Criando...' : 'Criar Projeto' }}
          </button>
          <button type="button" @click="cancelCreate" class="btn btn-secondary">
            Cancelar
          </button>
        </div>
      </form>
    </div>

    <!-- Projects Grid -->
    <div v-if="projects.length > 0" class="grid grid-cols-3">
      <div v-for="project in projects" :key="project.id" class="card project-card">
        <h3>{{ project.name }}</h3>
        <p class="project-meta">
          Criado em: {{ formatDate(project.created_at) }}
        </p>
        <div class="project-actions">
          <RouterLink :to="`/projects/${project.id}/tasks`" class="btn btn-primary">
            Ver Tarefas
          </RouterLink>
          <button @click="deleteProject(project.id)" class="btn btn-delete" title="Excluir Projeto">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading" class="empty-state">
      <h2>Nenhum Projeto Ainda</h2>
      <p>Crie seu primeiro projeto para começar a gerenciar tarefas.</p>
      <button @click="showCreateForm = true" class="btn btn-primary">
        Criar Primeiro Projeto
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !showCreateForm" class="loading">
      Carregando projetos...
    </div>

    <!-- Error State -->
    <div v-if="error" class="error">
      {{ error }}
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirm" class="modal-overlay" @click="cancelDelete">
      <div class="modal-content" @click.stop>
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir este projeto? Todas as tarefas relacionadas também serão excluídas.</p>
        <div class="modal-actions">
          <button @click="cancelDelete" class="btn btn-secondary">Cancelar</button>
          <button @click="confirmDelete" class="btn btn-danger">Excluir</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { projectsApi } from '@/services/api'

const projects = ref([])
const loading = ref(false)
const error = ref('')
const showCreateForm = ref(false)
const newProject = ref({ name: '' })
const showDeleteConfirm = ref(false)
const projectToDelete = ref(null)

const loadProjects = async () => {
  loading.value = true
  error.value = ''
  try {
    const response = await projectsApi.getAll()
    projects.value = response.data
  } catch (err) {
    error.value = 'Failed to load projects. Please try again.'
    console.error('Error loading projects:', err)
  } finally {
    loading.value = false
  }
}

const createProject = async () => {
  if (!newProject.value.name.trim()) return
  
  loading.value = true
  error.value = ''
  try {
    const response = await projectsApi.create(newProject.value)
    projects.value.push(response.data)
    newProject.value.name = ''
    showCreateForm.value = false
  } catch (err) {
    error.value = 'Failed to create project. Please try again.'
    console.error('Error creating project:', err)
  } finally {
    loading.value = false
  }
}

const cancelCreate = () => {
  showCreateForm.value = false
  newProject.value.name = ''
}

const deleteProject = (projectId) => {
  projectToDelete.value = projectId
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  if (!projectToDelete.value) return
  
  try {
    await projectsApi.delete(projectToDelete.value)
    projects.value = projects.value.filter(project => project.id !== projectToDelete.value)
    showDeleteConfirm.value = false
    projectToDelete.value = null
  } catch (err) {
    error.value = 'Falha ao excluir projeto. Tente novamente.'
    console.error('Error deleting project:', err)
  }
}

const cancelDelete = () => {
  showDeleteConfirm.value = false
  projectToDelete.value = null
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  loadProjects()
})
</script>

<style scoped>
.projects-view {
  max-width: 1200px;
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

.project-card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.project-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.project-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.project-meta {
  color: #6b7280;
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.project-actions {
  margin-top: auto;
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

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
}

.btn-delete:hover {
  background-color: #fef2f2;
  color: #dc2626;
  border-color: #fecaca;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.empty-state h2 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: #374151;
}

.empty-state p {
  margin-bottom: 1.5rem;
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

/* Modal Styles */
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

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
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

@media (max-width: 768px) {
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