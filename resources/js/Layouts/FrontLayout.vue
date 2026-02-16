<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { formatCurrencyIndo } from '@/Utils/formatter';
import { useToast } from "primevue/usetoast";

const page = usePage();
const toast = useToast();
const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 10;
};

const showFlashMessage = (flash) => {

    if (flash?.success) {
        toast.add({ severity: 'success', summary: 'Success', detail: flash.success, life: 3000 });
    }
    if (flash?.warning) {
        toast.add({ severity: 'warn', summary: 'Warning', detail: flash.warning, life: 5000 });
    }
    if (flash?.error) {
        toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 5000 });
    }
};

onMounted(() => {
    // Pastikan DOM dan PrimeVue Service siap sebelum menampilkan flash awal
    nextTick(() => {
        showFlashMessage(page.props.flash);
    });

    window.addEventListener('scroll', handleScroll);
});

// Watcher untuk navigasi antar halaman (Inertia Props Update)
watch(() => page.props.flash, (newFlash) => {
    showFlashMessage(newFlash);
}, { deep: true });

const cartStore = useCartStore();

// State untuk UI
const isMobileMenuOpen = ref(false);

const menuActive = defineModel('menuActive');
const title = defineModel('title');


onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const menus = computed(() => [
    {
        label: 'Home',
        href: '/',
        icon: 'pi pi-home',
        active: menuActive.value === 'home'
    },
    {
        label: 'Product',
        href: '/product',
        icon: 'pi pi-th-large',
        active: menuActive.value === 'products'
    },
    {
        label: 'Cart',
        href: '/cart',
        icon: 'pi pi-shopping-cart',
        active: menuActive.value === 'cart',
        // badge tetap reaktif karena cartStore ada di dalam computed
        badge: cartStore.totalItems
    }
]);


</script>

<template>
    <Toast position="top-center" />
    <ConfirmDialog></ConfirmDialog>

    <!-- Main App Container -->
    <div class="min-h-screen bg-gray-100 flex justify-center font-sans text-gray-900">

        <!-- Mobile Width Wrapper -->
        <div class="w-full max-w-md bg-white min-h-screen shadow-2xl relative flex flex-col">

            <!-- Sticky Header -->
            <header :class="[
                'sticky top-0 z-50 transition-all duration-300',
                isScrolled ? 'bg-white/40 backdrop-blur-sm shadow-sm border-b border-gray-100 rounded-b-2xl' : 'bg-white'
            ]">
                <div class="flex items-center justify-between px-4 py-3">
                    <!-- Logo / Brand -->
                    <Link href="/" class="flex items-center gap-2">
                        <img v-if="$page.props.settings?.site_logo" :src="`/storage/${$page.props.settings?.site_logo}`"
                            alt="Logo" class="h-8 w-auto">
                        <span v-else class="text-xl font-black text-blue-600 tracking-tight">
                            {{ $page.props.settings?.site_name ?? "Kantin" }}
                        </span>
                    </Link>

                    <!-- Header Actions -->
                    <div class="flex items-center gap-3">
                        <Link v-if="$page.props.auth.user" href="/admin/dashboard"
                            class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 font-bold text-sm border border-blue-100">
                            {{ $page.props.auth.user.name.charAt(0) }}
                        </Link>
                        <Link v-else :href="route('login')"
                            class="px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-full shadow-lg shadow-blue-200">
                            Login
                        </Link>

                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-grow pb-24">
                <slot />
            </main>

            <!-- Bottom Navigation Bar -->
            <nav
                class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-green-500 border-t-2 z-50 px-6 py-3 pb-safe shadow-[0_-4px_20px_rgba(0,0,0,0.03)] rounded-t-[20px]">
                <ul class="flex justify-between items-center">
                    <li v-for="menu in menus" :key="menu.href">
                        <Link :href="menu.label === 'Account' && $page.props.auth.user ? '/admin/dashboard' : menu.href"
                            class="flex flex-col items-center gap-1 group relative p-2"
                            :class="menu.active ? '!bg-green-100 !text-green-900 !rounded-2xl w-16 h-16' : ''">
                            <div class="relative transition-all duration-300 transform group-active:scale-95"
                                :class="menu.active ? '-translate-y-1' : ''">
                                <i
                                    :class="[menu.icon, 'text-2xl transition-colors duration-300', menu.active ? 'text-green-600' : 'text-gray-400 group-hover:text-blue-500']"></i>

                                <!-- Badge for Cart -->
                                <span v-if="menu.label === 'Cart' && cartStore.totalItems > 0"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold h-4 w-4 flex items-center justify-center rounded-full border border-white">
                                    {{ cartStore.totalItems }}
                                </span>
                            </div>

                            <span class="text-[11px] font-bold tracking-wide transition-colors duration-300"
                                :class="menu.active ? 'text-green-600' : 'text-gray-400 group-hover:text-blue-500'">
                                {{ menu.label }}
                            </span>

                            <!-- Active Indicator Dot -->
                            <span v-if="menu.active" class="absolute -bottom-2 w-1 h-1 bg-blue-600 rounded-full"></span>
                        </Link>
                    </li>
                </ul>
            </nav>

        </div>
    </div>
</template>

<style scoped>
/* Safe area for mobile devices with notches/home bars */
.pb-safe {
    padding-bottom: env(safe-area-inset-bottom, 20px);
}

/* Hide scrollbar for cleaner look */
::-webkit-scrollbar {
    width: 0px;
    background: transparent;
}
</style>