<template>
    <div class="space-y-5">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-white font-semibold text-lg">Daftar Pengunjung</h2>
                <p class="text-slate-500 text-sm mt-0.5">Total {{ meta.total ?? 0 }} pengunjung terdaftar</p>
            </div>
            <RouterLink
                :to="{ name: 'admin-visitors-create' }"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/20"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Pengunjung
            </RouterLink>
        </div>

        <!-- Search -->
        <div class="relative max-w-sm">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" />
                </svg>
            </span>
            <input
                v-model="search"
                type="text"
                placeholder="Cari nama, ID anggota, email..."
                class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                @input="onSearchInput"
            />
        </div>

        <!-- Tabel -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">

            <!-- Loading -->
            <div v-if="loading" class="divide-y divide-slate-800">
                <div v-for="i in 6" :key="i" class="flex items-center gap-4 px-5 py-4">
                    <div class="w-9 h-9 rounded-full bg-slate-800 animate-pulse shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 w-36 bg-slate-800 rounded animate-pulse"></div>
                        <div class="h-3 w-24 bg-slate-800 rounded animate-pulse"></div>
                    </div>
                    <div class="h-3 w-20 bg-slate-800 rounded animate-pulse hidden sm:block"></div>
                    <div class="flex gap-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 animate-pulse"></div>
                        <div class="w-8 h-8 rounded-lg bg-slate-800 animate-pulse"></div>
                        <div class="w-8 h-8 rounded-lg bg-slate-800 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Empty -->
            <div v-else-if="visitors.length === 0" class="py-16 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                <p class="text-slate-500 text-sm">Belum ada pengunjung terdaftar.</p>
                <RouterLink :to="{ name: 'admin-visitors-create' }" class="inline-block mt-3 text-indigo-400 text-sm hover:text-indigo-300 transition">
                    Tambah pengunjung pertama →
                </RouterLink>
            </div>

            <!-- List -->
            <div v-else>
                <!-- Header desktop -->
                <div class="hidden md:grid grid-cols-[1fr_140px_160px_80px_96px] gap-4 px-5 py-3 border-b border-slate-800 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <span>Pengunjung</span>
                    <span>ID Anggota</span>
                    <span>Kontak</span>
                    <span class="text-center">Kunjungan</span>
                    <span class="text-right">Aksi</span>
                </div>

                <div class="divide-y divide-slate-800">
                    <div
                        v-for="visitor in visitors"
                        :key="visitor.id"
                        class="flex md:grid md:grid-cols-[1fr_140px_160px_80px_96px] items-center gap-4 px-5 py-4 hover:bg-slate-800/40 transition"
                    >
                        <!-- Avatar + nama -->
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-indigo-600/20 flex items-center justify-center text-indigo-400 font-semibold text-sm shrink-0">
                                {{ visitor.name.charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-slate-200 text-sm font-medium truncate">{{ visitor.name }}</p>
                                <p class="text-slate-500 text-xs">{{ visitor.occupation || 'Umum' }}</p>
                            </div>
                        </div>

                        <!-- ID Anggota -->
                        <div class="hidden md:block">
                            <span v-if="visitor.member_id" class="bg-slate-800 text-slate-300 text-xs px-2.5 py-1 rounded-full font-mono">
                                {{ visitor.member_id }}
                            </span>
                            <span v-else class="text-slate-600 text-xs">—</span>
                        </div>

                        <!-- Kontak -->
                        <div class="hidden md:block min-w-0">
                            <p v-if="visitor.phone" class="text-slate-400 text-sm truncate">{{ visitor.phone }}</p>
                            <p v-if="visitor.email" class="text-slate-500 text-xs truncate">{{ visitor.email }}</p>
                            <span v-if="!visitor.phone && !visitor.email" class="text-slate-600 text-xs">—</span>
                        </div>

                        <!-- Total kunjungan -->
                        <div class="hidden md:flex justify-center">
                            <span class="text-slate-300 text-sm font-medium">{{ visitor.visit_logs_count ?? 0 }}</span>
                        </div>

                        <!-- Aksi -->
                        <div class="flex items-center gap-2 shrink-0 ml-auto md:ml-0 md:justify-end">
                            <!-- Catat kunjungan -->
                            <button
                                @click="openLogModal(visitor)"
                                class="w-8 h-8 rounded-lg bg-emerald-600/15 hover:bg-emerald-600/25 flex items-center justify-center text-emerald-400 hover:text-emerald-300 transition"
                                title="Catat kunjungan"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                            <!-- Edit -->
                            <RouterLink
                                :to="{ name: 'admin-visitors-edit', params: { id: visitor.id } }"
                                class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-400 hover:text-white transition"
                                title="Edit"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                </svg>
                            </RouterLink>
                            <!-- Hapus -->
                            <button
                                @click="confirmDelete(visitor)"
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
            <p class="text-slate-500 text-sm">Halaman {{ meta.current_page }} dari {{ meta.last_page }}</p>
            <div class="flex gap-2">
                <button @click="fetchVisitors(meta.current_page - 1)" :disabled="meta.current_page === 1"
                    class="px-3 py-2 rounded-lg text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    ← Sebelumnya
                </button>
                <button @click="fetchVisitors(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page"
                    class="px-3 py-2 rounded-lg text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    Berikutnya →
                </button>
            </div>
        </div>

        <!-- Modal catat kunjungan -->
        <div v-if="logModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                <h3 class="text-white font-semibold mb-1">Catat Kunjungan</h3>
                <p class="text-slate-400 text-sm mb-5">
                    Pengunjung: <span class="text-slate-200">{{ logTarget?.name }}</span>
                </p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tujuan Kunjungan</label>
                        <input
                            v-model="logForm.purpose"
                            type="text"
                            placeholder="Baca buku, tugas, pinjam buku..."
                            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Catatan</label>
                        <textarea
                            v-model="logForm.notes"
                            rows="2"
                            placeholder="Catatan tambahan (opsional)"
                            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"
                        ></textarea>
                    </div>
                </div>

                <div v-if="logError" class="mt-3 text-red-400 text-xs">{{ logError }}</div>

                <div class="flex gap-3 mt-5">
                    <button @click="logModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                        Batal
                    </button>
                    <button @click="saveLog" :disabled="loggingVisit"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-emerald-600 hover:bg-emerald-500 disabled:opacity-60 text-white transition">
                        {{ loggingVisit ? 'Menyimpan...' : 'Catat Masuk' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal hapus -->
        <div v-if="deleteModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                <div class="w-12 h-12 rounded-full bg-red-500/15 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <h3 class="text-white font-semibold text-center mb-1">Hapus Pengunjung?</h3>
                <p class="text-slate-400 text-sm text-center mb-6">
                    "<span class="text-slate-200">{{ deleteTarget?.name }}</span>" dan semua data kunjungannya akan dihapus permanen.
                </p>
                <div class="flex gap-3">
                    <button @click="deleteModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                        Batal
                    </button>
                    <button @click="deleteVisitor" :disabled="deleting"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-red-600 hover:bg-red-500 disabled:opacity-60 text-white transition">
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

const visitors    = ref([])
const meta        = ref({})
const loading     = ref(true)
const search      = ref('')
const deleteModal = ref(false)
const deleteTarget = ref(null)
const deleting    = ref(false)

const logModal      = ref(false)
const logTarget     = ref(null)
const loggingVisit  = ref(false)
const logError      = ref('')
const logForm       = ref({ purpose: '', notes: '' })

let searchTimeout = null

async function fetchVisitors(page = 1) {
    loading.value = true
    try {
        const { data } = await axios.get('/api/visitors', {
            params: { page, per_page: 15, search: search.value || undefined }
        })
        visitors.value = data.data
        meta.value = {
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

function onSearchInput() {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => fetchVisitors(1), 400)
}

function confirmDelete(visitor) {
    deleteTarget.value = visitor
    deleteModal.value  = true
}

async function deleteVisitor() {
    deleting.value = true
    try {
        await axios.delete(`/api/visitors/${deleteTarget.value.id}`)
        deleteModal.value = false
        await fetchVisitors(meta.value.current_page)
    } catch (e) {
        console.error(e)
    } finally {
        deleting.value = false
    }
}

function openLogModal(visitor) {
    logTarget.value   = visitor
    logForm.value     = { purpose: '', notes: '' }
    logError.value    = ''
    logModal.value    = true
}

async function saveLog() {
    loggingVisit.value = true
    logError.value     = ''
    try {
        await axios.post('/api/visit-logs', {
            visitor_id: logTarget.value.id,
            purpose:    logForm.value.purpose,
            notes:      logForm.value.notes,
        })
        logModal.value = false
        await fetchVisitors(meta.value.current_page)
    } catch (e) {
        logError.value = e.response?.data?.message || 'Terjadi kesalahan.'
    } finally {
        loggingVisit.value = false
    }
}

onMounted(() => fetchVisitors())
</script>