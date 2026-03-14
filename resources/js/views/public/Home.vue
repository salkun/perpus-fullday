<template>
    <div>

        <!-- Hero -->
        <section class="relative overflow-hidden px-4 pt-16 pb-20">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-indigo-600/6 rounded-full blur-3xl"></div>
                <div class="absolute top-20 left-10 w-64 h-64 bg-violet-600/5 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 opacity-[0.03]"
                    style="background-image: linear-gradient(#818cf8 1px, transparent 1px), linear-gradient(to right, #818cf8 1px, transparent 1px); background-size: 40px 40px;"></div>
            </div>

            <div class="max-w-6xl mx-auto relative z-10">
                <div class="max-w-3xl mx-auto text-center">

                    <div class="inline-flex items-center gap-2 bg-indigo-600/10 border border-indigo-500/20 rounded-full px-4 py-1.5 mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                        <span class="text-indigo-300 text-xs font-medium tracking-wide">Perpustakaan Digital</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-tight mb-5">
                        Ribuan buku,<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">satu tempat</span>
                    </h1>

                    <p class="text-slate-400 text-base sm:text-lg leading-relaxed mb-10 max-w-xl mx-auto">
                        Jelajahi koleksi buku kami dan baca langsung di browser — kapan saja, tanpa perlu mengunduh.
                    </p>

                    <!-- Search bar -->
                    <div class="relative max-w-xl mx-auto mb-5">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" />
                            </svg>
                        </span>
                        <input
                            v-model="heroSearch"
                            type="text"
                            placeholder="Cari judul atau pengarang..."
                            class="w-full bg-slate-900/80 backdrop-blur border border-slate-700 focus:border-indigo-500 rounded-2xl pl-12 pr-28 py-4 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition"
                            @keyup.enter="goSearch"
                        />
                        <button
                            @click="goSearch"
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-5 py-2 rounded-xl transition shadow-lg shadow-indigo-600/20"
                        >
                            Cari
                        </button>
                    </div>

                    <RouterLink :to="{ name: 'books' }" class="text-slate-500 hover:text-slate-300 text-sm transition">
                        atau lihat semua koleksi →
                    </RouterLink>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="px-4 pb-14">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-3 divide-x divide-slate-800 max-w-sm mx-auto sm:max-w-md">
                    <div class="text-center px-4 sm:px-8">
                        <p class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <span v-if="loadingStats" class="inline-block w-10 h-7 bg-slate-800 rounded animate-pulse"></span>
                            <span v-else>{{ stats.total_books }}</span>
                        </p>
                        <p class="text-slate-500 text-xs">Koleksi Buku</p>
                    </div>
                    <div class="text-center px-4 sm:px-8">
                        <p class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <span v-if="loadingStats" class="inline-block w-10 h-7 bg-slate-800 rounded animate-pulse"></span>
                            <span v-else>{{ stats.total_visitors }}</span>
                        </p>
                        <p class="text-slate-500 text-xs">Anggota</p>
                    </div>
                    <div class="text-center px-4 sm:px-8">
                        <p class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            <span v-if="loadingStats" class="inline-block w-10 h-7 bg-slate-800 rounded animate-pulse"></span>
                            <span v-else>{{ stats.today }}</span>
                        </p>
                        <p class="text-slate-500 text-xs">Kunjungan Hari Ini</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kategori -->
        <section v-if="categories.length" class="px-4 pb-14">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-white font-semibold text-lg">Telusuri Kategori</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <RouterLink
                        v-for="cat in categories" :key="cat"
                        :to="{ name: 'books', query: { category: cat } }"
                        class="group flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-indigo-500/40 text-slate-400 hover:text-indigo-300 rounded-xl text-sm transition duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        {{ cat }}
                    </RouterLink>
                </div>
            </div>
        </section>

        <!-- Buku terbaru -->
        <section class="px-4 pb-20">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-white font-semibold text-lg">Buku Terbaru</h2>
                        <p class="text-slate-500 text-xs mt-0.5">Koleksi yang baru ditambahkan</p>
                    </div>
                    <RouterLink :to="{ name: 'books' }"
                        class="inline-flex items-center gap-1 text-indigo-400 hover:text-indigo-300 text-sm transition">
                        Lihat semua
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </RouterLink>
                </div>

                <!-- Loading -->
                <div v-if="loadingBooks" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-5">
                    <div v-for="i in 10" :key="i" class="space-y-3">
                        <div class="aspect-[3/4] rounded-2xl bg-slate-800 animate-pulse"></div>
                        <div class="space-y-2">
                            <div class="h-3 bg-slate-800 rounded animate-pulse"></div>
                            <div class="h-3 w-2/3 bg-slate-800 rounded animate-pulse"></div>
                        </div>
                    </div>
                </div>

                <!-- Grid -->
                <div v-else-if="latestBooks.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-5">
                    <RouterLink
                        v-for="book in latestBooks" :key="book.id"
                        :to="{ name: 'book-detail', params: { id: book.id } }"
                        class="group block"
                    >
                        <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-slate-800 mb-3 relative shadow-lg">
                            <img v-if="book.cover_url" :src="book.cover_url" :alt="book.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                            <div v-else class="w-full h-full flex flex-col items-center justify-center p-4"
                                :style="{ background: placeholderGradient(book.id) }">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white/20 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                                <p class="text-white/40 text-xs text-center line-clamp-3 leading-snug">{{ book.title }}</p>
                            </div>

                            <span class="absolute top-2.5 left-2.5 bg-black/50 backdrop-blur-sm text-white/80 text-xs px-2 py-0.5 rounded-full">
                                {{ book.category }}
                            </span>
                            <span v-if="book.pdf_url" class="absolute top-2.5 right-2.5 bg-indigo-600/80 backdrop-blur-sm text-white text-xs px-2 py-0.5 rounded-full">
                                PDF
                            </span>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex items-end p-3">
                                <span class="w-full inline-flex items-center justify-center gap-1.5 bg-indigo-600 text-white text-xs font-semibold px-3 py-2 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    Lihat Detail
                                </span>
                            </div>
                        </div>

                        <p class="text-slate-200 text-sm font-medium leading-snug line-clamp-2 group-hover:text-white transition mb-1">
                            {{ book.title }}
                        </p>
                        <p class="text-slate-500 text-xs">{{ book.author }}</p>
                    </RouterLink>
                </div>

                <!-- Empty -->
                <div v-else class="text-center py-20">
                    <div class="w-16 h-16 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <p class="text-slate-500 text-sm">Belum ada koleksi buku.</p>
                </div>
            </div>
        </section>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import axios from 'axios'

