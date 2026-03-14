<template>
    <div class="max-w-4xl mx-auto px-4 py-10">

        <!-- Back -->
        <RouterLink
            :to="{ name: 'books' }"
            class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm transition mb-8"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Kembali ke koleksi
        </RouterLink>

        <!-- Loading skeleton -->
        <div v-if="loading" class="flex flex-col sm:flex-row gap-8">
            <div class="w-full sm:w-48 aspect-[3/4] rounded-2xl bg-slate-800 animate-pulse shrink-0"></div>
            <div class="flex-1 space-y-4">
                <div class="h-7 w-3/4 bg-slate-800 rounded animate-pulse"></div>
                <div class="h-4 w-1/3 bg-slate-800 rounded animate-pulse"></div>
                <div class="space-y-2 pt-4">
                    <div class="h-3 bg-slate-800 rounded animate-pulse"></div>
                    <div class="h-3 bg-slate-800 rounded animate-pulse"></div>
                    <div class="h-3 w-2/3 bg-slate-800 rounded animate-pulse"></div>
                </div>
            </div>
        </div>

        <!-- Not found -->
        <div v-else-if="!book" class="text-center py-20">
            <p class="text-slate-500">Buku tidak ditemukan.</p>
            <RouterLink :to="{ name: 'books' }" class="mt-3 inline-block text-indigo-400 hover:text-indigo-300 text-sm transition">
                Kembali ke koleksi →
            </RouterLink>
        </div>

        <!-- Detail -->
        <div v-else class="flex flex-col sm:flex-row gap-8">

            <!-- Cover -->
            <div class="shrink-0 w-full sm:w-48">
                <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-slate-800 shadow-2xl">
                    <img
                        v-if="book.cover_url"
                        :src="book.cover_url"
                        :alt="book.title"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-700 to-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                </div>

                <!-- Tombol baca -->
                <RouterLink
                    v-if="book.pdf_url"
                    :to="{ name: 'book-read', params: { id: book.id } }"
                    class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-indigo-600/25 text-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    Baca Sekarang
                </RouterLink>
                <div v-else class="mt-4 w-full flex items-center justify-center gap-2 bg-slate-800 text-slate-500 font-medium py-3 rounded-xl text-sm cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    PDF Tidak Tersedia
                </div>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
                <!-- Kategori badge -->
                <span class="inline-block bg-indigo-600/15 text-indigo-300 text-xs font-medium px-3 py-1 rounded-full mb-3">
                    {{ book.category }}
                </span>

                <h1 class="text-white text-2xl sm:text-3xl font-bold leading-tight mb-2">
                    {{ book.title }}
                </h1>
                <p class="text-slate-400 text-base mb-6">{{ book.author }}</p>

                <!-- Meta info -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div v-if="book.publisher" class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-3">
                        <p class="text-slate-500 text-xs mb-1">Penerbit</p>
                        <p class="text-slate-200 text-sm font-medium">{{ book.publisher }}</p>
                    </div>
                    <div v-if="book.year" class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-3">
                        <p class="text-slate-500 text-xs mb-1">Tahun Terbit</p>
                        <p class="text-slate-200 text-sm font-medium">{{ book.year }}</p>
                    </div>
                    <div v-if="book.isbn" class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-3">
                        <p class="text-slate-500 text-xs mb-1">ISBN</p>
                        <p class="text-slate-200 text-sm font-medium font-mono">{{ book.isbn }}</p>
                    </div>
                    <div class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-3">
                        <p class="text-slate-500 text-xs mb-1">Stok</p>
                        <p class="text-sm font-medium" :class="book.stock > 0 ? 'text-emerald-400' : 'text-red-400'">
                            {{ book.stock > 0 ? `${book.stock} tersedia` : 'Habis' }}
                        </p>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div v-if="book.description">
                    <h2 class="text-slate-300 text-sm font-semibold mb-2">Deskripsi</h2>
                    <p class="text-slate-400 text-sm leading-relaxed">{{ book.description }}</p>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route  = useRoute()
const router = useRouter()
const book   = ref(null)
const loading = ref(true)

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/books/${route.params.id}`)
        book.value = data
    } catch {
        book.value = null
    } finally {
        loading.value = false
    }
})
</script>