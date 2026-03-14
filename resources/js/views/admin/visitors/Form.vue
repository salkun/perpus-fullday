<template>
    <div class="max-w-2xl space-y-6">

        <!-- Back -->
        <RouterLink
            :to="{ name: 'admin-visitors' }"
            class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm transition"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Kembali ke daftar pengunjung
        </RouterLink>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
            <h2 class="text-white font-semibold text-base mb-6">
                {{ isEdit ? 'Edit Pengunjung' : 'Tambah Pengunjung Baru' }}
            </h2>

            <div v-if="loadingData" class="space-y-4">
                <div v-for="i in 5" :key="i" class="space-y-1.5">
                    <div class="h-3 w-24 bg-slate-800 rounded animate-pulse"></div>
                    <div class="h-10 bg-slate-800 rounded-xl animate-pulse"></div>
                </div>
            </div>

            <div v-else class="space-y-5">

                <!-- Nama & ID Anggota -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            Nama Lengkap <span class="text-red-400">*</span>
                        </label>
                        <input v-model="form.name" type="text" placeholder="Nama lengkap"
                            :class="inputClass('name')" />
                        <p v-if="errors.name" class="text-red-400 text-xs mt-1.5">{{ errors.name[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            ID Anggota
                            <span class="text-slate-500 font-normal ml-1">(opsional)</span>
                        </label>
                        <input v-model="form.member_id" type="text" placeholder="Contoh: MBR-001"
                            :class="inputClass('member_id')" />
                        <p v-if="errors.member_id" class="text-red-400 text-xs mt-1.5">{{ errors.member_id[0] }}</p>
                    </div>
                </div>

                <!-- Email & No HP -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                        <input v-model="form.email" type="email" placeholder="email@contoh.com"
                            :class="inputClass('email')" />
                        <p v-if="errors.email" class="text-red-400 text-xs mt-1.5">{{ errors.email[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">No. Telepon</label>
                        <input v-model="form.phone" type="text" placeholder="08xxxxxxxxxx"
                            :class="inputClass('phone')" />
                    </div>
                </div>

                <!-- Jenis Kelamin & Tanggal Lahir -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Jenis Kelamin</label>
                        <select v-model="form.gender" :class="inputClass('gender')">
                            <option value="">Pilih jenis kelamin</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Lahir</label>
                        <input v-model="form.birth_date" type="date"
                            :class="inputClass('birth_date')" />
                    </div>
                </div>

                <!-- Pekerjaan/Status -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pekerjaan / Status</label>
                    <input
                        v-model="form.occupation"
                        type="text"
                        placeholder="Pelajar, Mahasiswa, Umum..."
                        list="occupation-list"
                        :class="inputClass('occupation')"
                    />
                    <datalist id="occupation-list">
                        <option value="Pelajar" />
                        <option value="Mahasiswa" />
                        <option value="Guru / Dosen" />
                        <option value="Karyawan" />
                        <option value="Umum" />
                    </datalist>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Alamat</label>
                    <textarea
                        v-model="form.address"
                        rows="3"
                        placeholder="Alamat lengkap..."
                        class="w-full bg-slate-800/50 border border-slate-700 hover:border-slate-600 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"
                    ></textarea>
                </div>

                <!-- Error umum -->
                <div v-if="errorMessage" class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    {{ errorMessage }}
                </div>

                <!-- Tombol -->
                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
                    <RouterLink :to="{ name: 'admin-visitors' }"
                        class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 transition text-center">
                        Batal
                    </RouterLink>
                    <button @click="submit" :disabled="submitting"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-500 disabled:opacity-60 text-white transition shadow-lg shadow-indigo-600/20">
                        <svg v-if="submitting" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ submitting ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Pengunjung') }}
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

const isEdit      = computed(() => !!route.params.id)
const loadingData = ref(false)
const submitting  = ref(false)
const errors      = ref({})
const errorMessage = ref('')

const form = reactive({
    name:       '',
    member_id:  '',
    email:      '',
    phone:      '',
    gender:     '',
    birth_date: '',
    occupation: '',
    address:    '',
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

async function submit() {
    submitting.value   = true
    errors.value       = {}
    errorMessage.value = ''

    const payload = {
        name:       form.name,
        member_id:  form.member_id  || null,
        email:      form.email      || null,
        phone:      form.phone      || null,
        gender:     form.gender     || null,
        birth_date: form.birth_date || null,
        occupation: form.occupation || null,
        address:    form.address    || null,
    }

    try {
        if (isEdit.value) {
            await axios.put(`/api/visitors/${route.params.id}`, payload)
        } else {
            await axios.post('/api/visitors', payload)
        }
        router.push({ name: 'admin-visitors' })
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

async function fetchVisitor() {
    loadingData.value = true
    try {
        const { data } = await axios.get(`/api/visitors/${route.params.id}`)
        form.name       = data.name
        form.member_id  = data.member_id  || ''
        form.email      = data.email      || ''
        form.phone      = data.phone      || ''
        form.gender     = data.gender     || ''
        form.birth_date = data.birth_date ? data.birth_date.substring(0, 10) : ''
        form.occupation = data.occupation || ''
        form.address    = data.address    || ''
    } catch {
        router.push({ name: 'admin-visitors' })
    } finally {
        loadingData.value = false
    }
}

onMounted(() => {
    if (isEdit.value) fetchVisitor()
})
</script>