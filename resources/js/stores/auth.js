import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
    const user    = ref(null)
    const checked = ref(false)

    async function fetchUser() {
        try {
            const { data } = await axios.get('/user')
            user.value = data
        } catch {
            user.value = null
        } finally {
            checked.value = true
        }
    }

    async function login(email, password) {
        await axios.get('/sanctum/csrf-cookie', { baseURL: '/' })
        const { data } = await axios.post('/login', { email, password })
        user.value = data.user
    }

    async function logout() {
        await axios.post('/logout')
        user.value = null
    }

    return { user, checked, fetchUser, login, logout }
})
