<script setup>

defineProps({
    form: Object,
    logoPreview: String,
    onLogoChange: Function,
});
</script>

<template>
    <div class="animate-in fade-in slide-in-from-right-4 duration-500">
        <div
            class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-8">
            <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-2">
                <div
                    class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <i class="pi pi-cog text-xl"></i>
                </div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">
                    General Settings</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2 flex flex-col gap-4 mb-4">
                    <label class="font-black tracking-widest text-sm text-gray-500 uppercase">Application
                        Logo</label>
                    <div
                        class="flex items-center gap-8 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                        <div class="relative flex-shrink-0 w-32 h-32">
                            <div v-if="logoPreview" class="w-full h-full overflow-hidden rounded-2xl shadow-xl">
                                <img :src="logoPreview" class="w-full h-full object-cover" alt="Logo Preview" />
                            </div>
                            <div v-else
                                class="w-full h-full rounded-2xl bg-white dark:bg-gray-800 flex flex-col items-center justify-center border border-gray-100 dark:border-gray-700 shadow-inner">
                                <i class="pi pi-image text-3xl text-gray-200"></i>
                                <span class="text-[10px] font-black uppercase text-gray-300 mt-2">No
                                    Logo</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-3">
                            <p class="text-xs font-bold text-gray-500">Recommended size: 512x512px
                                (PNG, SVG, or JPG).</p>
                            <label
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md cursor-pointer transition-all active:scale-95">
                                <i class="pi pi-upload"></i>
                                <span class="text-xs font-black uppercase tracking-widest">Choose
                                    Image</span>
                                <input type="file" @change="onLogoChange" class="hidden" accept="image/*" />
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Site
                        Name</label>
                    <InputText v-model="form.site_name"
                        class="!py-2 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Site
                        Tagline</label>
                    <InputText v-model="form.site_tagline"
                        class="!py-2 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                </div>
                <div class="md:col-span-2 flex flex-col gap-2">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Site
                        Description</label>
                    <Textarea v-model="form.site_description" rows="3"
                        class="!py-2 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                </div>
            </div>
        </div>
    </div>
</template>
