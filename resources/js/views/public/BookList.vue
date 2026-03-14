<template>
    <div class="max-w-6xl mx-auto px-4 py-10">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-white text-2xl font-bold">Koleksi Buku</h1>
            <p class="text-slate-400 text-sm mt-1">{{ meta.total ?? 0 }} buku tersedia</p>
        </div>

        <!-- Search & Filter -->
        <div class="flex flex-col sm:flex-row gap-3 mb-8">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" />
                    </svg>
                </span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari judul atau pengarang..."
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                    @input="onSearchInput"
                />
            </div>
            <select
                v-model="filterCategory"
                @change="fetchBooks(1)"
                class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
            >
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
        </div>

        <!-- Filter kategori chips -->
        <div v-if="categories.length" class="flex flex-wrap gap-2 mb-8">
            <button
                @click="filterCategory = ''; fetchBooks(1)"
                :class="[
                    'px-3 py-1.5 rounded-lg text-xs font-medium transition',
                    filterCategory === ''
                        ? 'bg-indigo-600 text-white'
                        : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700'
                ]"
            >
                Semua
            </button>
            <button
                v-for="cat in categories" :key="cat"
                @click="filterCategory = cat; fetchBooks(1)"
                :class="[
                    'px-3 py-1.5 rounded-lg text-xs font-medium transition',
                    filterCategory === cat
                        ? 'bg-indigo-600 text-white'
                        : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700'
                ]"
            >
                {{ cat }}
            </button>
        </div>

        <!-- Loading skeleton -->
        <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <div v-for="i in 10" :key="i" class="space-y-3">
                <div class="aspect-[3/4] rounded-xl bg-slate-800 animate-pulse"></div>
                <div class="space-y-2">
                    <div class="h-3 bg-slate-800 rounded animate-pulse"></div>
                    <div class="h-3 w-2/3 bg-slate-800 rounded animate-pulse"></div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else-if="books.length === 0" class="text-center py-20">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" />
            </svg>
            <p class="text-slate-500">Tidak ada buku yang ditemukan.</p>
            <button @click="search = ''; filterCategory = ''; fetchBooks(1)" class="mt-3 text-indigo-400 text-sm hover:text-indigo-300 transition">
                Reset pencarian
            </button>
        </div>

        <!-- Book grid -->
        <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <RouterLink
                v-for="book in books"
                :key="book.id"
                :to="{ name: 'book-detail', params: { id: book.id } }"
                class="group block"
            >
                <div class="aspect-[3/4] rounded-xl overflow-hidden bg-slate-800 mb-3 relative">
                    <img
                        v-if="book.cover_url"
                        :src="book.cover_url"
                        :alt="book.title"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                    />
                    <div v-else class="w-full h-full flex flex-col items-center justify-center p-4 bg-gradient-to-br from-slate-700 to-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <p class="text-slate-500 text-xs text-center line-clamp-2">{{ book.title }}</p>
                    </div>
                    <span class="absolute top-2 left-2 bg-slate-900/80 backdrop-blur text-slate-300 text-xs px-2 py-0.5 rounded-full">
                        {{ book.category }}
                    </span>
                    <div class="absolute inset-0 bg-slate-900/70 opacity-0 group-hover:opacity-100 transition duration-200 flex items-center justify-center">
                        <span class="bg-indigo-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                            Lihat Detail
                        </span>
                    </div>
                </div>
                <p class="text-slate-200 text-sm font-medium leading-snug line-clamp-2 group-hover:text-white transition">
                    {{ book.title }}
                </p>
                <p class="text-slate-500 text-xs mt-1">{{ book.author }}</p>
            </RouterLink>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-2 mt-10">
            <button
                @click="fetchBooks(meta.current_page - 1)"
                :disabled="meta.current_page === 1"
                class="px-4 py-2 rounded-xl text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
            >
                ← Sebelumnya
            </button>
            <span class="text-slate-500 text-sm px-2">
                {{ meta.current_page }} / {{ meta.last_page }}
            </span>
            <button
                @click="fetchBooks(meta.current_page + 1)"
                :disabled="meta.current_page === meta.last_page"
                class="px-4 py-2 rounded-xl text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
            >
                Berikutnya →
            </button>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import axios from 'axios'

const route          = useRoute()
const books          = ref([])
const categories     = ref([])
const meta           = ref({})
const loading        = ref(true)
const search         = ref('')
const filterCategory = ref(route.query.category || '')

let searchTimeout = null

async function fetchBooks(page = 1) {
    loading.value = true
    try {
        const { data } = await axios.get('/api/books', {
            params: {
                page,
                per_page: 20,
                search:   search.value || undefined,
                category: filterCategory.value || undefined,
            }
        })
        books.value = data.data
        meta.value  = {
            current_page: data.current_page,
            last_page:    data.last_page,
            total:        data.total,
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

async function fetchCategories() {
    try {
        const { data } = await axios.get('/api/books/categories')
        categories.value = data
    } catch {}
}

function onSearchInput() {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => fetchBooks(1), 400)
}

onMounted(() => {
    fetchBooks()
    fetchCategories()
})
</script>