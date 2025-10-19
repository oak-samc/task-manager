import axios from 'axios'

const API_BASE_URL = 'http://127.0.0.1:8000/api'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Projects API
export const projectsApi = {
  getAll: () => api.get('/projects'),
  create: (data) => api.post('/projects', data),
  getById: (id) => api.get(`/projects/${id}`),
  getTasks: (id) => api.get(`/projects/${id}/tasks`),
}

// Tasks API
export const tasksApi = {
  getAll: () => api.get('/tasks'),
  create: (data) => api.post('/tasks', data),
  getById: (id) => api.get(`/tasks/${id}`),
  update: (id, data) => api.put(`/tasks/${id}`, data),
  delete: (id) => api.delete(`/tasks/${id}`),
}

export default api