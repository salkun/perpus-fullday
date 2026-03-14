<template>
    <div class="min-h-screen bg-slate-950 flex flex-col">

        <!-- Navbar -->
        <header class="sticky top-0 z-30 bg-slate-950/80 backdrop-blur border-b border-slate-800">
            <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">

                <!-- Logo -->
                <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2.5 shrink-0">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-600/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <span class="text-white font-semibold text-sm">Perpustakaan</span>
                </RouterLink>

                <!-- Desktop nav -->
                <nav class="hidden md:flex items-center gap-1">
                    <RouterLink
                        v-for="item in navItems" :key="item.name"
                        :to="item.to"
                        :class="[
                            'px-4 py-2 rounded-lg text-sm font-medium transition duration-150',
                            route.name === item.name
                                ? 'bg-slate-800 text-white'
                                : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
                        ]"
                    >
                        {{ item.label }}
                    </RouterLink>
                </nav>

                <div class="flex items-center gap-2">
                    <RouterLink
                        :to="{ name: 'login' }"
                        class="hidden md:flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800 transition duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                        Admin
                    </RouterLink>

                    <!-- Hamburger mobile -->
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden text-slate-400 hover:text-white transition p-1">
                        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="mobileOpen" class="md:hidden border-t border-slate-800 px-4 py-3 space-y-1">
                    <RouterLink
                        v-for="item in navItems" :key="item.name"
                        :to="item.to"
                        @click="mobileOpen = false"
                        class="block px-4 py-2.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition"
                    >
                        {{ item.label }}
                    </RouterLink>
                    <RouterLink
                        :to="{ name: 'login' }"
                        @click="mobileOpen = false"
                        class="block px-4 py-2.5 rounded-lg text-sm text-slate-400 hover:bg-slate-800 hover:text-white transition"
                    >
                        Login Admin
                    </RouterLink>
                </div>
            </transition>
        </header>

        <!-- Page content -->
        <main class="flex-1">
            <RouterView />
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800 py-8 px-4 text-center">
            <p class="text-slate-600 text-sm">&copy; {{ new Date().getFullYear() }} Perpustakaan. Semua hak dilindungi.</p>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, RouterView, useRoute } from 'vue-router'

const route      = useRoute()
const mobileOpen = ref(false)

const navItems = [
    { name: 'home',  label: 'Beranda', to: { name: 'home' } },
    { name: 'books', label: 'Koleksi Buku', to: { name: 'books' } },
]
</script>
