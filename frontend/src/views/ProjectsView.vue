<template>
  <div class="projects-view">
    <div class="header-section">
      <div class="header-top">
        <h1>Projetos</h1>
        <div class="header-actions">
          <div class="search-container">
            <input
              type="text"
              v-model="searchQuery"
              class="search-input"
              placeholder="Buscar projetos..."
              @input="filterProjects"
            />
            <span class="search-icon">🔍</span>
          </div>
          <button @click="showCreateForm = true" class="btn btn-primary btn-new-project">
            + Novo Projeto
          </button>
        </div>
      </div>
    </div>

    
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

        <div class="form-group">
          <label class="form-label">Descrição (Opcional)</label>
          <textarea
            v-model="newProject.description"
            class="form-input"
            placeholder="Digite a descrição do projeto"
            rows="3"
          ></textarea>
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

    
    <div v-if="allProjects.length > 0" class="projects-stats">
      <span class="stats-text">
        {{ filteredProjects.length }} de {{ allProjects.length }} projetos
        <span v-if="searchQuery">(filtrados)</span>
      </span>
      <div class="view-controls">
        <button
          @click="toggleGridSize('small')"
          :class="['view-btn', { active: gridSize === 'small' }]"
          title="Visualização compacta"
        >⊞</button>
        <button
          @click="toggleGridSize('medium')"
          :class="['view-btn', { active: gridSize === 'medium' }]"
          title="Visualização padrão"
        >⊡</button>
        <button
          @click="toggleGridSize('large')"
          :class="['view-btn', { active: gridSize === 'large' }]"
          title="Visualização detalhada"
        >⊟</button>
      </div>
    </div>

    
    <div v-if="filteredProjects.length > 0" :class="['projects-grid', `grid-${gridSize}`]">
      <div v-for="project in displayedProjects" :key="project.id" class="card project-card">
        <h3>{{ project.name }}</h3>
        <p class="project-description">
          {{ project.description || 'Sem descrição' }}
        </p>
        <p class="project-meta">
          Criado em: {{ formatDate(project.created_at) }}
        </p>
        <div class="project-actions">
          <RouterLink :to="`/projects/${project.id}/tasks`" class="btn btn-primary">
        Ver tarefas
          </RouterLink>
<button @click="editProject(project)" class="btn btn-edit" title="Editar projeto">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M11 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V20C2 20.5304 2.21071 21.0391 2.58579 21.4142C2.96086 21.7893 3.46957 22 4 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M18.5 2.49998C18.8978 2.10216 19.4374 1.87866 20 1.87866C20.5626 1.87866 21.1022 2.10216 21.5 2.49998C21.8978 2.89781 22.1213 3.43737 22.1213 3.99998C22.1213 4.56259 21.8978 5.10216 21.5 5.49998L12 15L8 16L9 12L18.5 2.49998Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
<button @click="deleteProject(project.id)" class="btn btn-delete" title="Excluir projeto">
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

    
    <div v-if="hasMoreProjects" class="load-more-container">
      <button @click="loadMoreProjects" class="btn btn-secondary" :disabled="loadingMore">
        {{ loadingMore ? 'Carregando...' : `Carregar mais (${remainingCount} restantes)` }}
      </button>
    </div>

    
    <div v-else-if="searchQuery && filteredProjects.length === 0" class="empty-search">
      <h3>🔍 Nenhum projeto encontrado</h3>
      <p>Tente ajustar sua busca ou criar um novo projeto.</p>
      <button @click="clearSearch" class="btn btn-secondary">Limpar busca</button>
    </div>

    
    <div v-else-if="allProjects.length === 0 && !loading" class="empty-state">
      <h2>Nenhum Projeto Ainda</h2>
      <p>Crie seu primeiro projeto para começar a gerenciar tarefas.</p>
      <button @click="showCreateForm = true" class="btn btn-primary">
      Criar primeiro projeto
      </button>
    </div>

    
    <div v-if="loading && !showCreateForm" class="loading">
      Carregando projetos...
    </div>

    
    <div v-if="error" class="error">
      {{ error }}
    </div>

    
    <div v-if="showEditModal" class="modal-overlay" @click="cancelEdit">
      <div class="modal-content" @click.stop>
        <h3>Editar Projeto</h3>
        <form @submit.prevent="updateProject">
          <div class="form-group">
            <label class="form-label">Nome do Projeto</label>
            <input
              type="text"
              v-model="editingProject.name"
              class="form-input"
              required
              placeholder="Digite o nome do projeto"
            />
          </div>
          <div class="form-group">
            <label class="form-label">Descrição (Opcional)</label>
            <textarea
              v-model="editingProject.description"
              class="form-input"
              placeholder="Digite a descrição do projeto"
              rows="3"
            ></textarea>
          </div>
          <div class="modal-actions">
            <button @click="cancelEdit" type="button" class="btn btn-secondary">Cancelar</button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Salvando...' : 'Salvar' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    
    <div v-if="showDeleteConfirm" class="modal-overlay" @click="cancelDelete">
      <div class="modal-content" @click.stop>
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir este projeto?</p>
        <p style="color: #dc2626; font-weight: 500;">⚠️ Todas as listas e tarefas relacionadas também serão excluídas permanentemente.</p>
        <div class="modal-actions">
          <button @click="cancelDelete" class="btn btn-secondary">Cancelar</button>
          <button @click="confirmDelete" class="btn btn-danger">Excluir</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { projectsApi } from '@/services/api'

