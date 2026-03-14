import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
    // Public routes
    {
        path: '/',
        component: () => import('@/layouts/PublicLayout.vue'),
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('@/views/public/Home.vue'),
            },
            {
                path: 'books',
                name: 'books',
                component: () => import('@/views/public/BookList.vue'),
            },
            {
                path: 'books/:id',
                name: 'book-detail',
                component: () => import('@/views/public/BookDetail.vue'),
            },
            {
                path: 'books/:id/read',
                name: 'book-read',
                component: () => import('@/views/public/BookReader.vue'),
            },
        ],
    },

    // Auth
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/Login.vue'),
        meta: { guestOnly: true },
    },

    // Admin routes
    {
        path: '/admin',
        component: () => import('@/layouts/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'admin-dashboard',
                component: () => import('@/views/admin/Dashboard.vue'),
            },
            {
                path: 'books',
                name: 'admin-books',
                component: () => import('@/views/admin/books/Index.vue'),
            },
            {
                path: 'books/create',
                name: 'admin-books-create',
                component: () => import('@/views/admin/books/Form.vue'),
            },
            {
                path: 'books/:id/edit',
                name: 'admin-books-edit',
                component: () => import('@/views/admin/books/Form.vue'),
            },
            {
                path: 'visitors',
                name: 'admin-visitors',
                component: () => import('@/views/admin/visitors/Index.vue'),
            },
            {
                path: 'visitors/create',
                name: 'admin-visitors-create',
                component: () => import('@/views/admin/visitors/Form.vue'),
            },
            {
                path: 'visitors/:id/edit',
                name: 'admin-visitors-edit',
                component: () => import('@/views/admin/visitors/Form.vue'),
            },
            {
                path: 'visit-logs',
                name: 'admin-visit-logs',
                component: () => import('@/views/admin/visit-logs/Index.vue'),
            },
        ],
    },

    // 404
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/NotFound.vue'),
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Navigation guard
router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore()

    if (!auth.checked) {
        await auth.fetchUser()
    }

    if (to.meta.requiresAuth && !auth.user) {
        return next({ name: 'login' })
    }

    if (to.meta.guestOnly && auth.user) {
        return next({ name: 'admin-dashboard' })
    }

    next()
})

export default router
