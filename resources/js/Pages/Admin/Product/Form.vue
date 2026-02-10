<script setup>
import { watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    product: Object,
    categories: Array,
    isEdit: Boolean,
});

const visible = defineModel('visible', { type: Boolean, default: false });
const toast = useToast();
const imagePreviews = ref([]);

const form = useForm({
    name: '',
    description: '',
    cost_price: 0,
    selling_price: 0,
    stock: 0,
    is_active: true,
    product_category_id: null,
    images: [],
    deleted_images: [],
    _method: 'POST',
});

watch(() => props.product, (newVal) => {
    if (newVal && props.isEdit) {
        form.name = newVal.name || '';
        form.description = newVal.description || '';
        form.cost_price = newVal.cost_price || 0;
        form.selling_price = newVal.selling_price || 0;
        form.stock = newVal.stock || 0;
        form.is_active = newVal.is_active === 1 || newVal.is_active === true;
        form.product_category_id = newVal.product_category_id || null;
        form.images = [];
        form.deleted_images = [];
        imagePreviews.value = newVal.images ? newVal.images.map(img => ({ id: img.id, url: img.image_url, isExisting: true })) : [];
    } else {
        form.reset();
        form.clearErrors();
        imagePreviews.value = [];
    }
}, { immediate: true });

const onFileSelect = (event) => {
    const files = Array.from(event.target.files);
    files.forEach(file => {
        form.images.push(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value.push({ url: e.target.result, isExisting: false, file });
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    const img = imagePreviews.value[index];
    if (img.isExisting) {
        // Track ID for backend deletion
        form.deleted_images.push(img.id);
    } else {
        // Remove from local upload array
        const fileIndex = form.images.indexOf(img.file);
        if (fileIndex > -1) form.images.splice(fileIndex, 1);
    }
    // Remove from UI preview
    imagePreviews.value.splice(index, 1);
};

const hideDialog = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
    imagePreviews.value = [];
};

const saveProduct = () => {
    if (props.isEdit) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.products.update', props.product.id), {
            forceFormData: true,
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Updated', life: 3000 });
            },
        });
    } else {
        form.post(route('admin.products.store'), {
            forceFormData: true,
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Created', life: 3000 });
            },
        });
    }
};
</script>

<template>
    <Dialog v-model:visible="visible" :style="{ width: '800px' }" :header="isEdit ? 'Edit Product' : 'New Product'"
        :modal="true" class="p-fluid" @hide="hideDialog">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-2">
            <!-- Left Column: Details -->
            <div class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-300">Product Name</label>
                    <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" autocomplete="off"
                        placeholder="e.g. Mineral Water" />
                    <Message v-if="form.errors.name" severity="error" variant="simple">{{ form.errors.name }}</Message>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="category" class="text-sm font-bold text-gray-700 dark:text-gray-300">Category</label>
                    <Select id="category" v-model="form.product_category_id" filter :options="categories"
                        optionLabel="name" optionValue="id" placeholder="Select a Category"
                        :invalid="!!form.errors.product_category_id" />
                    <Message v-if="form.errors.product_category_id" severity="error" variant="simple">{{
                        form.errors.product_category_id }}</Message>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="cost_price" class="text-sm font-bold text-gray-700 dark:text-gray-300">Cost
                            Price</label>
                        <InputNumber id="cost_price" v-model="form.cost_price" mode="currency" fluid currency="IDR"
                            locale="id-ID" :maxFractionDigits="0" :invalid="!!form.errors.cost_price" />
                        <Message v-if="form.errors.cost_price" severity="error" variant="simple">{{
                            form.errors.cost_price }}
                        </Message>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="selling_price" class="text-sm font-bold text-gray-700 dark:text-gray-300">Selling
                            Price</label>
                        <InputNumber id="selling_price" v-model="form.selling_price" mode="currency" fluid
                            currency="IDR" locale="id-ID" :maxFractionDigits="0"
                            :invalid="!!form.errors.selling_price" />
                        <Message v-if="form.errors.selling_price" severity="error" variant="simple">{{
                            form.errors.selling_price }}
                        </Message>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="stock" class="text-sm font-bold text-gray-700 dark:text-gray-300">Available
                        Stock</label>
                    <InputNumber id="stock" v-model="form.stock" :invalid="!!form.errors.stock" showButtons :min="0"
                        placeholder="0" />
                    <Message v-if="form.errors.stock" severity="error" variant="simple">{{ form.errors.stock }}
                    </Message>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="description"
                        class="text-sm font-bold text-gray-700 dark:text-gray-300">Description</label>
                    <Textarea id="description" v-model="form.description" rows="5"
                        placeholder="Optional product description..." />
                    <Message v-if="form.errors.description" severity="error" variant="simple">{{ form.errors.description
                    }}</Message>
                </div>

                <div class="flex items-center gap-3">
                    <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                    <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300">Active
                        Status</label>
                </div>
            </div>

            <!-- Right Column: Images -->
            <div class="flex flex-col gap-4">
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Product Images</label>

                <!-- Gallery Grid -->
                <div class="grid grid-cols-2 gap-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                    <div v-for="(img, index) in imagePreviews" :key="index"
                        class="relative aspect-square rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 group bg-gray-50 dark:bg-gray-800">
                        <img :src="img.url" class="w-full h-full object-cover" />
                        <div
                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <Button icon="pi pi-trash" severity="danger" rounded @click="removeImage(index)" />
                        </div>
                        <div v-if="img.isExisting"
                            class="absolute top-2 left-2 bg-emerald-500 text-[8px] text-white px-1.5 py-0.5 rounded font-black uppercase tracking-wider shadow-sm">
                            Saved</div>
                    </div>

                    <!-- Upload Trigger -->
                    <label for="multi-upload"
                        class="aspect-square rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors text-gray-400 group">
                        <i class="pi pi-plus-circle text-2xl group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] uppercase font-black tracking-widest">Add Image</span>
                        <input id="multi-upload" type="file" multiple @change="onFileSelect" accept="image/*"
                            class="hidden" />
                    </label>
                </div>
                <Message v-if="form.errors.images" severity="error" variant="simple">{{ form.errors.images }}</Message>
                <p class="text-[10px] text-gray-400 text-center italic">Supported: JPG, PNG, GIF (Max 2MB per file)</p>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2 mt-6 border-t border-gray-50 dark:border-gray-800 pt-6 px-1">
                <Button type="button" label="Cancel" severity="secondary" @click="hideDialog"
                    :disabled="form.processing" variant="text" />
                <Button type="button" label="Save Product" @click="saveProduct" :loading="form.processing"
                    class="px-8 shadow-lg shadow-emerald-500/20" />
            </div>
        </template>
    </Dialog>
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
