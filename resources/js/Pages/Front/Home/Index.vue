<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, nextTick } from 'vue';


// Import Swiper core and required modules
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

// Import Swiper styles (WAJIB)
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

// Menggunakan computed agar reaktif jika data berubah
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
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
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
    
    // Gunakan \n untuk enter
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
        <section id="product-slider" class="relative overflow-hidden">
            <div class="main-slider swiper h-full overflow-hidden">
                <div v-if="sliderImages.length > 0" class="swiper-wrapper h-full">
                    <div v-for="(img, index) in sliderImages" :key="index" class="swiper-slide relative h-full">
                        <img :src="`/storage/${img}`" alt="Slider Image" class="w-full h-full object-cover">

                        <div
                            class="swiper-slide-content absolute inset-0 flex flex-col justify-center items-center text-center">
                            <h2 class="text-3xl md:text-7xl font-bold text-white mb-2 md:mb-4  tracking-tighter">
                                {{ settings.front_heading }}</h2>
                            <p class="mb-4 text-white md:text-2xl font-medium">{{ settings.front_sub_heading }}</p>
                            <Link :href="route('product.index')"
                                class="bg-green-600 hover:bg-white text-white hover:text-green-600 font-black px-10 py-4 rounded-2xl  text-xs tracking-widest transition-all shadow-2xl shadow-green-500/30">
                                Beli Sekarang
                            </Link>
                        </div>
                    </div>

                </div>

                <div class="swiper-button-prev !text-white opacity-50 hover:opacity-100 transition-all"></div>
                <div class="swiper-button-next !text-white opacity-50 hover:opacity-100 transition-all"></div>
                <div class="swiper-pagination"></div>
            </div>
        </section>

        <!-- Product banner section (Categories) -->
        <section id="product-banners" class="bg-white">
            <div class="container mx-auto py-20 px-4">
                <div class="flex flex-col items-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tighter">Kategori</h2>
                </div>
                <div class="flex flex-wrap -mx-4 justify-center">
                    <div v-for="category in categories" :key="category.id"
                        v-animateonscroll="{ enterClass: 'animate-enter fade-in-10 zoom-in-50 animate-duration-1000' }"
                        class="w-full sm:w-1/2 lg:w-1/4 px-4 mb-8">

                        <Link :href="route('product.index', { category: category.slug })"
                            class="group relative block overflow-hidden rounded-[2.5rem] aspect-[4/5] shadow-2xl shadow-gray-200/50 border border-gray-100">

                            <img :src="category.image ? `/storage/${category.image}` : '/assets/images/placeholder.webp'"
                                :alt="category.name"
                                class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity">
                            </div>

                            <div
                                class="absolute inset-0 flex flex-col items-center justify-end p-8 text-center text-white">

                                <h3
                                    class="text-xl font-black mb-4 tracking-tighter  group-hover:scale-105 transition-transform">
                                    {{ category.name }}
                                </h3>

                                <div
                                    class="w-10 h-1 bg-green-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-500 rounded-full">
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular product section -->
        <section id="popular-products" class="bg-gray-50 py-24">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-4">
                    <div class="flex flex-col">
                        <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tighter">Produk Pilihan
                        </h2>
                    </div>
                    <Link :href="route('product.index')"
                        class="text-xs font-black  tracking-widest text-gray-400 hover:text-green-600 transition-colors flex items-center gap-2">
                        Lihat Semua <i class="pi pi-arrow-right text-[10px]"></i>
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="product in products" :key="product.id"
                        v-animateonscroll="{ enterClass: 'animate-enter fade-in-10 slide-in-from-l-8 animate-duration-1000', leaveClass: 'animate-leave fade-out-0' }"
                        class="group bg-white p-4 rounded-3xl border border-gray-100 hover:border-green-100 transition-all duration-500 hover:shadow-2xl hover:shadow-green-200">
                        <div
                            class="relative aspect-[4/5] overflow-hidden rounded-3xl mb-6 bg-gray-50 border border-gray-100">
                            <img v-if="product.images && product.images.length > 0"
                                :src="`/storage/${product.images[0].image}`" :alt="product.name"
                                class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            <img v-else src="/assets/images/placeholder.webp" :alt="product.name"
                                class="w-full h-full object-cover grayscale opacity-50">
                            <div
                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-2">
                                <Link :href="route('product.show', product.slug)"
                                    class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-black hover:bg-green-600 hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-500 shadow-xl">
                                    <i class="pi pi-eye text-lg"></i>
                                </Link>
                                <button @click="cartStore.addItem(product)" :disabled="product.stock === 0"
                                    class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-black hover:bg-green-600 hover:text-white transition-all transform translate-y-4 group-hover:translate-y-0 duration-700 shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="pi pi-cart-plus text-lg"></i>
                                </button>
                            </div>
                        </div>

                        <Link :href="route('product.show', product.slug)">
                            <h3
                                class="font-black text-gray-900 group-hover:text-green-600 transition-colors text-lg tracking-tight mb-2 ">
                                {{ product.name }}</h3>
                            <div class="flex"> <span
                                    class="inline-flex items-center bg-green-600/10 text-green-600 text-[10px] font-bold px-2.5 py-0.5 rounded-full tracking-wider mb-4 border border-green-600/20">
                                    {{ product.category?.name }}
                                </span>
                            </div>
                            <div
                                class="text-2xl font-black flex justify-between items-center py-2 border-t border-gray-50">
                                <span class="font-black text-gray-900 tracking-tighter">{{
                                    formatCurrencyIndo(product.selling_price) }}</span>

                            </div>
                        </Link>

                    </div>
                </div>
            </div>
        </section>




        <!-- Banner section -->
        <section id="banner"
            v-animateonscroll="{ enterClass: 'animate-enter fade-in-10 slide-in-from-l-8 animate-duration-1000', leaveClass: 'animate-leave fade-out-0' }"
            class="relative bg-gray-100 py-16">
            <div class="container mx-auto px-4 py-20 rounded-lg relative bg-cover bg-center"
                style="background-image: url('assets/images/banner1.jpg');">
                <div class="absolute inset-0 bg-black opacity-40 rounded-lg"></div>
                <div class="relative flex flex-col items-center justify-center h-full text-center text-white py-20">
                    <h2 class="text-4xl font-bold mb-4">Welcome to Our Shop</h2>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="bg-primary hover:bg-transparent text-white hover:text-primary border border-transparent hover:border-primary font-semibold px-4 py-2 rounded-full inline-block">Shop
                            Now</a>
                        <a href="#"
                            class="bg-primary hover:bg-transparent text-white hover:text-primary border border-transparent hover:border-primary font-semibold px-4 py-2 rounded-full inline-block">New
                            Arrivals</a>
                        <a href="#"
                            class="bg-primary hover:bg-transparent text-white hover:text-primary border border-transparent hover:border-primary font-semibold px-4 py-2 rounded-full inline-block">Sale</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Subscribe section -->
        <section id="cooperation"
            v-animateonscroll="{ enterClass: 'animate-enter fade-in-10 slide-in-from-l-8 animate-duration-1000', leaveClass: 'animate-leave fade-out-0' }"
            class="py-12 lg:py-24 bg-white border-t border-gray-100">
            <div class="container mx-auto px-4">
                <div
                    class="flex flex-col items-center bg-indigo-50/50 dark:bg-gray-900/40 rounded-[3rem] p-8 md:p-16 border border-indigo-50">

                    <div class="mb-10 max-w-2xl">
                        <h2 class="text-center text-2xl md:text-3xl font-black text-gray-900 tracking-tighter mb-4">
                            Jalin Kerja Sama <span class="text-primary">Saling Menguntungkan</span>
                        </h2>
                        <p class="text-center text-gray-600 leading-relaxed">
                            Kami membuka peluang bagi para vendor, supplier, atau pelaku UMKM untuk berkolaborasi
                            bersama
                            <strong>{{ $page.props.settings?.site_name || 'Koperasi NFBS Bogor' }}</strong>.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <a :href="whatsappUrl"
                            target="_blank"
                            class="group flex items-center gap-3 bg-green-500 border-2 border-green-500 hover:bg-green-600 hover:border-green-600 text-white font-black py-4 px-8 rounded-full transition-all shadow-xl shadow-green-200">
                            <i class="pi pi-whatsapp text-2xl"></i>
                            <span class="text-lg">Hubungi via WhatsApp</span>
                            <i class="pi pi-arrow-right transition-transform group-hover:translate-x-2"></i>
                        </a>
                    </div>

                </div>
            </div>
        </section>
    </FrontLayout>
</template>
