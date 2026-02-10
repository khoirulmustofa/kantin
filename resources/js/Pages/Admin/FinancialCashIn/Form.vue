<script setup>
import { watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    mutation: Object,
    isEdit: Boolean,
    accounts: Array,
    categories: Array,
});

const visible = defineModel('visible', { type: Boolean, default: false });
const toast = useToast();
const imagePreview = ref(null);

const form = useForm({
    financial_account_id: null,
    financial_category_id: null,
    amount: 0,
    transaction_date: new Date().toISOString().split('T')[0],
    description: '',
    proof_image: null,
});

watch(() => props.mutation, (newVal) => {
    if (newVal && props.isEdit) {
        form.financial_account_id = newVal.financial_account_id;
        form.financial_category_id = newVal.financial_category_id;
        form.amount = parseFloat(newVal.amount);
        form.transaction_date = newVal.transaction_date;
        form.description = newVal.description || '';
        form.proof_image = null;
        imagePreview.value = newVal.proof_image_url || null;
    } else {
        form.reset();
        form.clearErrors();
        form.transaction_date = new Date().toISOString().split('T')[0];
        imagePreview.value = null;
    }
}, { immediate: true });

const onFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.proof_image = file;
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

const saveInflow = () => {
    if (props.isEdit) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.financial_cash_in.update', props.mutation.id), {
            forceFormData: true,
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Inflow Updated', life: 3000 });
            },
        });
    } else {
        form.post(route('admin.financial_cash_in.store'), {
            forceFormData: true,
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Inflow Recorded', life: 3000 });
            },
        });
    }
};
</script>

<template>
    <Dialog v-model:visible="visible" :style="{ width: '500px' }" :header="isEdit ? 'Edit Inflow' : 'New Cash Inflow'"
        :modal="true" class="p-fluid" @hide="hideDialog">
        <div class="flex flex-col gap-5 mt-2">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label for="date" class="text-sm font-bold text-gray-700 dark:text-gray-300">Transaction
                        Date</label>
                    <InputText id="date" type="date" v-model="form.transaction_date"
                        :invalid="!!form.errors.transaction_date" />
                    <Message v-if="form.errors.transaction_date" severity="error" variant="simple">{{
                        form.errors.transaction_date }}</Message>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="amount" class="text-sm font-bold text-gray-700 dark:text-gray-300">Amount</label>
                    <InputNumber id="amount" v-model="form.amount" mode="currency" currency="IDR" locale="id-ID"
                        :maxFractionDigits="0" :invalid="!!form.errors.amount" />
                    <Message v-if="form.errors.amount" severity="error" variant="simple">{{ form.errors.amount }}
                    </Message>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label for="account" class="text-sm font-bold text-gray-700 dark:text-gray-300">Receiver Account</label>
                <Select id="account" v-model="form.financial_account_id" :options="accounts" optionLabel="name"
                    optionValue="id" placeholder="Select Account" :invalid="!!form.errors.financial_account_id" />
                <Message v-if="form.errors.financial_account_id" severity="error" variant="simple">{{
                    form.errors.financial_account_id }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="category" class="text-sm font-bold text-gray-700 dark:text-gray-300">Income Category</label>
                <Select id="category" v-model="form.financial_category_id" :options="categories" optionLabel="name"
                    optionValue="id" placeholder="Select Category" :invalid="!!form.errors.financial_category_id" />
                <Message v-if="form.errors.financial_category_id" severity="error" variant="simple">{{
                    form.errors.financial_category_id }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="description" class="text-sm font-bold text-gray-700 dark:text-gray-300">Description</label>
                <Textarea id="description" v-model="form.description" rows="3" placeholder="Mutation details..."
                    :invalid="!!form.errors.description" />
                <Message v-if="form.errors.description" severity="error" variant="simple">{{ form.errors.description }}
                </Message>
            </div>

            <div class="flex flex-col gap-3">
                <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Proof of Transaction
                    (Optional)</label>
                <div
                    class="relative w-full h-32 rounded-xl bg-gray-50 dark:bg-gray-800/50 border-2 border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden group">
                    <img v-if="imagePreview" :src="imagePreview" alt="Proof" class="w-full h-full object-cover" />
                    <div v-else class="flex flex-col items-center gap-1 text-gray-400">
                        <i class="pi pi-camera text-2xl"></i>
                        <span class="text-[10px] uppercase font-bold tracking-wider">Upload Proof</span>
                    </div>
                    <div
                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <label for="proof-upload"
                            class="cursor-pointer bg-white px-3 py-1.5 rounded-full text-[10px] font-black text-gray-900 shadow-xl hover:scale-105 transition-transform">
                            {{ imagePreview ? 'Change Image' : 'Select File' }}
                        </label>
                    </div>
                </div>
                <input id="proof-upload" type="file" @change="onFileSelect" accept="image/*" class="hidden" />
                <Message v-if="form.errors.proof_image" severity="error" variant="simple">{{ form.errors.proof_image }}
                </Message>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2 mt-6">
                <Button type="button" label="Cancel" severity="secondary" @click="hideDialog"
                    :disabled="form.processing" variant="text" />
                <Button type="button" label="Record Inflow" @click="saveInflow" :loading="form.processing"
                    class="px-8 shadow-lg shadow-emerald-500/20" />
            </div>
        </template>
    </Dialog>
</template>