const allProjects = ref([])
const filteredProjects = ref([])
const displayedProjects = ref([])
const loading = ref(false)
const loadingMore = ref(false)
const error = ref('')
const showCreateForm = ref(false)
const newProject = ref({ name: '', description: '' })
const showDeleteConfirm = ref(false)
const projectToDelete = ref(null)
const showEditModal = ref(false)
const editingProject = ref({ id: null, name: '', description: '' })

// Gerenciar busca e paginação
const searchQuery = ref('')
const gridSize = ref(localStorage.getItem('projectsGridSize') || 'medium') // small, medium, large
const itemsPerPage = 12
const currentPage = ref(1)

// Calcular estados derivados
const hasMoreProjects = computed(() => {
  return displayedProjects.value.length < filteredProjects.value.length
})

const remainingCount = computed(() => {
  return filteredProjects.value.length - displayedProjects.value.length
})

const loadProjects = async () => {
  loading.value = true
  error.value = ''
  try {
    const response = await projectsApi.getAll()
    allProjects.value = response.data
    filterProjects()
  } catch (err) {
    error.value = 'Failed to load projects. Please try again.'
    // Silenciar logs no console; manter feedback de erro via UI
  } finally {
    loading.value = false
  }
}

const filterProjects = () => {
  if (!searchQuery.value.trim()) {
    filteredProjects.value = [...allProjects.value]
  } else {
    const query = searchQuery.value.toLowerCase()
    filteredProjects.value = allProjects.value.filter(project =>
      project.name.toLowerCase().includes(query)
    )
  }

  currentPage.value = 1
  updateDisplayedProjects()
}

const updateDisplayedProjects = () => {
  const endIndex = currentPage.value * itemsPerPage
  displayedProjects.value = filteredProjects.value.slice(0, endIndex)
}

const loadMoreProjects = () => {
  currentPage.value++
  updateDisplayedProjects()
}

const clearSearch = () => {
  searchQuery.value = ''
  filterProjects()
}

const toggleGridSize = (size) => {
  if (gridSize.value === size) {
    gridSize.value = 'medium'
  } else {
    gridSize.value = size
  }
  localStorage.setItem('projectsGridSize', gridSize.value)
}

const createProject = async () => {
  if (!newProject.value.name.trim()) return

  loading.value = true
  error.value = ''
  try {
    const response = await projectsApi.create(newProject.value)
    allProjects.value.push(response.data)
    newProject.value.name = ''
    newProject.value.description = ''
    showCreateForm.value = false
    filterProjects() // Atualizar filtros
  } catch (err) {
    error.value = 'Failed to create project. Please try again.'
    // Silenciar logs no console; manter feedback de erro via UI
  } finally {
    loading.value = false
  }
}

const cancelCreate = () => {
  showCreateForm.value = false
  newProject.value.name = ''
  newProject.value.description = ''
}

const editProject = (project) => {
  editingProject.value = {
    id: project.id,
    name: project.name,
    description: project.description || ''
  }
  showEditModal.value = true
}

const updateProject = async () => {
  if (!editingProject.value.name.trim()) return

  loading.value = true
  error.value = ''
  try {
    const response = await projectsApi.update(editingProject.value.id, {
      name: editingProject.value.name,
      description: editingProject.value.description
    })

    const index = allProjects.value.findIndex(p => p.id === editingProject.value.id)
    if (index !== -1) {
      allProjects.value[index] = response.data
    }

    showEditModal.value = false
    filterProjects() // Atualizar filtros
  } catch (err) {
    error.value = 'Falha ao atualizar projeto. Tente novamente.'
    // Silenciar logs no console; manter feedback de erro via UI
  } finally {
    loading.value = false
  }
}

const cancelEdit = () => {
  showEditModal.value = false
  editingProject.value = { id: null, name: '', description: '' }
}