const router       = useRouter()
const loadingStats = ref(true)
const loadingBooks = ref(true)
const heroSearch   = ref('')
const stats        = ref({ total_books: 0, total_visitors: 0, today: 0 })
const latestBooks  = ref([])
const categories   = ref([])

const gradients = [
    'linear-gradient(135deg, #1e3a5f, #2d6a9f)',
    'linear-gradient(135deg, #1a3a2a, #2d6a4f)',
    'linear-gradient(135deg, #3a1a2a, #6a2d4f)',
    'linear-gradient(135deg, #2a1a3a, #4f2d6a)',
    'linear-gradient(135deg, #3a2a1a, #6a4f2d)',
    'linear-gradient(135deg, #1a2a3a, #2d4f6a)',
]

function placeholderGradient(id) {
    return gradients[id % gradients.length]
}

function goSearch() {
    if (heroSearch.value.trim()) {
        router.push({ name: 'books', query: { search: heroSearch.value.trim() } })
    }
}

onMounted(async () => {
    try {
        const [summaryRes, booksRes, catsRes] = await Promise.all([
            axios.get('/api/visit-logs/summary'),
            axios.get('/api/books', { params: { per_page: 10 } }),
            axios.get('/api/books/categories'),
        ])
        stats.value       = summaryRes.data
        latestBooks.value = booksRes.data.data
        categories.value  = catsRes.data
    } catch (e) {
        console.error(e)
    } finally {
        loadingStats.value = false
        loadingBooks.value = false
    }
})
</script>