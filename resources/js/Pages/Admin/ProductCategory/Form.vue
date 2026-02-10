<script setup>
import { watch, ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    category: Object,
    isEdit: Boolean,
});

const visible = defineModel('visible', { type: Boolean, default: false });
const toast = useToast();
const imagePreview = ref(null);

const form = useForm({
    name: '',
    image: null,
    _method: 'POST', // Default for create
});

watch(() => props.category, (newVal) => {
    if (newVal && props.isEdit) {
        form.name = newVal.name || '';
        form.image = null; // Don't bind existing image string to file input
        imagePreview.value = newVal.image_url || null;
    } else {
        form.reset();
        form.clearErrors();
        imagePreview.value = null;
    }
}, { immediate: true });

const onFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const hideDialog = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
    imagePreview.value = null;
};

const saveCategory = () => {
    if (props.isEdit) {
        // Use POST with _method: PUT because multipart/form-data doesn't work with PUT
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.product-categories.update', props.category.id), {
            forceFormData: true,
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Category Updated', life: 3000 });
            },
        });
    } else {
        form.post(route('admin.product-categories.store'), {
            forceFormData: true,
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Category Created', life: 3000 });
            },
        });
    }
};
</script>

<template>
    <Dialog v-model:visible="visible" :style="{ width: '450px' }" :header="isEdit ? 'Edit Category' : 'New Category'"
        :modal="true" class="p-fluid" @hide="hideDialog">
        <div class="flex flex-col gap-6 mt-2">
            <div class="flex flex-col gap-2">
                <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-300">Category Name</label>
                <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" autocomplete="off"
                    placeholder="e.g. Food, Drinks" />
                <Message v-if="form.errors.name" severity="error" variant="simple">{{ form.errors.name }}</Message>
            </div>

            <div class="flex flex-col gap-3">
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Category Image</label>

                <!-- Image Preview Area -->
                <div
                    class="relative w-full h-48 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border-2 border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden group">
                    <img v-if="imagePreview" :src="imagePreview" alt="Preview"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div v-else class="flex flex-col items-center gap-2 text-gray-400">
                        <i class="pi pi-image text-4xl"></i>
                        <span class="text-xs">No image selected</span>
                    </div>

                    <!-- Overlay Upload Button -->
                    <div
                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <label for="file-upload"
                            class="cursor-pointer bg-white px-4 py-2 rounded-full text-sm font-bold text-gray-900 shadow-xl hover:scale-105 transition-transform">
                            {{ imagePreview ? 'Change Image' : 'Upload Image' }}
                        </label>
                    </div>
                </div>

                <input id="file-upload" type="file" @change="onFileSelect" accept="image/*" class="hidden" />
                <Message v-if="form.errors.image" severity="error" variant="simple">{{ form.errors.image }}</Message>
                <p class="text-[10px] text-gray-400 text-center">Supported: JPG, PNG, GIF (Max 2MB)</p>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2 mt-6">
                <Button type="button" label="Cancel" severity="secondary" @click="hideDialog"
                    :disabled="form.processing" variant="text" />
                <Button type="button" label="Save Category" @click="saveCategory" :loading="form.processing"
                    class="px-6" />
            </div>
        </template>
    </Dialog>
</template>
