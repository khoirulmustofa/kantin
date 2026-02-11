export function useSettings() {
    const page = usePage();
    const allSettings = computed(() => page.props.settings || {});

    const getSetting = (key, defaultValue = null) => {
        return allSettings.value[key] || defaultValue;
    };

    // Tambahkan helper ini
    const getStorageUrl = (key) => {
        const path = getSetting(key);
        return path ? `/storage/${path}` : '/images/placeholder-logo.png';
    };

    return { getSetting, getStorageUrl };
}

// import { useSettings } from '@/Composables/useSettings';
// const { getStorageUrl } = useSettings();
// </script>

// <template>
//     <img :src="getStorageUrl('site_logo')" alt="Logo" class="h-8 w-auto">
// </template>