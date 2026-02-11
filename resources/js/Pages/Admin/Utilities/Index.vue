<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useToast } from "primevue/usetoast";

const props = defineProps({
    menu: String,
    title: String,
    logSize: Number,
    isDebug: Boolean,
    phpVersion: String,
    laravelVersion: String,
});


const toast = useToast();
const loadingCache = ref(false);
const loadingLogs = ref(false);
const loadingDebug = ref(false);
const loadingSession = ref(false);

const clearCache = () => {
    loadingCache.value = true;
    router.post(route('admin.utilities.clear_cache'), {}, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'System cache cleared', life: 3000 });
        },
        onFinish: () => loadingCache.value = false
    });
};

const clearLogs = () => {
    loadingLogs.value = true;
    router.post(route('admin.utilities.clear_log'), {}, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Logs cleared', life: 3000 });
        },
        onFinish: () => loadingLogs.value = false
    });
};

const toggleDebug = () => {
    loadingDebug.value = true;
    router.post(route('admin.utilities.toggle_debug'), {}, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Debug mode updated', life: 3000 });
        },
        onFinish: () => loadingDebug.value = false
    });
};

const runStorageLink = () => {
    // Just a placeholder for now or we could add another route
    toast.add({ severity: 'info', summary: 'Info', detail: 'Storage link action triggered', life: 3000 });
};

const clearSession = () => {
    loadingSession.value = true;
    router.post(route('admin.utilities.clear_session'), {}, {
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Session cleared', life: 3000 });
        },
        onFinish: () => loadingSession.value = false
    });
};
</script>

<template>

    <Head :title="props.title" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="card p-2 space-y-2 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">
            <!-- 4-Column Action Cards (Dashboard Style) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Cache Card -->
                <div
                    class="relative overflow-hidden group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:-rotate-12 transition-transform duration-500">
                        <i class="pi pi-bolt text-7xl text-indigo-600"></i>
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <i class="pi pi-bolt text-xl"></i>
                        </div>
                        <span class="font-black  tracking-[0.2em] text-gray-400">Optimization</span>
                    </div>
                    <div class="space-y-4">
                        <h3 class="font-black text-gray-900 dark:text-white tracking-tight">System Cache</h3>
                        <Button @click="clearCache" :loading="loadingCache" label="Optimize Now" icon="pi pi-refresh"
                            class="!w-full !rounded-xl !py-3 !bg-indigo-600 !border-none !font-black  !tracking-widest shadow-lg shadow-indigo-500/20" />
                    </div>
                </div>

                <!-- Logs Card -->
                <div
                    class="relative overflow-hidden group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-rose-500/10 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:-rotate-12 transition-transform duration-500">
                        <i class="pi pi-list text-7xl text-rose-600"></i>
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400">
                            <i class="pi pi-list text-xl"></i>
                        </div>
                        <span class="font-black  tracking-[0.2em] text-gray-400">Logs</span>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-baseline gap-2">
                            <h3 class="font-black text-gray-900 dark:text-white tracking-tight">{{ logSize }}
                            </h3>
                            <span class="font-black  text-gray-900">MB Used</span>
                        </div>
                        <Button @click="clearLogs" :loading="loadingLogs" label="Clear Logs" icon="pi pi-trash"
                            severity="danger"
                            class="!w-full !rounded-xl !py-3 !font-black  !tracking-widest shadow-lg shadow-rose-500/20" />
                    </div>
                </div>

                <!-- Debug Card -->
                <div
                    class="relative overflow-hidden group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:-rotate-12 transition-transform duration-500">
                        <i class="pi pi-code text-7xl text-amber-600"></i>
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <i class="pi pi-code text-xl"></i>
                        </div>
                        <span class="font-black  tracking-[0.2em] text-gray-400">Environment</span>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <div
                                :class="[isDebug ? 'bg-amber-500' : 'bg-gray-400', 'w-2 h-2 rounded-full animate-pulse']">
                            </div>
                            <h3 class="font-black text-gray-900 dark:text-white tracking-tight ">{{
                                isDebug ? 'Debug ON' : 'Debug OFF' }}</h3>
                        </div>
                        <Button @click="toggleDebug" :loading="loadingDebug"
                            :label="isDebug ? 'Disable Debug' : 'Enable Debug'"
                            :icon="isDebug ? 'pi pi-eye-slash' : 'pi pi-eye'" severity="warning"
                            class="!w-full !rounded-xl !py-3 !font-black  !tracking-widest shadow-lg shadow-amber-500/20" />
                    </div>
                </div>

                <!-- Session Card / Placeholder -->
                <div
                    class="relative overflow-hidden group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:-rotate-12 transition-transform duration-500">
                        <i class="pi pi-folder text-7xl text-emerald-600"></i>
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="pi pi-folder text-xl"></i>
                        </div>
                        <span class="font-black  tracking-[0.2em] text-gray-400">Filesystem</span>
                    </div>
                    <div class="space-y-4">
                        <h3 class="font-black text-gray-900 dark:text-white tracking-tight ">Storage
                            Link</h3>
                        <Button @click="runStorageLink" label="Link Storage" icon="pi pi-link" severity="success"
                            class="!w-full !rounded-xl !py-3 !font-black  !tracking-widest shadow-lg shadow-emerald-500/20" />
                    </div>
                </div>

                 <!-- Clear Session -->
                 <div
                    class="relative overflow-hidden group p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:shadow-red-500/10 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 p-8 opacity-[0.03] dark:opacity-[0.07] group-hover:scale-125 group-hover:-rotate-12 transition-transform duration-500">
                        <i class="pi pi-folder text-7xl text-red-600"></i>
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center text-red-600 dark:text-red-400">
                            <i class="pi pi-folder text-xl"></i>
                        </div>
                        <span class="font-black  tracking-[0.2em] text-gray-400">Clear Session</span>
                    </div>
                    <div class="space-y-4">
                        <h3 class="font-black text-gray-900 dark:text-white tracking-tight ">Clear Session</h3>
                        <Button @click="clearSession" :loading="loadingSession" label="Clear Session" icon="pi pi-link" severity="danger"
                            class="!w-full !rounded-xl !py-3 !font-black !tracking-widest shadow-lg shadow-red-500/20" />
                    </div>
                </div>
            </div>

            <!-- Additional system info or tools can go here -->
            <div
                class="card p-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-gray-900 text-white rounded-2xl flex items-center justify-center">
                        <i class="pi pi-info-circle text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 dark:text-white  tracking-tighter">System
                            Information</h4>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400  tracking-[0.2em] mb-2">PHP Version</p>
                        <p class="text-lg font-black text-gray-900 dark:text-white">{{ props.phpVersion }}</p>
                    </div>
                    <div
                        class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400  tracking-[0.2em] mb-2">Laravel Version
                        </p>
                        <p class="text-lg font-black text-gray-900 dark:text-white">{{ props.laravelVersion }}</p>
                    </div>
                    <div
                        class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400  tracking-[0.2em] mb-2">App Name</p>
                        <p class="text-lg font-black text-gray-900 dark:text-white">Kantin Digital</p>
                    </div>
                </div>
            </div>

            
        </div>
    </AdminLayout>
</template>

<style scoped>
.group:hover .p-button {
    transform: translateY(-2px);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
</style>
