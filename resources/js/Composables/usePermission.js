import { usePage } from '@inertiajs/vue3';

export function usePermission() {
    const page = usePage();

    const hasRole = (name) => {
        return page.props.auth.user?.roles.includes(name) ?? false;
    };

    const can = (permission) => {
        return page.props.auth.user?.permissions.includes(permission) ?? false;
    };

    // Helper untuk cek banyak permission sekaligus (OR)
    const canAny = (permissions) => {
        if (!Array.isArray(permissions)) return can(permissions);
        return permissions.some(p => can(p));
    };

    return { hasRole, can, canAny };
}

{/* <script setup>
import { usePermission } from '@/Composables/usePermission';

const { can, hasRole } = usePermission();
</script>

<template>
    <div>
        <button v-if="can('hapus produk')" class="text-red-500">
            Hapus Produk
        </button>

        <div v-if="hasRole('admin')" class="badge">
            Mode Administrator
        </div>
    </div>
</template> */}