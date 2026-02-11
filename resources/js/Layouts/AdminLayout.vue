<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppSidebar from '@/Components/AppSidebar.vue';
import { useConfirm } from "primevue/useconfirm";

const showingMobileSidebar = ref(false);
const isDesktopSidebarVisible = ref(true);
const isDark = ref(false);
const userMenu = ref();
const page = usePage();
const settings = ref(page.props.settings);

const menuActive = defineModel('menuActive', { type: String });
const title = defineModel('title', { type: String });

const confirm = useConfirm();

const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
};

const toggleSidebar = () => {
    if (window.innerWidth >= 1024) {
        isDesktopSidebarVisible.value = !isDesktopSidebarVisible.value;
    } else {
        showingMobileSidebar.value = !showingMobileSidebar.value;
    }
};

const handleResize = () => {
    if (window.innerWidth < 1024) {
        isDesktopSidebarVisible.value = false;
    } else {
        isDesktopSidebarVisible.value = true;
    }
};

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('my-app-dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('my-app-dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    window.addEventListener('resize', handleResize);
    handleResize(); // Initial check

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        isDark.value = savedTheme === 'dark';
        if (isDark.value) document.documentElement.classList.add('my-app-dark');
    } else {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        isDark.value = prefersDark;
        if (prefersDark) document.documentElement.classList.add('my-app-dark');
    }
});


onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

const userMenuItems = [
    {
        label: 'Profile',
        icon: 'pi pi-user',
        command: () => router.visit(route('profile.edit'))
    },
    { separator: true },
    {
        label: 'Log Out',
        icon: 'pi pi-sign-out',
        command: () => confirmLogout()
    }
];

const confirmLogout = () => {
    confirm.require({
        message: 'Apakah Anda yakin ingin keluar dari aplikasi?',
        header: 'Konfirmasi Log Out',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Batal',
        acceptLabel: 'Keluar',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Ya, Keluar',
            severity: 'danger'
        },
        accept: () => {
            router.post(route('logout'));
        },
        reject: () => {
            return;
        }
    });
};
</script>

<template>
    <Toast />
    <ConfirmDialog></ConfirmDialog>
    <div
        class="min-h-screen flex relative font-sans text-gray-900 antialiased bg-gray-50 dark:bg-[#0f172a] transition-colors duration-300">
        <!-- Sidebar (Desktop) -->
        <aside :class="['fixed inset-y-0 left-0 w-64 bg-white dark:bg-[#1e293b] border-r border-gray-200 dark:border-gray-800 z-30 shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-all duration-300 ease-in-out hidden lg:block', {
            'translate-x-0 opacity-100': isDesktopSidebarVisible,
            '-translate-x-full opacity-0 pointer-events-none': !isDesktopSidebarVisible
        }]">
            <AppSidebar v-model:menuActive="menuActive" />
        </aside>

        <!-- Sidebar (Mobile - Drawer) -->
        <Drawer v-model:visible="showingMobileSidebar" class="!w-72 !p-0 border-none shadow-2xl dark:!bg-[#1e293b]">
            <template #container>
                <AppSidebar v-model:menuActive="menuActive" @close="showingMobileSidebar = false" />
            </template>
        </Drawer>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-300 ease-in-out"
            :class="{ 'lg:ml-64': isDesktopSidebarVisible, 'lg:ml-0': !isDesktopSidebarVisible }">
            <!-- Topbar -->
            <header
                class="sticky top-0 z-20 h-16 bg-white/80 dark:bg-[#1e293b]/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 sm:px-8 shadow-sm transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <Button icon="pi pi-bars" text rounded aria-label="Menu"
                        class="text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors"
                        @click="toggleSidebar" />
                    <h1 class="text-lg font-bold text-gray-800 dark:text-gray-100 hidden sm:block tracking-tight">{{
                        title }}
                    </h1>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Dark Mode Toggle -->
                    <Button @click="toggleDarkMode" :icon="isDark ? 'pi pi-moon' : 'pi pi-sun'" text rounded
                        severity="secondary" aria-label="Toggle Dark Mode"
                        class="w-10 h-10 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-all duration-300" />

                    <div class="hidden md:flex gap-1">
                        <Button icon="pi pi-cog" text rounded severity="secondary" aria-label="Settings"
                            class="w-10 h-10 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800" />
                        <Button icon="pi pi-bell" text rounded severity="secondary" aria-label="Notifications"
                            class="w-10 h-10 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800" />
                    </div>

                    <!-- User Profile -->
                    <button @click="toggleUserMenu"
                        class="flex items-center gap-3 pl-3 pr-1.5 py-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 group">
                        <div class="flex flex-col items-end hidden lg:flex">
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-100 leading-none">{{
                                page.props.auth.user.name }}</span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">{{
                                page.props.auth.user.role }}</span>
                        </div>
                        <Avatar :label="page.props.auth.user.name.charAt(0)"
                            class="!bg-emerald-500 !text-white !font-bold shadow-sm group-hover:scale-105 transition-transform"
                            shape="circle" />
                    </button>
                    <Menu ref="userMenu" :model="userMenuItems" :popup="true"
                        class="!min-w-[14rem] !border-none !shadow-xl dark:!bg-[#1e293b]" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-2 sm:p-2 lg:p-4 xl:p-2 transition-all duration-300">
                <div class="max-w-full mx-auto">
                    <!-- Page Header Slot -->
                    <div v-if="$slots.header" class="mb-4">
                        <slot name="header" />
                    </div>

                    <!-- Main Slot -->
                    <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                        <slot />
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer
                class="bg-white/50 dark:bg-[#1e293b]/50 backdrop-blur-sm py-4 px-8 text-xs text-gray-400 dark:text-gray-500 flex flex-col sm:flex-row justify-between items-center gap-4 mt-auto border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2">
                    <span>&copy; {{ new Date().getFullYear() }}</span>
                    <span class="font-bold text-emerald-600/80">Koperasi Digital</span>
                    <span class="text-gray-300 dark:text-gray-700">|</span>
                    <span>Admin Dashboard</span>
                </div>
                <div class="flex gap-6 uppercase tracking-widest text-[10px]">
                    <a href="#" class="hover:text-emerald-500 transition-colors">Privacy</a>
                    <a href="#" class="hover:text-emerald-500 transition-colors">Terms</a>
                    <a href="#" class="hover:text-emerald-500 transition-colors">Support</a>
                </div>
            </footer>
        </div>
    </div>
</template>

<style>
/* Global smoother transitions for layout */
.p-drawer-content {
    background-color: transparent !important;
}

body {
    transition: background-color 0.3s ease;
}

/* Custom Scrollbar for the whole app */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}

.my-app-dark ::-webkit-scrollbar-thumb {
    background: #334155;
}

::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

.my-app-dark ::-webkit-scrollbar-thumb:hover {
    background: #475569;
}
</style>
