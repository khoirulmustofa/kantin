<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, computed } from 'vue';
import { formatCurrencyIndo } from '@/Utils/formatter';
import { useCartStore } from '@/Stores/cart';

// Swiper
import Swiper from 'swiper';
import { Pagination, Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';


const cartStore = useCartStore();
const props = defineProps({
    product: Object,
    related_products: Array,
    menu: String,
    title: String,
});

const quantity = ref(1);
const showFullDescription = ref(false);

onMounted(async () => {
    await nextTick();
    new Swiper('.product-slider', {
        modules: [Pagination],
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
});

const scrollContainer = ref(null);

const scroll = (direction) => {
    const container = scrollContainer.value;
    const scrollAmount = 280; // Sesuaikan jarak scroll (misal 2 kartu)

    if (direction === 'left') {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
};

const productImages = computed(() => {
    if (!props.product.images || props.product.images.length === 0) {
        return [{
            itemImageSrc: '/assets/images/placeholder.webp',
            thumbnailImageSrc: '/assets/images/placeholder.webp'
        }];
    }
    return props.product.images.map(img => ({
        itemImageSrc: `/storage/${img.image}`,
        thumbnailImageSrc: `/storage/${img.image}`,
        alt: props.product.name
    }));
});


const responsiveOptions = [
    {
        breakpoint: '991px',
        numVisible: 4
    },
    {
        breakpoint: '767px',
        numVisible: 3,
        showThumbnails: false // Sembunyikan thumbnail di HP agar mirip Swiper
    }
];

const addToCart = () => {
    cartStore.addItem(product, quantity.value);
};
</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">
        <div class="pb-24 bg-gray-100 min-h-screen">
            <!-- Image Slider -->
            <div class="bg-white mb-2">
                <div class="bg-white overflow-hidden shadow-sm">
                    <Galleria :value="productImages" :responsiveOptions="responsiveOptions" :numVisible="5"
                        containerClass="w-full" :showThumbnails="true" :showIndicators="false" :circular="true"
                        :autoPlay="true" :transitionInterval="3000">
                        <template #item="slotProps">
                            <div class="w-full aspect-square bg-gray-50 flex items-center justify-center p-2">
                                <img :src="slotProps.item.itemImageSrc" :alt="product.name"
                                    class="!w-full object-contain rounded-b-2xl" />
                            </div>
                        </template>

                        <template #thumbnail="slotProps">
                            <img :src="slotProps.item.thumbnailImageSrc" :alt="product.name"
                                class="w-16 h-16 object-cover rounded-lg" />
                        </template>
                    </Galleria>
                </div>
            </div>



            <!-- Main Info -->
            <div class="bg-white p-4 mb-2">
                <div class="flex items-baseline gap-2 mb-1">
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{
                        formatCurrencyIndo(product.selling_price) }}</h1>
                    <span class="text-xs text-gray-400 line-through">{{ formatCurrencyIndo(product.selling_price * 1.25)
                        }}</span>
                    <span class="text-xs text-red-500 font-bold bg-red-50 px-1 py-0.5 rounded">-20%</span>
                </div>

                <h2 class="text-sm font-medium text-gray-800 leading-snug mb-2">{{ product.name }}</h2>


            </div>

            <!-- Variant / Quantity -->
            <div class="bg-white p-4">
                <h3 class="font-bold text-sm mb-3">Jumlah</h3>
                <div class="flex items-center gap-4">
                    <div class="flex items-center border border-gray-200 rounded-lg">
                        <button
                            class="w-8 h-11 flex items-center font-bold justify-center text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                            @click="quantity > 1 ? quantity-- : null" :disabled="quantity <= 1"><i
                                class="pi pi-minus"></i></button>
                        <input type="number" v-model="quantity"
                            class="w-12 text-center text-sm font-bold border-none focus:ring-0 p-0" readonly>
                        <button
                            class="w-8 h-11 flex items-center font-bold justify-center text-green-600 hover:bg-gray-50"
                            @click="quantity < product.stock ? quantity++ : null"><i class="pi pi-plus"></i> </button>
                    </div>
                    <button
                        class="w-full flex-1 justify-center items-center py-3 bg-green-600 text-white font-bold text-sm rounded-lg shadow-lg shadow-green-200 active:scale-95 transition-transform"
                        @click="cartStore.addItem(product, quantity);">
                        Tambahkan ke Keranjang
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white p-4 mb-2">
                <h3 class="font-bold text-sm mb-3">Deskripsi Barang</h3>
                <div class="text-sm text-gray-600 leading-relaxed overflow-hidden relative"
                    :class="{ 'max-h-32': !showFullDescription }">
                    <div v-html="product.description || 'Tidak ada deskripsi.'"></div>

                    <div v-if="!showFullDescription"
                        class="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-white to-transparent"></div>
                </div>
                <button @click="showFullDescription = !showFullDescription"
                    class="text-green-600 text-sm font-bold mt-2">
                    {{ showFullDescription ? 'Lihat Lebih Sedikit' : 'Baca Selengkapnya' }}
                </button>
            </div>

            <!-- Related Products -->


            <div class="bg-white p-4 mb-20" v-if="related_products.length > 0">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-sm">Produk Lainnya</h3>
                    <Link :href="route('product.index')" class="text-xs font-bold text-green-600">Lihat Semua</Link>
                </div>

              

                <div class="relative group">
                    <button @click="scroll('left')"
                        class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/80 backdrop-blur-md shadow-lg border border-gray-100 p-3 rounded-full -ml-4  md:flex items-center justify-center hover:bg-green-600 hover:text-white transition-all">
                        <i class="pi pi-chevron-left text-xs"></i>
                    </button>

                    <div ref="scrollContainer"
                        class="flex overflow-x-auto gap-4 pb-4 no-scrollbar -mx-4 px-4 cursor-grab active:cursor-grabbing select-none scroll-smooth">
                        <Link v-for="rel in related_products" :key="rel.id" :href="route('product.show', rel.slug)"
                            class="min-w-[140px] w-[140px] bg-white border border-gray-100 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="aspect-square bg-gray-50">
                                <img v-if="rel?.images?.[0]?.image" :src="`/storage/${rel.images[0].image}`"
                                    class="w-full h-full object-cover">
                                <img v-else src="/assets/images/placeholder.webp" class="w-full h-full object-cover">
                            </div>
                            <div class="p-2">
                                <p class="text-xs h-8 line-clamp-2 leading-tight mb-1 text-gray-800">{{ rel.name }}</p>
                                <p class="text-xs font-bold text-gray-900">{{ formatCurrencyIndo(rel.selling_price) }}
                                </p>
                            </div>
                        </Link>
                    </div>

                    <button @click="scroll('right')"
                        class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white/80 backdrop-blur-md shadow-lg border border-gray-100 p-3 rounded-full -mr-4  md:flex items-center justify-center hover:bg-green-600 hover:text-white transition-all">
                        <i class="pi pi-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

        </div>

        <!-- Sticky Bottom Action Bar -->


    </FrontLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.pb-safe {
    padding-bottom: env(safe-area-inset-bottom, 12px);
}
</style>
