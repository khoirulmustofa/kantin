<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useToast } from "primevue/usetoast";

const props = defineProps({
    menu: String,
    title: String,
    settings: Object,
});

const toast = useToast();
const activeSection = ref('general');

const form = useForm({
    site_name: props.settings.site_name || 'Kantin Digital',
    site_tagline: props.settings.site_tagline || 'Canteen Management System',
    site_description: props.settings.site_description || '',
    site_logo: null,
    contact_email: props.settings.contact_email || '',
    contact_phone: props.settings.contact_phone || '',
    address: props.settings.address || '',
    whatsapp_number: props.settings.whatsapp_number || '',
    facebook_url: props.settings.facebook_url || '',
    instagram_url: props.settings.instagram_url || '',
    currency: props.settings.currency || 'IDR',
    min_order: props.settings.min_order || '0',
    store_open: props.settings.store_open === '1',
    maintenance_mode: props.settings.maintenance_mode === '1',
});

const logoPreview = ref(props.settings.site_logo ? `/storage/${props.settings.site_logo}` : null);

const onLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.site_logo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const sections = [
    { id: 'general', label: 'General', icon: 'pi pi-cog', description: 'Basic site configuration and identity' },
    { id: 'contact', label: 'Contact', icon: 'pi pi-envelope', description: 'Public contact information and social media' },
    { id: 'store', label: 'Store', icon: 'pi pi-shopping-bag', description: 'Store behavior and operational settings' },
    { id: 'system', label: 'System', icon: 'pi pi-server', description: 'Advanced system and maintenance settings' },
];

const submit = () => {
    // Convert boolean back to string for backend storage
    const data = {
        ...form.data(),
        store_open: form.store_open ? '1' : '0',
        maintenance_mode: form.maintenance_mode ? '1' : '0',
    };

    form.post(route('admin.settings.update'), {
        forceFormData: true,
        onSuccess: () => {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Settings updated successfully', life: 3000 });
        },
    });
};
</script>

