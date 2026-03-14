<template>
    <div class="space-y-5">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-white font-semibold text-lg">Log Kunjungan</h2>
                <p class="text-slate-500 text-sm mt-0.5">
                    {{ filterDate === today ? 'Hari ini' : filterDate }} — {{ meta.total ?? 0 }} kunjungan
                </p>
            </div>
            <button
                @click="openAddModal"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-lg shadow-emerald-600/20"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Catat Kunjungan
            </button>
        </div>

        <!-- Filter tanggal -->
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <div class="flex items-center gap-2">
                <input
                    v-model="filterDate"
                    type="date"
                    @change="fetchLogs(1)"
                    class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                />
                <button
                    v-if="filterDate !== today"
                    @click="filterDate = today; fetchLogs(1)"
                    class="px-3 py-2.5 rounded-xl text-sm bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition"
                >
                    Hari ini
                </button>
            </div>
        </div>

        <!-- Tabel -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">

            <!-- Loading -->
            <div v-if="loading" class="divide-y divide-slate-800">
                <div v-for="i in 5" :key="i" class="flex items-center gap-4 px-5 py-4">
                    <div class="w-9 h-9 rounded-full bg-slate-800 animate-pulse shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 w-36 bg-slate-800 rounded animate-pulse"></div>
                        <div class="h-3 w-24 bg-slate-800 rounded animate-pulse"></div>
                    </div>
                    <div class="h-3 w-16 bg-slate-800 rounded animate-pulse"></div>
                </div>
            </div>

            <!-- Empty -->
            <div v-else-if="logs.length === 0" class="py-16 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-700 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                </svg>
                <p class="text-slate-500 text-sm">Belum ada kunjungan pada tanggal ini.</p>
            </div>

            <!-- List -->
            <div v-else>
                <div class="hidden md:grid grid-cols-[1fr_180px_120px_120px_80px] gap-4 px-5 py-3 border-b border-slate-800 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <span>Pengunjung</span>
                    <span>Tujuan</span>
                    <span>Jam Masuk</span>
                    <span>Jam Keluar</span>
                    <span class="text-right">Aksi</span>
                </div>

                <div class="divide-y divide-slate-800">
                    <div
                        v-for="log in logs"
                        :key="log.id"
                        class="flex md:grid md:grid-cols-[1fr_180px_120px_120px_80px] items-center gap-4 px-5 py-4 hover:bg-slate-800/40 transition"
                    >
                        <!-- Pengunjung -->
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-indigo-600/20 flex items-center justify-center text-indigo-400 font-semibold text-sm shrink-0">
                                {{ log.visitor?.name?.charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-slate-200 text-sm font-medium truncate">{{ log.visitor?.name }}</p>
                                <p v-if="log.visitor?.member_id" class="text-slate-500 text-xs font-mono">{{ log.visitor.member_id }}</p>
                                <!-- Mobile info -->
                                <div class="flex items-center gap-2 mt-0.5 md:hidden">
                                    <span class="text-slate-400 text-xs">{{ formatTime(log.visited_at) }}</span>
                                    <span v-if="log.purpose" class="text-slate-500 text-xs">· {{ log.purpose }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tujuan -->
                        <p class="text-slate-400 text-sm hidden md:block truncate">
                            {{ log.purpose || '—' }}
                        </p>

                        <!-- Jam masuk -->
                        <p class="text-slate-300 text-sm hidden md:block">{{ formatTime(log.visited_at) }}</p>

                        <!-- Jam keluar -->
                        <div class="hidden md:block">
                            <span v-if="log.left_at" class="text-slate-300 text-sm">{{ formatTime(log.left_at) }}</span>
                            <button v-else @click="markLeave(log)"
                                class="text-xs text-amber-400 hover:text-amber-300 transition">
                                Tandai keluar
                            </button>
                        </div>

                        <!-- Aksi -->
                        <div class="flex items-center gap-2 ml-auto md:ml-0 md:justify-end shrink-0">
                            <button @click="confirmDelete(log)"
                                class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-red-500/15 flex items-center justify-center text-slate-400 hover:text-red-400 transition">
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
                <button @click="fetchLogs(meta.current_page - 1)" :disabled="meta.current_page === 1"
                    class="px-3 py-2 rounded-lg text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    ← Sebelumnya
                </button>
                <button @click="fetchLogs(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page"
                    class="px-3 py-2 rounded-lg text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                    Berikutnya →
                </button>
            </div>
        </div>

        <!-- Modal catat kunjungan baru -->
        <div v-if="addModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                <h3 class="text-white font-semibold mb-5">Catat Kunjungan Baru</h3>

                <div class="space-y-4">
                    <!-- Cari pengunjung -->
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Pengunjung <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input
                                v-model="visitorSearch"
                                type="text"
                                placeholder="Cari nama atau ID anggota..."
                                class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                @input="searchVisitors"
                            />
                            <!-- Dropdown hasil cari -->
                            <div v-if="visitorResults.length && !selectedVisitor"
                                class="absolute top-full left-0 right-0 mt-1 bg-slate-800 border border-slate-700 rounded-xl overflow-hidden z-10 shadow-xl max-h-48 overflow-y-auto">
                                <button
                                    v-for="v in visitorResults" :key="v.id"
                                    @click="selectVisitor(v)"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-700 transition text-left"
                                >
                                    <div class="w-7 h-7 rounded-full bg-indigo-600/20 flex items-center justify-center text-indigo-400 text-xs font-semibold shrink-0">
                                        {{ v.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-slate-200 text-sm">{{ v.name }}</p>
                                        <p v-if="v.member_id" class="text-slate-500 text-xs font-mono">{{ v.member_id }}</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <!-- Selected visitor chip -->
                        <div v-if="selectedVisitor" class="mt-2 flex items-center gap-2 bg-indigo-600/15 border border-indigo-600/30 rounded-xl px-3 py-2">
                            <span class="text-indigo-300 text-sm flex-1">{{ selectedVisitor.name }}</span>
                            <button @click="selectedVisitor = null; visitorSearch = ''" class="text-indigo-400 hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tujuan Kunjungan</label>
                        <input v-model="addForm.purpose" type="text" placeholder="Baca buku, tugas, pinjam buku..."
                            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Catatan</label>
                        <textarea v-model="addForm.notes" rows="2" placeholder="Catatan tambahan (opsional)"
                            class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"></textarea>
                    </div>
                </div>

                <div v-if="addError" class="mt-3 text-red-400 text-xs">{{ addError }}</div>

                <div class="flex gap-3 mt-5">
                    <button @click="addModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                        Batal
                    </button>
                    <button @click="saveLog" :disabled="savingLog || !selectedVisitor"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-emerald-600 hover:bg-emerald-500 disabled:opacity-60 text-white transition">
                        {{ savingLog ? 'Menyimpan...' : 'Catat Masuk' }}
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
                <h3 class="text-white font-semibold text-center mb-1">Hapus Log Kunjungan?</h3>
                <p class="text-slate-400 text-sm text-center mb-6">Data kunjungan ini akan dihapus permanen.</p>
                <div class="flex gap-3">
                    <button @click="deleteModal = false"
                        class="flex-1 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                        Batal
                    </button>
                    <button @click="deleteLog" :disabled="deleting"
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
import axios from 'axios'

const logs        = ref([])
const meta        = ref({})
const loading     = ref(true)
const today       = new Date().toISOString().split('T')[0]
const filterDate  = ref(today)

const addModal       = ref(false)
const addForm        = ref({ purpose: '', notes: '' })
const addError       = ref('')
const savingLog      = ref(false)
const visitorSearch  = ref('')
const visitorResults = ref([])
const selectedVisitor = ref(null)

const deleteModal  = ref(false)
const deleteTarget = ref(null)
const deleting     = ref(false)

let visitorSearchTimeout = null

async function fetchLogs(page = 1) {
    loading.value = true
    try {
        const { data } = await axios.get('/api/visit-logs', {
            params: { page, per_page: 20, date: filterDate.value }
        })
        logs.value = data.data
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

function formatTime(datetime) {
    if (!datetime) return '—'
    return new Date(datetime).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

function openAddModal() {
    addForm.value     = { purpose: '', notes: '' }
    addError.value    = ''
    visitorSearch.value  = ''
    visitorResults.value = []
    selectedVisitor.value = null
    addModal.value    = true
}

function searchVisitors() {
    clearTimeout(visitorSearchTimeout)
    if (!visitorSearch.value || selectedVisitor.value) return
    visitorSearchTimeout = setTimeout(async () => {
        try {
            const { data } = await axios.get('/api/visitors', {
                params: { search: visitorSearch.value, per_page: 6 }
            })
            visitorResults.value = data.data
        } catch {}
    }, 300)
}

function selectVisitor(v) {
    selectedVisitor.value  = v
    visitorSearch.value    = v.name
    visitorResults.value   = []
}

async function saveLog() {
    if (!selectedVisitor.value) return
    savingLog.value = true
    addError.value  = ''
    try {
        await axios.post('/api/visit-logs', {
            visitor_id: selectedVisitor.value.id,
            purpose:    addForm.value.purpose,
            notes:      addForm.value.notes,
        })
        addModal.value = false
        await fetchLogs(1)
    } catch (e) {
        addError.value = e.response?.data?.message || 'Terjadi kesalahan.'
    } finally {
        savingLog.value = false
    }
}

async function markLeave(log) {
    try {
        await axios.put(`/api/visit-logs/${log.id}`, { left_at: new Date().toISOString() })
        await fetchLogs(meta.value.current_page)
    } catch (e) {
        console.error(e)
    }
}

function confirmDelete(log) {
    deleteTarget.value = log
    deleteModal.value  = true
}

async function deleteLog() {
    deleting.value = true
    try {
        await axios.delete(`/api/visit-logs/${deleteTarget.value.id}`)
        deleteModal.value = false
        await fetchLogs(meta.value.current_page)
    } catch (e) {
        console.error(e)
    } finally {
        deleting.value = false
    }
}

onMounted(() => fetchLogs())
</script>