<script setup>
import { ref, onMounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import AppSidebar from '@/Components/AppSidebar.vue';

const showingMobileSidebar = ref(false);
const isDesktopSidebarVisible = ref(true);
const isDark = ref(false);
const userMenu = ref();
const page = usePage();

const toggleUserMenu = (event) => {
    userMenu.value.toggle(event);
};

const toggleSidebar = () => {
    if (window.innerWidth >= 1024) {
        isDesktopSidebarVisible.value = !isDesktopSidebarVisible.value;
    } else {
        showingMobileSidebar.value = true;
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
    const savedTheme = localStorage.getItem('theme','light');
    if (savedTheme) {
        isDark.value = savedTheme === 'dark';
        if (isDark.value) document.documentElement.classList.add('my-app-dark');
    } else {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        isDark.value = prefersDark;
        if (prefersDark) document.documentElement.classList.add('my-app-dark');
    }
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
        command: () => router.post(route('logout'))
    }
];
</script>

<template>
    <div class="min-h-screen flex relative font-sans text-gray-900 antialiased">
        <!-- Sidebar (Desktop) -->
        <div :class="['fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-30 shadow-[0_2px_12px_rgba(0,0,0,0.08)] transition-transform duration-300 hidden lg:block', {
            'translate-x-0': isDesktopSidebarVisible,
            '-translate-x-full': !isDesktopSidebarVisible
        }]">
            <AppSidebar />
        </div>

        <!-- Sidebar (Mobile - Drawer) -->
        <Drawer v-model:visible="showingMobileSidebar" class="!w-64 !p-0 border-none shadow-xl">
            <template #container="{ closeCallback }">
                <AppSidebar />
            </template>
        </Drawer>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
            :class="{ 'lg:ml-64': isDesktopSidebarVisible, 'lg:ml-0': !isDesktopSidebarVisible }">
            <!-- Topbar -->
            <header
                class="sticky top-0 z-20 h-16 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 sm:px-6 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
                <div class="flex items-center gap-3">
                    <Button icon="pi pi-bars" text rounded aria-label="Menu" class="text-gray-500 hover:bg-gray-100"
                        @click="toggleSidebar" />
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100 hidden sm:block">Dashboard</h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Dark Mode Toggle (Visible Mobile & Desktop) -->
                    <Button @click="toggleDarkMode" :icon="isDark ? 'pi pi-moon' : 'pi pi-sun'" text rounded
                        severity="secondary" aria-label="Toggle Dark Mode" class="w-10 h-10 text-gray-500" />

                    <div class="hidden sm:flex gap-1">
                        <Button icon="pi pi-cog" text rounded severity="secondary" aria-label="Settings"
                            class="w-10 h-10 text-gray-500" />
                        <Button icon="pi pi-bell" text rounded severity="secondary" aria-label="Notifications"
                            class="w-10 h-10 text-gray-500" />
                    </div>

                    <!-- User Profile -->
                    <button @click="toggleUserMenu"
                        class="flex items-center gap-3 pl-3 pr-2 py-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/20 border border-transparent hover:border-gray-200">
                        <div class="flex flex-col items-end hidden md:flex">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 leading-none">{{
                                page.props.auth.user.name }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Administrator</span>
                        </div>
                        <Avatar :label="page.props.auth.user.name.charAt(0)" class="bg-emerald-500 text-white font-bold"
                            shape="circle" />
                    </button>
                    <Menu ref="userMenu" :model="userMenuItems" :popup="true" class="!min-w-[12rem]" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-x-hidden">
                <div class="max-w-[1600px] mx-auto space-y-6">
                    <!-- Page Header Slot -->
                    <div v-if="$slots.header" class="mb-6">
                        <slot name="header" />
                    </div>

                    <!-- Main Slot -->
                    <slot />
                </div>
            </main>

            <!-- Footer -->
            <footer
                class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6 px-8 text-center sm:text-left text-sm text-gray-500 flex flex-col sm:flex-row justify-between items-center gap-4">
                <span>&copy; {{ new Date().getFullYear() }} <span class="font-medium text-emerald-600">Sakai</span> by
                    PrimeVue.</span>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-gray-900 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-900 transition-colors">Terms of Service</a>
                </div>
            </footer>
        </div>
    </div>
</template>
