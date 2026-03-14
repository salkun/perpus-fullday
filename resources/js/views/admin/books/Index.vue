<template>
    <div class="space-y-5">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-white font-semibold text-lg">Daftar Buku</h2>
                <p class="text-slate-500 text-sm mt-0.5">Total {{ meta.total ?? 0 }} buku</p>
            </div>
            <RouterLink
                :to="{ name: 'admin-books-create' }"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/20"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Buku
            </RouterLink>
        </div>

        <!-- Filter & Search -->
        <div class="flex flex-col sm:flex-row gap-3">
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
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
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

        <!-- Tabel -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">

            <!-- Loading skeleton -->
            <div v-if="loading">
                <div class="divide-y divide-slate-800">
                    <div v-for="i in 6" :key="i" class="flex items-center gap-4 px-5 py-4">
                        <div class="w-10 h-14 rounded-lg bg-slate-800 animate-pulse shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3.5 w-48 bg-slate-800 rounded animate-pulse"></div>
                            <div class="h-3 w-32 bg-slate-800 rounded animate-pulse"></div>
                        </div>
                        <div class="h-3 w-20 bg-slate-800 rounded animate-pulse hidden sm:block"></div>
                        <div class="h-6 w-16 bg-slate-800 rounded-full animate-pulse hidden md:block"></div>
                        <div class="flex gap-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-800 animate-pulse"></div>
                            <div class="w-8 h-8 rounded-lg bg-slate-800 animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else-if="books.length === 0" class="py-16 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                <p class="text-slate-500 text-sm">Belum ada buku.</p>
                <RouterLink :to="{ name: 'admin-books-create' }" class="inline-block mt-3 text-indigo-400 text-sm hover:text-indigo-300 transition">
                    Tambah buku pertama →
                </RouterLink>
            </div>

            <!-- Table -->
            <div v-else>
                <!-- Desktop table header -->
                <div class="hidden md:grid grid-cols-[auto_1fr_160px_100px_80px_96px] gap-4 px-5 py-3 border-b border-slate-800 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <span>Cover</span>
                    <span>Buku</span>
                    <span>Pengarang</span>
                    <span>Kategori</span>
                    <span class="text-center">Stok</span>
                    <span class="text-right">Aksi</span>
                </div>

                <div class="divide-y divide-slate-800">
                    <div
                        v-for="book in books"
                        :key="book.id"
                        class="flex md:grid md:grid-cols-[auto_1fr_160px_100px_80px_96px] items-center gap-4 px-5 py-4 hover:bg-slate-800/40 transition"
                    >
                        <!-- Cover -->
                        <div class="w-10 h-14 rounded-lg overflow-hidden bg-slate-800 shrink-0">
                            <img v-if="book.cover_url" :src="book.cover_url" :alt="book.title" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                        </div>

                        <!-- Judul & ISBN -->
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-200 text-sm font-medium leading-snug line-clamp-2">{{ book.title }}</p>
                            <p v-if="book.isbn" class="text-slate-500 text-xs mt-0.5">ISBN: {{ book.isbn }}</p>
                            <!-- Mobile: author & category -->
                            <div class="flex items-center gap-2 mt-1 md:hidden">
                                <span class="text-slate-400 text-xs">{{ book.author }}</span>
                                <span class="text-slate-600">·</span>
                                <span class="bg-slate-800 text-slate-400 text-xs px-2 py-0.5 rounded-full">{{ book.category }}</span>
                            </div>
                        </div>

                        <!-- Pengarang (desktop) -->
                        <p class="text-slate-400 text-sm hidden md:block truncate">{{ book.author }}</p>

                        <!-- Kategori (desktop) -->
                        <div class="hidden md:block">
                            <span class="bg-slate-800 text-slate-300 text-xs px-2.5 py-1 rounded-full">{{ book.category }}</span>
                        </div>

                        <!-- Stok (desktop) -->
                        <p class="text-slate-300 text-sm text-center hidden md:block">{{ book.stock }}</p>

                        <!-- Aksi -->
                        <div class="flex items-center gap-2 shrink-0 md:justify-end ml-auto md:ml-0">
                            <RouterLink
                                :to="{ name: 'admin-books-edit', params: { id: book.id } }"
                                class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-400 hover:text-white transition"
                                title="Edit"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                </svg>
                            </RouterLink>
                            <button
                                @click="confirmDelete(book)"
                                class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-red-500/15 flex items-center justify-center text-slate-400 hover:text-red-400 transition"
                                title="Hapus"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="meta.last_page > 1" class="flex items-center justify-between">
            <p class="text-slate-500 text-sm">
                Halaman {{ meta.current_page }} dari {{ meta.last_page }}
            </p>
            <div class="flex gap-2">
                <button
                    @click="fetchBooks(meta.current_page - 1)"
                    :disabled="meta.current_page === 1"
                    class="px-3 py-2 rounded-lg text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
                >
                    ← Sebelumnya
                </button>
                <button
                    @click="fetchBooks(meta.current_page + 1)"
                    :disabled="meta.current_page === meta.last_page"
                    class="px-3 py-2 rounded-lg text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition"
                >
                    Berikutnya →
                </button>
            </div>
        </div>

        <!-- Modal konfirmasi delete -->
        <div v-if="deleteModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                <div class="w-12 h-12 rounded-full bg-red-500/15 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-white font-semibold text-center mb-1">Hapus Buku?</h3>
                <p class="text-slate-400 text-sm text-center mb-6">
                    "<span class="text-slate-200">{{ deleteTarget?.title }}</span>" akan dihapus permanen beserta file cover dan PDF-nya.
                </p>
                <div class="flex gap-3">
                    <button
                        @click="deleteModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition"
                    >
                        Batal
                    </button>
                    <button
                        @click="deleteBook"
                        :disabled="deleting"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-red-600 hover:bg-red-500 disabled:opacity-60 text-white transition"
                    >
                        {{ deleting ? 'Menghapus...' : 'Hapus' }}
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import axios from 'axios'

const books          = ref([])
const categories     = ref([])
const meta           = ref({})
const loading        = ref(true)
const search         = ref('')
const filterCategory = ref('')
const deleteModal    = ref(false)
const deleteTarget   = ref(null)
const deleting       = ref(false)

let searchTimeout = null

async function fetchBooks(page = 1) {
    loading.value = true
    try {
        const { data } = await axios.get('/api/books', {
            params: {
                page,
                per_page: 15,
                search: search.value || undefined,
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
    } catch (e) {
        console.error(e)
    }
}

function onSearchInput() {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => fetchBooks(1), 400)
}

function confirmDelete(book) {
    deleteTarget.value = book
    deleteModal.value  = true
}

async function deleteBook() {
    if (!deleteTarget.value) return
    deleting.value = true
    try {
        await axios.delete(`/api/books/${deleteTarget.value.id}`)
        deleteModal.value = false
        await fetchBooks(meta.value.current_page)
    } catch (e) {
        console.error(e)
    } finally {
        deleting.value = false
    }
}

onMounted(() => {
    fetchBooks()
    fetchCategories()
})
</script>
