<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// Menu items based on the Sakai dashboard example
const items = ref([
    {
        label: 'Home',
        items: [
            { label: 'Dashboard', icon: 'pi pi-home', route: 'dashboard' }
        ]
    },
    {
        label: 'UI Components',
        items: [
            { label: 'Form Layout', icon: 'pi pi-id-card', to: '/formlayout' },
            { label: 'Input', icon: 'pi pi-check-square', to: '/input' },
            { label: 'Button', icon: 'pi pi-mobile', to: '/button' },
            { label: 'Table', icon: 'pi pi-table', to: '/table' },
            { label: 'List', icon: 'pi pi-list', to: '/list' },
            { label: 'Tree', icon: 'pi pi-share-alt', to: '/tree' },
            { label: 'Panel', icon: 'pi pi-tablet', to: '/panel' },
            { label: 'Overlay', icon: 'pi pi-clone', to: '/overlay' },
            { label: 'Media', icon: 'pi pi-image', to: '/media' },
            { label: 'Menu', icon: 'pi pi-bars', to: '/menu' },
            { label: 'Message', icon: 'pi pi-comment', to: '/message' },
            { label: 'File', icon: 'pi pi-file', to: '/file' },
            { label: 'Chart', icon: 'pi pi-chart-bar', to: '/chart' },
            { label: 'Timeline', icon: 'pi pi-calendar', to: '/timeline' },
            { label: 'Misc', icon: 'pi pi-circle', to: '/misc' }
        ]
    },
    {
        label: 'Prime Blocks',
        items: [
            { label: 'Free Blocks', icon: 'pi pi-eye', to: '/blocks' },
            { label: 'All Blocks', icon: 'pi pi-globe', url: 'https://primeblocks.org', target: '_blank' }
        ]
    }
]);
</script>

<template>
    <div class="flex flex-col h-full bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-100">
        <!-- Logo Area -->
        <div
            class="flex items-center justify-center p-6 border-b border-gray-100 dark:border-gray-700 h-16 min-h-[4rem]">
            <Link :href="route('home')" class="flex items-center gap-2">
                <!-- Simple SVG Logo Placeholder if Image fails -->
                <svg class="h-8 w-8 text-emerald-500" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span class="text-xl font-bold text-gray-800 dark:text-white">SAKAI</span>
            </Link>
        </div>

        <!-- Menu Items -->
        <div class="flex-1 overflow-y-auto py-4 px-4 custom-scrollbar">
            <ul class="flex flex-col gap-1">
                <template v-for="(category, i) in items" :key="i">
                    <!-- Category Label -->
                    <li v-if="category.label"
                        class="mt-4 first:mt-0 mb-2 px-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                        {{ category.label }}
                    </li>
                    <!-- Menu Items -->
                    <li v-for="(item, j) in category.items" :key="j">
                        <!-- Items with Route (Inertia) -->
                        <Link v-if="item.route" :href="route(item.route)"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 cursor-pointer"
                            :class="route().current(item.route) ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white'">
                            <i :class="[item.icon, 'mr-2 text-lg']"></i>
                            <span>{{ item.label }}</span>
                        </Link>

                        <!-- External Links -->
                        <a v-else-if="item.url" :href="item.url" :target="item.target"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 cursor-pointer text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                            <i :class="[item.icon, 'mr-2 text-lg']"></i>
                            <span>{{ item.label }}</span>
                        </a>

                        <!-- Placeholder Links -->
                        <a v-else href="#"
                            class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 cursor-pointer text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                            <i :class="[item.icon, 'mr-2 text-lg']"></i>
                            <span>{{ item.label }}</span>
                        </a>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
}
</style>
