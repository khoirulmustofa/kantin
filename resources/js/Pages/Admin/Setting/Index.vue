<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useConfirm } from "primevue/useconfirm";
import axios from 'axios';

// Import Modular Components
import GeneralSettings from './Components/GeneralSettings.vue';
import FrontendSettings from './Components/FrontendSettings.vue';
import ContactSettings from './Components/ContactSettings.vue';
import OperationalSettings from './Components/OperationalSettings.vue';
import SystemSettings from './Components/SystemSettings.vue';

const props = defineProps({
    menu: String,
    title: String,
    settings: Object,
});

const confirm = useConfirm();
const activeSection = ref('general');
const localSettings = ref({ ...props.settings });
const settings = ref([]);

const form = useForm({
    site_name: settings.value.site_name || 'Kantin Digital',
    site_tagline: settings.value.site_tagline || 'Canteen Management System',
    site_description: settings.value.site_description || '',
    site_logo: null,
    contact_email: settings.value.contact_email || '',
    contact_phone: settings.value.contact_phone || '',
    address: settings.value.address || '',
    whatsapp_number: settings.value.whatsapp_number || '',
    facebook_url: settings.value.facebook_url || '',
    instagram_url: settings.value.instagram_url || '',
    currency: settings.value.currency || 'IDR',
    min_order: settings.value.min_order || '0',
    store_open: settings.value.store_open === '1',
    register_open: settings.value.register_open === '1',
    front_heading: settings.value.front_heading || '',
    front_sub_heading: settings.value.front_sub_heading || '',
    front_slider: settings.value.front_slider || [], // For new uploads
});

const logoPreview = ref(settings.value.site_logo ? `/storage/${settings.value.site_logo}` : null);
const sliderImages = ref([]);

const updateSliderImages = () => {
    try {
        const sliderData = settings.value.front_slider;
        if (sliderData) {
            sliderImages.value = JSON.parse(sliderData);
        } else {
            sliderImages.value = [];
        }
    } catch (e) {
        sliderImages.value = [];
    }
};

onMounted(() => {
    fetchSettings();
});

const fetchSettings = async () => {
    try {
        const response = await axios.get(route('admin.settings.data'));
        if (response.data.status) {
            settings.value = response.data.settings;
            updateSliderImages();

            // Refresh logo preview from server data
            if (settings.value.site_logo) {
                logoPreview.value = `/storage/${settings.value.site_logo}`;
            } else {
                logoPreview.value = null;
            }

            // Sync form partially if needed, or just rely on localSettings for display
            // Usually we want to keep form for editing
            form.site_name = settings.value.site_name || '';
            form.site_tagline = settings.value.site_tagline || '';
            form.site_description = settings.value.site_description || '';
            form.contact_email = settings.value.contact_email || '';
            form.contact_phone = settings.value.contact_phone || '';
            form.address = settings.value.address || '';
            form.whatsapp_number = settings.value.whatsapp_number || '';
            form.facebook_url = settings.value.facebook_url || '';
            form.instagram_url = settings.value.instagram_url || '';
            form.currency = settings.value.currency || 'IDR';
            form.min_order = settings.value.min_order || '0';
            form.store_open = settings.value.store_open === '1';
            form.register_open = settings.value.register_open === '1';
            form.front_heading = settings.value.front_heading || '';
            form.front_sub_heading = settings.value.front_sub_heading || '';
        }
    } catch (error) {
        console.error('Failed to fetch settings', error);
    }
};

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

const onSliderChange = (e) => {
    const files = Array.from(e.target.files);
    form.front_slider = files;
};

const deleteSliderImage = async (path) => {
    confirm.require({
        message: `Are you sure you want to delete this slider image?`,
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: async () => {
            try {
                const response = await axios.post(route('admin.settings.delete_slider'), { image_path: path });
                if (response.data.status) {

                    await fetchSettings();
                }
            } catch (error) {
                console.log(error);
            }
        }
    });


};

const sections = [
    { id: 'general', label: 'General', icon: 'pi pi-cog', description: 'Basic site configuration and identity' },
    { id: 'frontend', label: 'Frontend', icon: 'pi pi-desktop', description: 'Frontend configuration' },
    { id: 'contact', label: 'Contact', icon: 'pi pi-envelope', description: 'Public contact information and social media' },
    { id: 'store', label: 'Store', icon: 'pi pi-shopping-bag', description: 'Store behavior and operational settings' },
    { id: 'system', label: 'System', icon: 'pi pi-server', description: 'Advanced system and maintenance settings' },
];

const submit = () => {
    form.post(route('admin.settings.update'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.front_slider = [];
            fetchSettings(); // Refresh local settings display
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
                            <h3 class="font-black tracking-tight leading-tight">{{ section.label }}</h3>
                            <p class="text-sm opacity-90 font-medium leading-tight mt-1">{{ section.description }}</p>
                        </div>
                    </button>
                </aside>

                <!-- Settings Content -->
                <div class="flex-1">
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Settings Sections -->
                        <GeneralSettings v-if="activeSection === 'general'" :form="form" :logoPreview="logoPreview"
                            :onLogoChange="onLogoChange" />

                        <FrontendSettings v-if="activeSection === 'frontend'" :form="form" :sliderImages="sliderImages"
                            :onSliderChange="onSliderChange" :deleteSliderImage="deleteSliderImage" />

                        <ContactSettings v-if="activeSection === 'contact'" :form="form" />

                        <OperationalSettings v-if="activeSection === 'store'" :form="form" />

                        <SystemSettings v-if="activeSection === 'system'" :form="form" />


                        <!-- Sticky Footer -->
                        <div class="sticky bottom-4 z-10 flex justify-end">
                            <Button type="submit" label="Save All Settings" icon="pi pi-save"
                                class="!rounded-2xl !py-4 !px-8 !bg-green-600 !border-none hover:!bg-green-700 !font-black !text-sm !tracking-widest shadow-2xl active:scale-95 transition-all"
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
