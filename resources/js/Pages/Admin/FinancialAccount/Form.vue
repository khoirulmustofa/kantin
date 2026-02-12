<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    account: Object,
    isEdit: Boolean,
});

const visible = defineModel('visible', { type: Boolean, default: false });

const form = useForm({
    name: '',
    account_number: '',
    balance: 0,
    is_active: true,
});

watch(() => props.account, (newVal) => {
    if (newVal && props.isEdit) {
        form.name = newVal.name || '';
        form.account_number = newVal.account_number || '';
        form.balance = newVal.balance || 0;
        form.is_active = !!newVal.is_active;
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

const saveAccount = () => {
    if (props.isEdit) {
        form.put(route('admin.financial_accounts.update', props.account.id), {
            onSuccess: () => {
                hideDialog();
            },
        });
    } else {
        form.post(route('admin.financial_accounts.store'), {
            onSuccess: () => {
                hideDialog();
            },
        });
    }
};
</script>

<template>
    <Dialog v-model:visible="visible" :style="{ width: '450px' }" :header="isEdit ? 'Edit Account' : 'New Account'"
        :modal="true" class="p-fluid" @hide="hideDialog">
        <div class="flex flex-col gap-6 mt-2">
            <div class="flex flex-col gap-2">
                <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-300">Account Name</label>
                <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" autocomplete="off"
                    placeholder="e.g. Bank BCA, Petty Cash" />
                <Message v-if="form.errors.name" severity="error" variant="simple">{{ form.errors.name }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="account_number" class="text-sm font-bold text-gray-700 dark:text-gray-300">Account
                    Number</label>
                <InputText id="account_number" v-model="form.account_number" :invalid="!!form.errors.account_number"
                    autocomplete="off" placeholder="Optional" />
                <Message v-if="form.errors.account_number" severity="error" variant="simple">{{
                    form.errors.account_number }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="balance" class="text-sm font-bold text-gray-700 dark:text-gray-300">Initial Balance</label>
                <InputNumber id="balance" v-model="form.balance" mode="currency" currency="IDR" locale="id-ID"
                    :maxFractionDigits="0" :invalid="!!form.errors.balance" />
                <Message v-if="form.errors.balance" severity="error" variant="simple">{{ form.errors.balance }}
                </Message>
            </div>

            <div class="flex items-center gap-3">
                <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300">Active Status</label>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2 mt-6">
                <Button type="button" label="Cancel" severity="secondary" @click="hideDialog"
                    :disabled="form.processing" variant="text" />
                <Button type="button" label="Save Account" @click="saveAccount" :loading="form.processing"
                    class="px-6 shadow-lg shadow-emerald-500/20" />
            </div>
        </template>
    </Dialog>
</template>
