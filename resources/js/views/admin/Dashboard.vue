<template>
    <div class="space-y-6">

        <!-- Greeting -->
        <div>
            <h2 class="text-white text-xl font-semibold">Selamat datang, {{ auth.user?.name }} 👋</h2>
            <p class="text-slate-400 text-sm mt-1">{{ today }}</p>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
            <div
                v-for="stat in stats"
                :key="stat.label"
                class="bg-slate-900 border border-slate-800 rounded-2xl p-4 lg:p-5"
            >
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0', stat.iconBg]">
                        <span class="w-5 h-5" v-html="stat.icon"></span>
                    </div>
                    <span v-if="stat.badge" :class="['text-xs font-medium px-2 py-0.5 rounded-full', stat.badgeClass]">
                        {{ stat.badge }}
                    </span>
                </div>
                <p class="text-2xl font-bold text-white">
                    <span v-if="loading">
                        <span class="inline-block w-12 h-6 bg-slate-800 rounded animate-pulse"></span>
                    </span>
                    <span v-else>{{ stat.value }}</span>
                </p>
                <p class="text-slate-400 text-xs mt-1">{{ stat.label }}</p>
            </div>
        </div>

        <!-- Recent visit logs -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-800">
                <h3 class="text-white font-semibold text-sm">Kunjungan Hari Ini</h3>
                <RouterLink
                    :to="{ name: 'admin-visit-logs' }"
                    class="text-indigo-400 text-xs hover:text-indigo-300 transition"
                >
                    Lihat semua →
                </RouterLink>
            </div>

            <!-- Loading skeleton -->
            <div v-if="loading" class="divide-y divide-slate-800">
                <div v-for="i in 4" :key="i" class="px-5 py-3.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-slate-800 animate-pulse shrink-0"></div>
                    <div class="flex-1 space-y-1.5">
                        <div class="w-32 h-3 bg-slate-800 rounded animate-pulse"></div>
                        <div class="w-20 h-2.5 bg-slate-800 rounded animate-pulse"></div>
                    </div>
                    <div class="w-16 h-2.5 bg-slate-800 rounded animate-pulse"></div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else-if="recentLogs.length === 0" class="py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
                <p class="text-slate-500 text-sm">Belum ada kunjungan hari ini</p>
                <RouterLink :to="{ name: 'admin-visit-logs' }" class="inline-block mt-3 text-indigo-400 text-sm hover:text-indigo-300 transition">
                    Catat kunjungan →
                </RouterLink>
            </div>

            <!-- Log list -->
            <div v-else class="divide-y divide-slate-800">
                <div
                    v-for="log in recentLogs"
                    :key="log.id"
                    class="px-5 py-3.5 flex items-center gap-3"
                >
                    <div class="w-8 h-8 rounded-full bg-indigo-600/20 flex items-center justify-center text-indigo-400 text-sm font-semibold shrink-0">
                        {{ log.visitor?.name?.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-slate-200 text-sm font-medium truncate">{{ log.visitor?.name }}</p>
                        <p class="text-slate-500 text-xs truncate">{{ log.purpose || 'Tidak ada keterangan' }}</p>
                    </div>
                    <span class="text-slate-500 text-xs shrink-0">{{ formatTime(log.visited_at) }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const auth    = useAuthStore()
const loading = ref(true)
const summary = ref({ today: 0, this_month: 0, total: 0, total_books: 0, total_visitors: 0 })
const recentLogs = ref([])

const today = computed(() => {
    return new Date().toLocaleDateString('id-ID', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    })
})

const stats = computed(() => [
    {
        label: 'Total Buku',
        value: summary.value.total_books,
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#818cf8" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>`,
        iconBg: 'bg-indigo-600/15',
    },
    {
        label: 'Total Anggota',
        value: summary.value.total_visitors,
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#34d399" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>`,
        iconBg: 'bg-emerald-600/15',
    },
    {
        label: 'Kunjungan Hari Ini',
        value: summary.value.today,
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#fb923c" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>`,
        iconBg: 'bg-orange-600/15',
        badge: 'Hari ini',
        badgeClass: 'bg-orange-600/15 text-orange-400',
    },
    {
        label: 'Kunjungan Bulan Ini',
        value: summary.value.this_month,
        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#a78bfa" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>`,
        iconBg: 'bg-violet-600/15',
    },
])

function formatTime(datetime) {
    return new Date(datetime).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

onMounted(async () => {
    try {
        const [summaryRes, logsRes] = await Promise.all([
            axios.get('/api/visit-logs/summary'),
            axios.get('/api/visit-logs', { params: { per_page: 8 } }),
        ])
        summary.value    = summaryRes.data
        recentLogs.value = logsRes.data.data
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
})
</script>