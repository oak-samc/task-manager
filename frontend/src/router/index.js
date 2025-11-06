import { createRouter, createWebHistory } from 'vue-router'
import ProjectsView from '@/views/ProjectsView.vue'
import TasksView from '@/views/TasksView.vue'

// Definir rotas da aplicação

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/projects',
      name: 'projects',
      component: ProjectsView
    },
    {
      path: '/',
      redirect: '/projects'
    },
    {
      path: '/alltasks',
      name: 'alltasks',
      component: TasksView
    },
    {
      path: '/tasks',
      redirect: '/alltasks'
    },
    {
      path: '/projects/:projectId/tasks',
      name: 'project-tasks',
      component: TasksView
    },
    {
      path: '/projects/:projectId/alltasksproject',
      name: 'project-alltasks',
      component: TasksView
    }
  ],
})

export default router
