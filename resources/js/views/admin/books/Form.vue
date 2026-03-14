<template>
    <div class="max-w-2xl space-y-6">

        <!-- Back -->
        <RouterLink
            :to="{ name: 'admin-books' }"
            class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm transition"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Kembali ke daftar buku
        </RouterLink>

        <!-- Form card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
            <h2 class="text-white font-semibold text-base mb-6">
                {{ isEdit ? 'Edit Buku' : 'Tambah Buku Baru' }}
            </h2>

            <!-- Loading saat fetch data edit -->
            <div v-if="loadingData" class="space-y-4">
                <div v-for="i in 5" :key="i" class="space-y-1.5">
                    <div class="h-3 w-24 bg-slate-800 rounded animate-pulse"></div>
                    <div class="h-10 bg-slate-800 rounded-xl animate-pulse"></div>
                </div>
            </div>

            <div v-else class="space-y-5">

                <!-- Judul -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Judul Buku <span class="text-red-400">*</span>
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="Masukkan judul buku"
                        :class="inputClass('title')"
                    />
                    <p v-if="errors.title" class="text-red-400 text-xs mt-1.5">{{ errors.title[0] }}</p>
                </div>

                <!-- Pengarang & Penerbit -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Pengarang <span class="text-red-400">*</span>
                        </label>
                        <input
                            v-model="form.author"
                            type="text"
                            placeholder="Nama pengarang"
                            :class="inputClass('author')"
                        />
                        <p v-if="errors.author" class="text-red-400 text-xs mt-1.5">{{ errors.author[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Penerbit</label>
                        <input
                            v-model="form.publisher"
                            type="text"
                            placeholder="Nama penerbit"
                            :class="inputClass('publisher')"
                        />
                    </div>
                </div>

                <!-- Kategori, Tahun, ISBN -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Kategori <span class="text-red-400">*</span>
                        </label>
                        <input
                            v-model="form.category"
                            type="text"
                            placeholder="Contoh: Fiksi"
                            list="category-list"
                            :class="inputClass('category')"
                        />
                        <datalist id="category-list">
                            <option v-for="cat in categories" :key="cat" :value="cat" />
                        </datalist>
                        <p v-if="errors.category" class="text-red-400 text-xs mt-1.5">{{ errors.category[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Terbit</label>
                        <input
                            v-model="form.year"
                            type="number"
                            placeholder="2024"
                            :min="1900"
                            :max="new Date().getFullYear()"
                            :class="inputClass('year')"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">ISBN</label>
                        <input
                            v-model="form.isbn"
                            type="text"
                            placeholder="978-xxx-xxx"
                            :class="inputClass('isbn')"
                        />
                        <p v-if="errors.isbn" class="text-red-400 text-xs mt-1.5">{{ errors.isbn[0] }}</p>
                    </div>
                </div>

                <!-- Stok -->
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Stok</label>
                    <input
                        v-model="form.stock"
                        type="number"
                        min="0"
                        placeholder="1"
                        :class="inputClass('stock')"
                    />
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Deskripsi</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        placeholder="Deskripsi singkat tentang buku..."
                        class="w-full bg-slate-800/50 border border-slate-700 hover:border-slate-600 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
                    ></textarea>
                </div>

                <!-- Upload Cover -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Cover Buku
                        <span class="text-slate-500 font-normal ml-1">(JPG, PNG, WEBP — maks. 2MB)</span>
                    </label>

                    <!-- Preview cover -->
                    <div v-if="coverPreview || (isEdit && form.existing_cover)" class="mb-3">
                        <div class="relative inline-block">
                            <img
                                :src="coverPreview || form.existing_cover"
                                alt="Cover preview"
                                class="w-24 h-32 object-cover rounded-xl border border-slate-700"
                            />
                            <button
                                type="button"
                                @click="removeCover"
                                class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-400 text-white flex items-center justify-center transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label
                        class="flex items-center gap-3 bg-slate-800/50 border border-dashed border-slate-600 hover:border-indigo-500 rounded-xl px-4 py-4 cursor-pointer transition group"
                        @dragover.prevent
                        @drop.prevent="onDropCover"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-500 group-hover:text-indigo-400 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        <span class="text-slate-400 text-sm group-hover:text-slate-300 transition">
                            Klik atau drag file ke sini
                        </span>
                        <input type="file" accept="image/*" class="hidden" @change="onCoverChange" ref="coverInput" />
                    </label>
                    <p v-if="errors.cover_image" class="text-red-400 text-xs mt-1.5">{{ errors.cover_image[0] }}</p>
                </div>

                <!-- Upload PDF -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        File PDF
                        <span class="text-slate-500 font-normal ml-1">(PDF — maks. 50MB)</span>
                    </label>

                    <!-- PDF existing info -->
                    <div v-if="isEdit && form.existing_pdf && !pdfFile" class="flex items-center gap-3 bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="text-slate-300 text-sm flex-1 truncate">PDF sudah ada</span>
                        <button type="button" @click="form.existing_pdf = null" class="text-slate-500 hover:text-red-400 transition text-xs">Ganti</button>
                    </div>

                    <!-- PDF new file info -->
                    <div v-else-if="pdfFile" class="flex items-center gap-3 bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="text-slate-300 text-sm flex-1 truncate">{{ pdfFile.name }}</span>
                        <button type="button" @click="removePdf" class="text-slate-500 hover:text-red-400 transition text-xs">Hapus</button>
                    </div>

                    <label
                        v-if="!pdfFile && !(isEdit && form.existing_pdf)"
                        class="flex items-center gap-3 bg-slate-800/50 border border-dashed border-slate-600 hover:border-indigo-500 rounded-xl px-4 py-4 cursor-pointer transition group"
                        @dragover.prevent
                        @drop.prevent="onDropPdf"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-500 group-hover:text-indigo-400 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        <span class="text-slate-400 text-sm group-hover:text-slate-300 transition">
                            Klik atau drag file PDF ke sini
                        </span>
                        <input type="file" accept=".pdf" class="hidden" @change="onPdfChange" ref="pdfInput" />
                    </label>
                    <p v-if="errors.pdf_file" class="text-red-400 text-xs mt-1.5">{{ errors.pdf_file[0] }}</p>
                </div>

                <!-- Error umum -->
                <div v-if="errorMessage" class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    {{ errorMessage }}
                </div>

                <!-- Tombol submit -->
                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
                    <RouterLink
                        :to="{ name: 'admin-books' }"
                        class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition text-center"
                    >
                        Batal
                    </RouterLink>
                    <button
                        @click="submit"
                        :disabled="submitting"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed text-white transition shadow-lg shadow-indigo-600/20"
                    >
                        <svg v-if="submitting" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ submitting ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Buku') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route  = useRoute()
const router = useRouter()

const isEdit     = computed(() => !!route.params.id)
const loadingData = ref(false)
const submitting  = ref(false)
const errors      = ref({})
const errorMessage = ref('')
const categories  = ref([])

const coverInput = ref(null)
const pdfInput   = ref(null)
const coverFile  = ref(null)
const pdfFile    = ref(null)
const coverPreview = ref(null)

const form = reactive({
    title:         '',
    author:        '',
    publisher:     '',
    year:          '',
    isbn:          '',
    category:      '',
    description:   '',
    stock:         1,
    existing_cover: null,
    existing_pdf:   null,
})

function inputClass(field) {
    return [
        'w-full bg-slate-800/50 border rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm',
        'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition',
        errors.value[field]
            ? 'border-red-500/50'
            : 'border-slate-700 hover:border-slate-600'
    ].join(' ')
}

// Cover handlers
function onCoverChange(e) {
    const file = e.target.files[0]
    if (file) setCover(file)
}
function onDropCover(e) {
    const file = e.dataTransfer.files[0]
    if (file && file.type.startsWith('image/')) setCover(file)
}
function setCover(file) {
    coverFile.value    = file
    coverPreview.value = URL.createObjectURL(file)
}
function removeCover() {
    coverFile.value         = null
    coverPreview.value      = null
    form.existing_cover     = null
    if (coverInput.value) coverInput.value.value = ''
}

// PDF handlers
function onPdfChange(e) {
    const file = e.target.files[0]
    if (file) pdfFile.value = file
}
function onDropPdf(e) {
    const file = e.dataTransfer.files[0]
    if (file && file.type === 'application/pdf') pdfFile.value = file
}
function removePdf() {
    pdfFile.value = null
    if (pdfInput.value) pdfInput.value.value = ''
}

async function submit() {
    submitting.value  = true
    errors.value      = {}
    errorMessage.value = ''

    const fd = new FormData()
    fd.append('title',       form.title)
    fd.append('author',      form.author)
    fd.append('publisher',   form.publisher || '')
    fd.append('year',        form.year || '')
    fd.append('isbn',        form.isbn || '')
    fd.append('category',    form.category)
    fd.append('description', form.description || '')
    fd.append('stock',       form.stock)
    if (coverFile.value) fd.append('cover_image', coverFile.value)
    if (pdfFile.value)   fd.append('pdf_file', pdfFile.value)

    try {
        if (isEdit.value) {
            await axios.post(`/api/books/${route.params.id}`, fd, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
        } else {
            await axios.post('/api/books', fd, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
        }
        router.push({ name: 'admin-books' })
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
        } else {
            errorMessage.value = 'Terjadi kesalahan. Coba lagi.'
        }
    } finally {
        submitting.value = false
    }
}

async function fetchCategories() {
    try {
        const { data } = await axios.get('/api/books/categories')
        categories.value = data
    } catch {}
}

async function fetchBook() {
    loadingData.value = true
    try {
        const { data } = await axios.get(`/api/books/${route.params.id}`)
        form.title          = data.title
        form.author         = data.author
        form.publisher      = data.publisher || ''
        form.year           = data.year || ''
        form.isbn           = data.isbn || ''
        form.category       = data.category
        form.description    = data.description || ''
        form.stock          = data.stock
        form.existing_cover = data.cover_url
        form.existing_pdf   = data.pdf_url
    } catch {
        router.push({ name: 'admin-books' })
    } finally {
        loadingData.value = false
    }
}

onMounted(() => {
    fetchCategories()
    if (isEdit.value) fetchBook()
})
</script>
