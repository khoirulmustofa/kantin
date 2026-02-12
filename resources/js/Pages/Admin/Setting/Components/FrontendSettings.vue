<script setup>

defineProps({
    form: Object,
    sliderImages: Array,
    onSliderChange: Function,
    deleteSliderImage: Function,
});
</script>

<template>
    <div class="animate-in fade-in slide-in-from-right-4 duration-500">
        <div
            class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-8">
            <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-2">
                <div
                    class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 text-blue-600 rounded-2xl flex items-center justify-center">
                    <i class="pi pi-desktop text-xl"></i>
                </div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tighter">
                    Frontend Settings</h2>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-black tracking-widest uppercase text-xs text-gray-500">Banner Heading</label>
                        <InputText v-model="form.front_heading"
                            class="!py-2 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner"
                            placeholder="e.g., Welcome to Our Canteen" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-black tracking-widest uppercase text-xs text-gray-500">Banner
                            Sub-Heading</label>
                        <Textarea v-model="form.front_sub_heading" rows="3"
                            class="!py-2 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner"
                            placeholder="e.g., Delicious meals served daily for our students." />
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-4 border-t border-gray-50 dark:border-gray-700">
                    <label class="font-black tracking-widest uppercase text-xs text-gray-500">Slider Images</label>
                    <!-- Current Images Slider -->
                    <div v-if="sliderImages.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="(img, index) in sliderImages" :key="index"
                            class="relative group aspect-video rounded-xl overflow-hidden shadow-lg">
                            <img :src="`/storage/${img}`" class="w-full h-full object-cover" />
                            <div
                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" @click="deleteSliderImage(img)"
                                    class="w-10 h-10 bg-rose-500 text-white rounded-full flex items-center justify-center hover:scale-110 transition-transform">
                                    <i class="pi pi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Box -->
                    <div
                        class="p-8 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-3xl bg-gray-50 dark:bg-gray-900/50 flex flex-col items-center justify-center">
                        <i class="pi pi-images text-4xl text-gray-300 mb-4"></i>
                        <p class="text-xs font-bold text-gray-500 mb-4 text-center">Upload multiple
                            images for your home page banner slider.<br>Recommend 1920x800px or
                            similar wide aspect ratio.</p>

                        <label
                            class="px-8 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md cursor-pointer transition-all active:scale-95 text-xs font-black uppercase tracking-widest">
                            <i class="pi pi-plus-circle mr-2"></i> Add Images
                            <input type="file" multiple @change="onSliderChange" class="hidden" accept="image/*" />
                        </label>

                        <div v-if="form.front_slider.length > 0" class="mt-4 flex flex-wrap gap-2 justify-center">
                            <Tag v-for="(file, fIdx) in form.front_slider" :key="fIdx" :value="file.name"
                                severity="info" rounded icon="pi pi-file" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
