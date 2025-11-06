import axios from 'axios'

const API_BASE_URL = 'http://127.0.0.1:8000/api'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Gerenciar projetos (API)
export const projectsApi = {
  getAll: () => api.get('/projects'),
  create: (data) => api.post('/projects', data),
  getById: (id) => api.get(`/projects/${id}`),
  update: (id, data) => api.put(`/projects/${id}`, data),
  delete: (id) => api.delete(`/projects/${id}`),
  getTasks: (id) => api.get(`/projects/${id}/tasks`),
}

// Gerenciar tarefas (API)
export const tasksApi = {
  getAll: (params = {}) => {
    const searchParams = new URLSearchParams()

    if (params.project_id) searchParams.append('project_id', params.project_id)
    if (params.task_list_id) searchParams.append('task_list_id', params.task_list_id)
    if (params.use_task_lists) searchParams.append('use_task_lists', params.use_task_lists)

    const queryString = searchParams.toString()
    const url = queryString ? `/tasks?${queryString}` : '/tasks'

    return api.get(url)
  },
  create: (data) => api.post('/tasks', data),
  getById: (id) => api.get(`/tasks/${id}`),
  update: (id, data) => api.put(`/tasks/${id}`, data),
  delete: (id) => api.delete(`/tasks/${id}`),
  reorder: (items) => api.post('/tasks/reorder', { items }),
}

// Gerenciar listas de tarefas (API)
export const taskListsApi = {
  getAll: (params = {}) => {
    const searchParams = new URLSearchParams()

    if (params.project_id) searchParams.append('project_id', params.project_id)
    if (params.user_id) searchParams.append('user_id', params.user_id)

    const queryString = searchParams.toString()
    const url = queryString ? `/task-lists?${queryString}` : '/task-lists'

    return api.get(url)
  },
  create: (data) => api.post('/task-lists', data),
  getById: (id) => api.get(`/task-lists/${id}`),
  update: (id, data) => api.put(`/task-lists/${id}`, data),
  delete: (id) => api.delete(`/task-lists/${id}`),
  reorder: (lists) => api.post('/task-lists/reorder', { lists }),
}

export default api
