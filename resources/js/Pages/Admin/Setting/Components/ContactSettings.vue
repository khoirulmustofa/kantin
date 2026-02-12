<script setup>

defineProps({
    form: Object,
});
</script>

<template>
    <div class="animate-in fade-in slide-in-from-right-4 duration-500">
        <div
            class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-8">
            <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-2">
                <div
                    class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center">
                    <i class="pi pi-envelope text-xl"></i>
                </div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">
                    Contact & Social</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Email
                        Address</label>
                    <InputText v-model="form.contact_email" class="!py-2 bg-gray-50 border-transparent shadow-inner" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Phone
                        Number</label>
                    <InputText v-model="form.contact_phone" class="!py-2 bg-gray-50 border-transparent shadow-inner" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">WhatsApp
                        Number</label>
                    <InputText v-model="form.whatsapp_number"
                        class="!py-2 bg-gray-50 border-transparent shadow-inner" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Store
                        Address</label>
                    <InputText v-model="form.address" class="!py-2 bg-gray-50 border-transparent shadow-inner" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-blue-600">Facebook
                        URL</label>
                    <InputText v-model="form.facebook_url" class="!py-2 bg-gray-50 border-transparent shadow-inner" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-rose-500">Instagram
                        URL</label>
                    <InputText v-model="form.instagram_url" class="!py-2 bg-gray-50 border-transparent shadow-inner" />
                </div>
            </div>
        </div>
    </div>
</template>