<template>

    <Head :title="props.title" />

    <AdminLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div
            class="card p-2 sm:p-2 bg-white dark:!bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300 min-w-0">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Navigation Sidebar -->
                <aside class="w-full lg:w-80 flex flex-col gap-2">
                    <button v-for="section in sections" :key="section.id" @click="activeSection = section.id"
                        class="group flex items-start gap-4 p-4 rounded-2xl transition-all duration-300 text-left"
                        :class="activeSection === section.id
                            ? 'bg-green-500 text-white shadow-xl shadow-gray-200 dark:shadow-none translate-x-1'
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark: hover:bg-gray-50 dark:hover:bg-gray-700 hover:translate-x-1'">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                            :class="activeSection === section.id ? 'bg-white/10' : 'bg-gray-100 dark:bg-gray-700'">
                            <i :class="[section.icon, 'text-lg']"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-black  tracking-tight text-sm leading-tight">{{ section.label }}
                            </h3>
                            <p class="text-sm opacity-90 font-medium leading-tight mt-1">{{ section.description }}
                            </p>
                        </div>
                    </button>
                </aside>

                <!-- Settings Content -->
                <div class="flex-1">
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- General Section -->
                        <div v-if="activeSection === 'general'"
                            class="animate-in fade-in slide-in-from-right-4 duration-500">
                            <div
                                class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-8">
                                <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-2">
                                    <div
                                        class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 rounded-2xl flex items-center justify-center">
                                        <i class="pi pi-cog text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-black text-gray-900 dark:text-white  tracking-tighter">
                                            General Settings</h2>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2 flex flex-col gap-4 mb-4">
                                        <label class="font-black tracking-widest text-sm text-gray-500">APPLICATION
                                            LOGO</label>
                                        <div
                                            class="flex items-center gap-8 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                                            <div class="relative flex-shrink-0">
                                                <div v-if="logoPreview"
                                                    class="w-full h-full overflow-hidden shadow-xl ring-4 ring-white dark:ring-gray-700">
                                                    <img :src="logoPreview" class="w-full h-full object-cover"
                                                        alt="Logo Preview" />
                                                </div>
                                                <div v-else
                                                    class="w-full h-full rounded-2xl bg-white dark:bg-gray-800 flex flex-col items-center justify-center border border-gray-100 dark:border-gray-700 shadow-inner">
                                                    <i class="pi pi-image text-3xl text-gray-200"></i>
                                                    <span class="text-[8px] font-black uppercase text-gray-300 mt-2">No
                                                        Logo</span>
                                                </div>
                                                <button type="button" v-if="logoPreview && form.site_logo"
                                                    @click="form.site_logo = null; logoPreview = props.settings.site_logo ? `/storage/${props.settings.site_logo}` : null"
                                                    class="absolute -top-2 -right-2 w-8 h-8 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-rose-600 transition-colors">
                                                    <i class="pi pi-times text-xs"></i>
                                                </button>
                                            </div>
                                            <div class="flex-1 space-y-3">
                                                <p class="text-xs font-bold text-gray-500">Upload your organization's
                                                    logo. Recommended size: 512x512px (PNG, SVG, or JPG).</p>
                                                <label
                                                    class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 cursor-pointer transition-all active:scale-95">
                                                    <i class="pi pi-upload"></i>
                                                    <span class="text-xs font-black uppercase tracking-widest">Choose
                                                        Image</span>
                                                    <input type="file" @change="onLogoChange" class="hidden"
                                                        accept="image/*" />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class="font-black  tracking-widest ">Site
                                            Name</label>
                                        <InputText v-model="form.site_name" placeholder="Kantin Digital"
                                            class="!py-2 !px-2 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest ">Site
                                            Tagline</label>
                                        <InputText v-model="form.site_tagline" placeholder="Modern management system"
                                            class="!py-2 !px-2 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                    <div class="flex flex-col gap-2 md:col-span-2">
                                        <label class=" font-black  tracking-widest ">Site
                                            Description</label>
                                        <Textarea v-model="form.site_description" rows="4"
                                            placeholder="Briefly describe your canteen..."
                                            class="!py-2 !px-2 bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Contact Section -->
                        <div v-if="activeSection === 'contact'"
                            class="animate-in fade-in slide-in-from-right-4 duration-500">
                            <div
                                class="card bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm space-y-8">
                                <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-6">
                                    <div
                                        class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center">
                                        <i class="pi pi-envelope text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-black text-gray-900 dark:text-white  tracking-tighter">
                                            Contact & Social</h2>
                                        <p class=" font-bold   tracking-widest mt-1">
                                            How customers can reach you</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest ">Email
                                            Address</label>
                                        <InputText v-model="form.contact_email" type="email"
                                            class="bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest ">Phone
                                            Number</label>
                                        <InputText v-model="form.contact_phone"
                                            class="bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest ">WhatsApp</label>
                                        <InputText v-model="form.whatsapp_number" placeholder="628..."
                                            class="bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest ">Address</label>
                                        <InputText v-model="form.address"
                                            class="bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest  text-blue-600"><i
                                                class="pi pi-facebook mr-1"></i> Facebook URL</label>
                                        <InputText v-model="form.facebook_url"
                                            class="bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest  text-rose-500"><i
                                                class="pi pi-instagram mr-1"></i> Instagram URL</label>
                                        <InputText v-model="form.instagram_url"
                                            class="bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Store Section -->
                        <div v-if="activeSection === 'store'"
                            class="animate-in fade-in slide-in-from-right-4 duration-500">
                            <div
                                class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                                <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-4">
                                    <div
                                        class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 text-amber-600 rounded-2xl flex items-center justify-center">
                                        <i class="pi pi-shopping-bag text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-black text-gray-900 dark:text-white  tracking-tighter">
                                            Operational Settings</h2>
                                        <p class=" font-bold   tracking-widest mt-1">
                                            Configure your store behavior</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest ">Currency
                                            Code</label>
                                        <InputText v-model="form.currency"
                                            class=" bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class=" font-black  tracking-widest ">Minimum
                                            Order Amount</label>
                                        <InputNumber v-model="form.min_order" class="!w-full"
                                            inputClass=" bg-gray-50 border-transparent focus:bg-white transition-all shadow-inner" />
                                    </div>

                                    <div
                                        class="md:col-span-2 flex items-center justify-between p-4 bg-amber-50/50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center text-amber-600">
                                                <i class="pi pi-clock"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-black text-gray-900 dark:text-white ">
                                                    Store Open Status</h4>
                                                <p class=" text-gray-500 font-medium">Temporarily close the
                                                    store for ordering</p>
                                            </div>
                                        </div>
                                        <ToggleSwitch v-model="form.store_open" severity="warning" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Section -->
                        <div v-if="activeSection === 'system'"
                            class="animate-in fade-in slide-in-from-right-4 duration-500">
                            <div
                                class="card bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                                <div class="flex items-center gap-4 border-b border-gray-50 dark:border-gray-700 pb-4">
                                    <div
                                        class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 text-rose-600 rounded-2xl flex items-center justify-center">
                                        <i class="pi pi-server text-xl"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-black text-gray-900 dark:text-white  tracking-tighter">
                                            System & Maintenance</h2>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div
                                        class="flex items-center justify-between p-6 bg-rose-50/50 dark:bg-rose-900/10 rounded-3xl border border-rose-100 dark:border-rose-900/30">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 bg-rose-100 dark:bg-rose-900/50 rounded-xl flex items-center justify-center text-rose-600">
                                                <i class="pi pi-lock"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-black text-gray-900 dark:text-white ">
                                                    Maintenance Mode</h4>
                                                <p class=" text-gray-500 font-medium">Show a maintenance page
                                                    to visitors</p>
                                            </div>
                                        </div>
                                        <ToggleSwitch v-model="form.maintenance_mode" severity="danger" />
                                    </div>

                                    <div
                                        class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-gray-100 dark:border-gray-800">
                                        <h4 class=" font-black   tracking-[0.2em] mb-4">
                                            Storage Info</h4>
                                        <div
                                            class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                                            <i class="pi pi-database opacity-50"></i>
                                            <span>Environment: {{ $page.props.auth.user ? 'Production' : 'Local'
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sticky Footer -->
                        <div class="sticky bottom-8 z-10 flex justify-end">
                            <Button type="submit" label="Update Settings" icon="pi pi-check"
                                class="!rounded-xl !py-3 !px-6 !bg-green-500 !border-green-500 hover:!bg-green-600 !font-black !text-sm ! !tracking-widest shadow-2xl shadow-gray-400 dark:shadow-none transition-all duration-300 active:scale-95"
                                :loading="form.processing" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.card {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

:deep(.p-inputtext),
:deep(.p-textarea-root) {
    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
}

:deep(.p-inputtext:focus),
:deep(.p-textarea:focus) {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