const deleteProject = (projectId) => {
  projectToDelete.value = projectId
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  if (!projectToDelete.value) return

  try {
    const response = await projectsApi.delete(projectToDelete.value)
    
    // Remover da lista local
    allProjects.value = allProjects.value.filter(project => project.id !== projectToDelete.value)
    
    if (response.data?.deleted_tasks || response.data?.deleted_task_lists) {
      // informação disponível na resposta; sem logs no console
    }
    
    showDeleteConfirm.value = false
    projectToDelete.value = null
    filterProjects() // Atualizar filtros
  } catch (err) {
    error.value = 'Falha ao excluir projeto. Tente novamente.'
    // Silenciar logs no console; manter feedback de erro via UI
    
    showDeleteConfirm.value = false
    projectToDelete.value = null

    setTimeout(() => {
      error.value = ''
    }, 5000)
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
  margin-bottom: 2rem;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: stretch;
}

.search-container {
  position: relative;
  min-width: 250px;
  max-width: 320px;
  display: flex;
  align-items: center;
}

.search-input {
  width: 100%;
  padding: 0.6rem 2.5rem 0.6rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.95rem;
  transition: all 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-icon {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
}

.header-top h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

/* Estilizar cabeçalho e controles de visualização */
.projects-stats {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 0.5rem;
  border: 1px solid #e2e8f0;
}

.stats-text {
  color: #64748b;
  font-size: 0.875rem;
  font-weight: 500;
}

.view-controls {
  display: flex;
  gap: 0.25rem;
}

.view-btn {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.view-btn:hover {
  background: #f3f4f6;
}

.view-btn.active {
  background: #2563eb;
  color: white;
  border-color: #2563eb;
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

/* Estilizar formulário de criação */
.form-card .form-group {
  margin-bottom: 1rem;
}

.form-card .form-label {
  display: block;
  margin-bottom: 0.5rem;
  color: #374151;
  font-weight: 500;
  font-size: 0.875rem;
}

.form-card .form-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
  box-sizing: border-box;
}

.form-card .form-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Estilizar campo de descrição */
.form-card textarea.form-input {
  /* Tamanho fixo proporcional ao layout e responsivo */
  height: clamp(120px, 16vh, 160px);
  min-height: 120px;
  max-height: 160px;

  /* Permite apenas redimensionamento horizontal dentro do container */
  resize: horizontal;
  min-width: 280px;
  max-width: 100%;

  /* Respeita paddings ao expandir horizontalmente */
  padding: 0.75rem;
  box-sizing: border-box;
  overflow-y: auto;
}

/* Definir grid responsivo */
.projects-grid {
  display: grid;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.grid-small {
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
}

.grid-medium {
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.grid-large {
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 2rem;
}

/* Estilizar botão de carregar mais */
.load-more-container {
  text-align: center;
  margin: 2rem 0;
}

/* Mostrar estado vazio da busca */
.empty-search {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.empty-search h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  color: #374151;
}

.empty-search p {
  margin-bottom: 1.5rem;
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
  /* Garantir que títulos longos não estourem o card */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.project-description {
  color: #4b5563;
  font-size: 0.875rem;
  margin-bottom: 0.75rem;
  font-style: italic;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.4em;
  word-break: break-word;
  overflow-wrap: anywhere;
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
  border: 2px solid #ef4444;
  color: #dc2626;
  padding: 1rem;
  border-radius: 0.5rem;
  margin: 1rem 0;
  font-weight: 500;
  box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1);
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Estilizar modais */
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

.modal-content .form-group {
  margin-bottom: 1rem;
}

.modal-content .form-label {
  display: block;
  margin-bottom: 0.5rem;
  color: #374151;
  font-weight: 500;
  font-size: 0.875rem;
}

.modal-content .form-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: border-color 0.2s;
  box-sizing: border-box;
}

.modal-content .form-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.modal-content textarea.form-input {
  /* Mesma regra do formulário principal para manter consistência */
  height: clamp(120px, 16vh, 160px);
  min-height: 120px;
  max-height: 160px;
  resize: horizontal;
  min-width: 280px;
  max-width: 100%;
  padding: 0.75rem;
  box-sizing: border-box;
  overflow-y: auto;
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

/* Restaurar estilos locais de botões para manter o padrão anterior no card */
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

/* Usa os estilos globais de botões definidos em App.vue
   para manter o padrão anterior de tamanho e cores */

@media (max-width: 768px) {
  .header-top {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .header-actions {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .search-container {
    min-width: auto;
    max-width: none;
  }

  .btn-new-project {
    justify-content: center !important;
  }  .projects-stats {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .form-actions {
    flex-direction: column;
  }

  .grid-small,
  .grid-medium,
  .grid-large {
    grid-template-columns: 1fr;
  }
}

/* Layout para tablets */
@media (max-width: 1024px) and (min-width: 769px) {
  .search-container {
    min-width: 200px;
    max-width: 250px;
  }
}

/* Botão Novo Projeto */
.btn-new-project {
  background-color: #1d4ed8 !important;
  color: white !important;
  padding: 0.6rem 1.2rem !important;
  font-size: 0.95rem !important;
  font-weight: 600 !important;
  border: 1px solid transparent !important; /* Altura igual ao input */
  border-radius: 0.5rem !important; /* Mesmo border-radius */
  transition: all 0.2s ease !important;
  height: auto !important;
  display: flex !important;
  align-items: center !important;
  white-space: nowrap !important;
}

.btn-new-project:hover {
  background-color: #1e40af !important;
  transform: translateY(-1px) !important;
  box-shadow: 0 4px 8px rgba(29, 78, 216, 0.3) !important;
}

.btn-new-project:active {
  transform: translateY(0) !important;
  box-shadow: 0 2px 4px rgba(29, 78, 216, 0.2) !important;
}
</style>
