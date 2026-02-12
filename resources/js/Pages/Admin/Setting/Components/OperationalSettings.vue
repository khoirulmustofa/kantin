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
                    class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center">
                    <i class="pi pi-shopping-bag text-xl"></i>
                </div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">
                    Operational Settings</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Currency
                        Code</label>
                    <InputText v-model="form.currency" class="!py-2 bg-gray-50 border-transparent shadow-inner" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Minimum
                        Order</label>
                    <InputNumber v-model="form.min_order" class="!w-full"
                        inputClass="!py-2 bg-gray-50 border-transparent shadow-inner" />
                </div>

                <div
                    class="md:col-span-2 flex items-center justify-between p-4 bg-amber-50/50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-900/30 mt-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center text-amber-600">
                            <i class="pi pi-clock"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-gray-900 dark:text-white">Store Open
                                Status</h4>
                            <p class="text-[10px] text-gray-500 font-medium">Temporarily disable
                                ordering from frontend</p>
                        </div>
                    </div>
                    <ToggleSwitch v-model="form.store_open" severity="warning" />
                </div>
            </div>
        </div>
    </div>
</template>
