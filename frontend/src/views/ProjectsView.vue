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