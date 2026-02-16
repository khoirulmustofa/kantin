<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, nextTick } from 'vue';

// Import Swiper core and required modules
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

// Import Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

import FrontLayout from '@/Layouts/FrontLayout.vue';
import { useCartStore } from '@/Stores/cart';
import { formatCurrencyIndo } from '@/Utils/formatter';

const cartStore = useCartStore();
const page = usePage();

const props = defineProps({
    menu: String,
    title: String,
    products: Array,
    categories: Array,
});

const sliderImages = ref([]);
const settings = computed(() => page.props.settings);

onMounted(async () => {
    updateSliderImages();

    await nextTick();

    const swiper = new Swiper('.main-slider', {
        modules: [Navigation, Pagination, Autoplay],
        loop: true,
        autoplay: {
            delay: 5000,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
});

const updateSliderImages = () => {
    try {
        const sliderData = settings.value.front_slider;
        if (sliderData) {
            sliderImages.value = typeof sliderData === 'string'
                ? JSON.parse(sliderData)
                : sliderData;
        } else {
            sliderImages.value = [];
        }
    } catch (e) {
        sliderImages.value = [];
    }
};

const whatsappUrl = computed(() => {
    const phone = page.props.settings?.whatsapp_number || '';
    const name = page.props.settings?.site_name || 'Admin';
    const message = encodeURIComponent(
        `Assalamualaikum ${name},\n\nSaya ingin menawarkan kerja sama sebagai vendor/supplier.`
    );
    return `https://wa.me/${phone}?text=${message}`;
});

</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">

        <!-- Slider -->
        <section id="product-slider" class="relative overflow-hidden mb-4">
            <div class="main-slider swiper h-48 md:h-64 rounded-b-[2rem] shadow-sm overflow-hidden bg-white">
                <div v-if="sliderImages.length > 0" class="swiper-wrapper h-full">
                    <div v-for="(img, index) in sliderImages" :key="index" class="swiper-slide relative h-full">
                        <img :src="`/storage/${img}`" alt="Slider Image" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    </div>
                </div>
                <div class="swiper-pagination !bottom-4"></div>
            </div>
        </section>

        <!-- Categories (Mobile Horizontal Scroll) -->
        <section id="categories" class="mb-4 px-4 bg-gradient-to-r from-blue-50 via-gray-50 to-white rounded-2xl py-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-black text-gray-900 tracking-tighter">Kategori</h2>
                <Link :href="route('product.index')"
                    class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full">
                    Lihat Semua
                </Link>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-3 gap-y-8 gap-x-2 justify-items-center ">
                <Link v-for="category in categories" :key="category.id"
                    :href="route('product.index', { category: category.slug })"
                    class="flex flex-col items-center gap-3 w-full group">
                    <div class="relative">
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-[2rem] bg-green-50 flex items-center justify-center p-4 transition-all duration-300 group-hover:bg-green-200 group-hover:border-green-100 group-hover:-translate-y-1">
                            <img :src="category.image ? `/storage/${category.image}` : '/assets/images/placeholder.webp'"
                                :alt="category.name"
                                class="w-full h-full object-contain filter drop-shadow-sm transition-transform group-hover:scale-110">
                        </div>
                    </div>

                    <span
                        class="text-[11px] font-bold text-gray-700 text-center leading-tight line-clamp-2 px-1 break-words w-full transition-colors group-hover:text-green-600">
                        {{ category.name }}
                    </span>
                </Link>
            </div>
        </section>

        <!-- Popular Products -->
        <section id="popular-products" class="bg-gradient-to-r from-blue-50 via-gray-50 to-white px-4 py-4 rounded-2xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Produk Pilihan</h2>
            </div>

            <!-- Grid 2 Columns for Mobile -->
            <div class="grid grid-cols-2 gap-4">
                <div v-for="product in products" :key="product.id"
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full hover:shadow-md transition-shadow">

                    <Link :href="route('product.show', product.slug)" class="relative aspect-square block bg-gray-50">
                        <img v-if="product.images && product.images.length > 0"
                            :src="`/storage/${product.images[0].image}`" :alt="product.name"
                            class="w-full h-full object-cover">
                        <img v-else src="/assets/images/placeholder.webp" :alt="product.name"
                            class="w-full h-full object-cover grayscale opacity-50">

                        <!-- Discount Badge (Example) -->
                        <span
                            class="absolute top-2 right-2 bg-green-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md shadow-sm">
                            {{ product.category?.name }}
                        </span>
                    </Link>

                    <div class="p-3 flex flex-col flex-grow">
                        <Link :href="route('product.show', product.slug)" class="mb-1">
                            <h3 class="text-xs font-bold text-gray-900 line-clamp-2 leading-snug">
                                {{ product.name }}
                            </h3>
                        </Link>

                        <div class="mt-auto">
                            <p class="text-sm font-black text-gray-900 text-green-600">
                                {{ formatCurrencyIndo(product.selling_price) }}
                            </p>
                            <p class="text-[10px] text-gray-400 line-through">
                                {{ formatCurrencyIndo(product.selling_price * 1.1) }}
                            </p>

                            <button @click.prevent="cartStore.addItem(product)" :disabled="product.stock === 0"
                                class="w-full mt-3 bg-green-50 text-green-700 hover:bg-green-600 hover:text-white border border-green-200 text-xs font-bold py-2 rounded-xl transition-colors flex items-center justify-center gap-1 disabled:opacity-50">
                                <i class="pi pi-plus text-[10px]"></i> Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </FrontLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}
</style>
