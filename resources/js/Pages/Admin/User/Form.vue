<script setup>
import { watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    user: Object,
    isEdit: Boolean,
});

// Sync visibility with Parent
const visible = defineModel('visible', { type: Boolean, default: false });

const toast = useToast();

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user',
});

const roles = ref([
    { name: 'Admin', code: 'admin' },
    { name: 'User', code: 'user' },
    { name: 'Kasir', code: 'kasir' },
]);

// Sync data when user prop changes
watch(() => props.user, (newUser) => {
    if (newUser && props.isEdit) {
        form.name = newUser.name || '';
        form.email = newUser.email || '';
        form.role = newUser.role || 'user';
        form.password = '';
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

const saveUser = () => {
    if (props.isEdit) {
        form.put(route('admin.users.update', props.user.id), {
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'User Updated', life: 3000 });
            },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                hideDialog();
                toast.add({ severity: 'success', summary: 'Successful', detail: 'User Created', life: 3000 });
            },
        });
    }
};
</script>

<template>
    <Dialog v-model:visible="visible" :style="{ width: '450px' }" :header="isEdit ? 'Edit User' : 'New User'"
        :modal="true" class="p-fluid" @hide="hideDialog">
        <div class="flex flex-col gap-5 mt-2">
            <div class="flex flex-col gap-2">
                <label for="name" class="text-sm font-bold text-gray-700 dark:text-gray-300">Name</label>
                <InputText id="name" v-model="form.name" :invalid="!!form.errors.name" autocomplete="off"
                    placeholder="Full Name" />
                <Message v-if="form.errors.name" severity="error" variant="simple">{{ form.errors.name }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="email" class="text-sm font-bold text-gray-700 dark:text-gray-300">Email</label>
                <InputText id="email" v-model="form.email" :invalid="!!form.errors.email" autocomplete="off"
                    placeholder="email@example.com" />
                <Message v-if="form.errors.email" severity="error" variant="simple">{{ form.errors.email }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="role" class="text-sm font-bold text-gray-700 dark:text-gray-300">Role</label>
                <Select v-model="form.role" :options="roles" optionLabel="name" optionValue="code"
                    placeholder="Select Role" fluid :invalid="!!form.errors.role" />
                <Message v-if="form.errors.role" severity="error" variant="simple">{{ form.errors.role }}</Message>
            </div>

            <div class="flex flex-col gap-2">
                <label for="password" class="text-sm font-bold text-gray-700 dark:text-gray-300">
                    Password
                    <small v-if="isEdit" class="font-normal text-gray-400 ml-1">(Leave blank to keep current)</small>
                </label>
                <Password id="password" v-model="form.password" :feedback="!isEdit" toggleMask fluid
                    :invalid="!!form.errors.password" placeholder="••••••••" />
                <Message v-if="form.errors.password" severity="error" variant="simple">{{ form.errors.password }}
                </Message>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2 mt-4">
                <Button type="button" label="Cancel" severity="secondary" @click="hideDialog"
                    :disabled="form.processing" variant="text" />
                <Button type="button" label="Save User" @click="saveUser" :loading="form.processing" class="px-6" />
            </div>
        </template>
    </Dialog>
</template>