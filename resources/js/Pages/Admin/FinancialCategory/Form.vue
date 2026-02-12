<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    category: Object,
    isEdit: Boolean,
});

const visible = defineModel('visible', { type: Boolean, default: false });

const form = useForm({
    name: '',
    type: 'income',
});

const typeOptions = [
    { label: 'Income', value: 'income' },
    { label: 'Expense', value: 'expense' },
];

watch(() => props.category, (newVal) => {
    if (newVal && props.isEdit) {
        form.name = newVal.name || '';
        form.type = newVal.type || 'income';
    } else {
        form.reset();
        form.clearErrors();
    }
}, { immediate: true });

const hideDialog = () => {
    visible.value = false;
    form.reset();
    form.clearErrors();
};

const saveCategory = () => {
    if (props.isEdit) {
        form.put(route('admin.financial_categories.update', props.category.id), {
            onSuccess: () => {
                hideDialog();
            },
        });
    } else {
        form.post(route('admin.financial_categories.store'), {
            onSuccess: () => {
                hideDialog();
            },
        });
    }
};
</script>

<template>
    <Dialog v-model:visible="visible" :style="{ width: '400px' }" :header="isEdit ? 'Edit Category' : 'New Category'"
        :modal="true" class="p-fluid" @hide="hideDialog">
        <div class="flex flex-col gap-6 mt-2">
            <div class="flex flex-col gap-2">
                <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-300">Category Name</label>
                <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" autocomplete="off"
                    placeholder="e.g. Sales, Rent, Supplies" />
                <Message v-if="form.errors.name" severity="error" variant="simple">{{ form.errors.name }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="type" class="text-sm font-bold text-gray-700 dark:text-gray-300">Transaction Type</label>
                <Select id="type" v-model="form.type" :options="typeOptions" optionLabel="label" optionValue="value"
                    placeholder="Select Type" :invalid="!!form.errors.type" />
                <Message v-if="form.errors.type" severity="error" variant="simple">{{ form.errors.type }}</Message>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2 mt-6">
                <Button type="button" label="Cancel" severity="secondary" @click="hideDialog"
                    :disabled="form.processing" variant="text" />
                <Button type="button" label="Save Category" @click="saveCategory" :loading="form.processing"
                    class="px-6 shadow-lg shadow-emerald-500/20" />
            </div>
        </template>
    </Dialog>
</template>
