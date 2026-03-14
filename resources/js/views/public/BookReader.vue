<template>
    <div class="min-h-screen bg-slate-950 flex flex-col">

        <!-- Topbar reader -->
        <div class="h-14 bg-slate-900 border-b border-slate-800 flex items-center px-4 gap-3 sticky top-0 z-20">
            <RouterLink
                :to="{ name: 'book-detail', params: { id: $route.params.id } }"
                class="text-slate-400 hover:text-white transition shrink-0"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </RouterLink>

            <p class="text-white text-sm font-medium truncate flex-1">{{ book?.title }}</p>

            <!-- Navigasi halaman -->
            <div class="flex items-center gap-2 shrink-0">
                <button
                    @click="prevPage"
                    :disabled="currentPage <= 1"
                    class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center text-slate-300 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>

                <div class="flex items-center gap-1.5">
                    <input
                        v-model.number="inputPage"
                        type="number"
                        :min="1"
                        :max="totalPages"
                        @keyup.enter="goToPage"
                        @blur="goToPage"
                        class="w-12 bg-slate-800 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs text-center focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    />
                    <span class="text-slate-500 text-xs">/ {{ totalPages || '—' }}</span>
                </div>

                <button
                    @click="nextPage"
                    :disabled="currentPage >= totalPages"
                    class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center text-slate-300 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>

                <!-- Zoom -->
                <div class="hidden sm:flex items-center gap-1 ml-2">
                    <button @click="zoomOut" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-300 transition text-sm font-bold">−</button>
                    <span class="text-slate-500 text-xs w-10 text-center">{{ Math.round(scale * 100) }}%</span>
                    <button @click="zoomIn" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-300 transition text-sm font-bold">+</button>
                </div>
            </div>
        </div>

        <!-- PDF Viewer area -->
        <div class="flex-1 overflow-auto bg-slate-950 flex flex-col items-center py-6 px-4" ref="viewerContainer">

            <!-- Loading book info -->
            <div v-if="loadingBook" class="flex items-center justify-center h-64">
                <div class="text-center">
                    <svg class="animate-spin w-8 h-8 text-indigo-400 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <p class="text-slate-400 text-sm">Memuat buku...</p>
                </div>
            </div>

            <!-- No PDF -->
            <div v-else-if="!book?.pdf_url" class="flex items-center justify-center h-64">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <p class="text-slate-500 text-sm">File PDF tidak tersedia.</p>
                    <RouterLink :to="{ name: 'book-detail', params: { id: $route.params.id } }" class="mt-3 inline-block text-indigo-400 hover:text-indigo-300 text-sm transition">
                        Kembali ke detail buku
                    </RouterLink>
                </div>
            </div>

            <!-- Canvas PDF -->
            <div v-else>
                <!-- Loading page -->
                <div v-if="renderingPage" class="flex items-center justify-center mb-4">
                    <div class="flex items-center gap-2 text-slate-400 text-sm">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Memuat halaman...
                    </div>
                </div>

                <div class="shadow-2xl shadow-black/50 rounded-lg overflow-hidden">
                    <canvas ref="pdfCanvas" class="block max-w-full"></canvas>
                </div>

                <!-- Navigasi bawah -->
                <div class="flex items-center justify-center gap-4 mt-6">
                    <button
                        @click="prevPage"
                        :disabled="currentPage <= 1"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed text-slate-300 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                        Sebelumnya
                    </button>
                    <span class="text-slate-500 text-sm">{{ currentPage }} / {{ totalPages }}</span>
                    <button
                        @click="nextPage"
                        :disabled="currentPage >= totalPages"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed text-slate-300 transition"
                    >
                        Berikutnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import axios from 'axios'

const route           = useRoute()
const book            = ref(null)
const loadingBook     = ref(true)
const pdfCanvas       = ref(null)
const viewerContainer = ref(null)
const currentPage     = ref(1)
const inputPage       = ref(1)
const totalPages      = ref(0)
const scale           = ref(1.2)
const renderingPage   = ref(false)

let pdfDoc = null

// Load PDF.js dari CDN
function loadPdfJs() {
    return new Promise((resolve, reject) => {
        if (window.pdfjsLib) return resolve(window.pdfjsLib)
        const script = document.createElement('script')
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js'
        script.onload = () => {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js'
            resolve(window.pdfjsLib)
        }
        script.onerror = reject
        document.head.appendChild(script)
    })
}

async function loadPdf(pdfUrl) {
    try {
        const pdfjsLib = await loadPdfJs()
        pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise
        totalPages.value = pdfDoc.numPages
        await renderPage(1)
    } catch (e) {
        console.error('Gagal load PDF:', e)
    }
}

async function renderPage(pageNum) {
    if (!pdfDoc || !pdfCanvas.value) return
    renderingPage.value = true
    try {
        const page     = await pdfDoc.getPage(pageNum)
        const viewport = page.getViewport({ scale: scale.value })
        const canvas   = pdfCanvas.value
        const ctx      = canvas.getContext('2d')

        canvas.height = viewport.height
        canvas.width  = viewport.width

        await page.render({ canvasContext: ctx, viewport }).promise

        currentPage.value = pageNum
        inputPage.value   = pageNum
    } catch (e) {
        console.error(e)
    } finally {
        renderingPage.value = false
    }
}

function prevPage() {
    if (currentPage.value > 1) renderPage(currentPage.value - 1)
}

function nextPage() {
    if (currentPage.value < totalPages.value) renderPage(currentPage.value + 1)
}

function goToPage() {
    const p = Math.max(1, Math.min(totalPages.value, inputPage.value || 1))
    inputPage.value = p
    if (p !== currentPage.value) renderPage(p)
}

function zoomIn() {
    if (scale.value < 3) {
        scale.value = Math.round((scale.value + 0.2) * 10) / 10
        renderPage(currentPage.value)
    }
}

function zoomOut() {
    if (scale.value > 0.5) {
        scale.value = Math.round((scale.value - 0.2) * 10) / 10
        renderPage(currentPage.value)
    }
}

// Keyboard navigation
function handleKeydown(e) {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') nextPage()
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   prevPage()
}

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/books/${route.params.id}`)
        book.value = data
        if (data.pdf_url) {
            await loadPdf(data.pdf_url)
        }
    } catch (e) {
        console.error(e)
    } finally {
        loadingBook.value = false
    }

    window.addEventListener('keydown', handleKeydown)
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown)
})
</script>