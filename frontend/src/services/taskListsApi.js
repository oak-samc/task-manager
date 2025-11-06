import api from './api'

export const taskListsApi = {
  // Buscar todas as task lists de um projeto
  getByProject(projectId) {
    return api.get(`/task-lists?project_id=${projectId}`)
  },

  // Buscar todas as task lists de um usuário
  getByUser(userId) {
    return api.get(`/task-lists?user_id=${userId}`)
  },

  // Buscar todas as task lists
  getAll() {
    return api.get('/task-lists')
  },

  // Buscar uma task list específica
  getById(id) {
    return api.get(`/task-lists/${id}`)
  },

  // Criar nova task list
  create(taskListData) {
    return api.post('/task-lists', taskListData)
  },

  // Atualizar task list
  update(id, taskListData) {
    return api.put(`/task-lists/${id}`, taskListData)
  },

  // Excluir task list
  delete(id) {
    return api.delete(`/task-lists/${id}`)
  },

  // Reordenar task lists
  reorder(lists) {
    return api.post('/task-lists/reorder', { lists })
  }
}

export default taskListsApi
