<script setup>

defineProps({
    form: Object,
});
</script>

<template>
    <div class="animate-in fade-in slide-in-from-right-4 duration-500">
        <div
            class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
            <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-4">
                <div
                    class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 text-rose-600 rounded-2xl flex items-center justify-center">
                    <i class="pi pi-server text-xl"></i>
                </div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">System
                    & Maintenance</h2>
            </div>

            <div class="space-y-4">
                <div
                    class="flex items-center justify-between p-4 bg-rose-50/50 dark:bg-rose-900/10 rounded-2xl border border-rose-100 dark:border-rose-900/30">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 bg-rose-100 dark:bg-rose-900/50 rounded-xl flex items-center justify-center text-rose-600">
                            <i class="pi pi-lock"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-gray-900 dark:text-white">Maintenance
                                Mode</h4>
                            <p class="text-[10px] text-gray-500 font-medium">Show maintenance page
                                to visitors</p>
                        </div>
                    </div>
                    <ToggleSwitch v-model="form.maintenance_mode" severity="danger" />
                </div>
            </div>
        </div>
    </div>
</template>
