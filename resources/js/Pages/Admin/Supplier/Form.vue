<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    supplier: Object,
    isEdit: Boolean,
});

const visible = defineModel('visible', { type: Boolean, default: false });
const toast = useToast();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    contact_person: '',
    address: '',
    city: '',
    province: '',
    postal_code: '',
    is_active: true,
});

watch(() => props.supplier, (newVal) => {
    if (newVal && props.isEdit) {
        form.name = newVal.name || '';
        form.email = newVal.email || '';
        form.phone = newVal.phone || '';
        form.contact_person = newVal.contact_person || '';
        form.address = newVal.address || '';
        form.city = newVal.city || '';
        form.province = newVal.province || '';
        form.postal_code = newVal.postal_code || '';
        form.is_active = newVal.is_active === 1 || newVal.is_active === true;
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

const saveSupplier = () => {
    if (props.isEdit) {
        form.put(route('admin.suppliers.update', props.supplier.id), {
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Supplier Updated', life: 3000 });
            },
        });
    } else {
        form.post(route('admin.suppliers.store'), {
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'Supplier Created', life: 3000 });
            },
        });
    }
};
</script>

<template>
    <Dialog v-model:visible="visible" :style="{ width: '600px' }" :header="isEdit ? 'Edit Supplier' : 'New Supplier'"
        :modal="true" class="p-fluid" @hide="hideDialog">
        <div class="flex flex-col gap-6 mt-2">
            <div class="flex flex-col gap-2">
                <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-300">Supplier Name</label>
                <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" autocomplete="off"
                    placeholder="e.g. PT. Sumber Makmur" />
                <Message v-if="form.errors.name" severity="error" variant="simple">{{ form.errors.name }}</Message>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label for="contact_person" class="text-sm font-bold text-gray-700 dark:text-gray-300">Contact
                        Person</label>
                    <InputText id="contact_person" v-model="form.contact_person" :invalid="!!form.errors.contact_person"
                        placeholder="e.g. Budi Santoso" />
                    <Message v-if="form.errors.contact_person" severity="error" variant="simple">{{
                        form.errors.contact_person }}</Message>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="phone" class="text-sm font-bold text-gray-700 dark:text-gray-300">Phone Number</label>
                    <InputText id="phone" v-model="form.phone" :invalid="!!form.errors.phone"
                        placeholder="e.g. 0812xxxxxx" />
                    <Message v-if="form.errors.phone" severity="error" variant="simple">{{ form.errors.phone }}
                    </Message>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label for="email" class="text-sm font-bold text-gray-700 dark:text-gray-300">Email Address</label>
                <InputText id="email" v-model="form.email" :invalid="!!form.errors.email"
                    placeholder="e.g. contact@supplier.com" />
                <Message v-if="form.errors.email" severity="error" variant="simple">{{ form.errors.email }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="address" class="text-sm font-bold text-gray-700 dark:text-gray-300">Address</label>
                <Textarea id="address" v-model="form.address" rows="3" placeholder="Full address..." />
                <Message v-if="form.errors.address" severity="error" variant="simple">{{ form.errors.address }}
                </Message>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label for="city" class="text-sm font-bold text-gray-700 dark:text-gray-300">City</label>
                    <InputText id="city" v-model="form.city" placeholder="e.g. Jakarta" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="province" class="text-sm font-bold text-gray-700 dark:text-gray-300">Province</label>
                    <InputText id="province" v-model="form.province" placeholder="e.g. DKI Jakarta" />
                </div>
            </div>

            <div class="flex items-center gap-3">
                <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300">Active Status</label>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2 mt-4">
                <Button type="button" label="Cancel" severity="secondary" @click="hideDialog"
                    :disabled="form.processing" variant="text" />
                <Button type="button" label="Save Supplier" @click="saveSupplier" :loading="form.processing"
                    class="px-8" />
            </div>
        </template>
    </Dialog>
</template>
