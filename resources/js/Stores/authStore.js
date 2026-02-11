import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const roles = ref([]);
    const permissions = ref([]);
    const isLoaded = ref(false);

    // Getters
    const can = computed(() => (p) => permissions.value.includes(p));
    const hasRole = computed(() => (r) => roles.value.includes(r));

    // Action: Ambil data dari API Laravel
    const fetchAuth = async () => {
        try {
            const response = await axios.get('/user/permissions');
            user.value = response.data.user;
            roles.value = response.data.roles;
            permissions.value = response.data.permissions;
            isLoaded.value = true;
        } catch (error) {
            console.error("Gagal memuat izin user", error);
            isLoaded.value = false;
        }
    };

    const resetAuth = () => {
        user.value = null;
        roles.value = [];
        permissions.value = [];
        isLoaded.value = false; // Set ke false agar aplikasi tahu data sudah kosong
    };

    return { user, roles, permissions, isLoaded, can, hasRole, fetchAuth, resetAuth };
});