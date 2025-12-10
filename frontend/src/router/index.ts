import { createRouter, createWebHistory } from 'vue-router'
import DrawListView from '@/views/DrawListView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'draws',
      component: DrawListView,
    },
    {
      path: '/create',
      name: 'create',
      component: () => import('@/views/CreateDrawView.vue'),
    },
    {
      path: '/draw/:id',
      name: 'draw-detail',
      component: () => import('@/views/DrawDetailView.vue'),
    },
  ],
})

export default router
