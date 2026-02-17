<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { formatCurrencyIndo } from '@/Utils/formatter';
import debounce from 'lodash/debounce';
import { useCartStore } from '@/Stores/cart';

const cartStore = useCartStore();

const props = defineProps({
    menu: String,
    title: String,
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || null);

const filterProducts = () => {
    router.get(route('product.index'), {
        search: search.value,
        category: selectedCategory.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const onPageChange = (event) => {
    // PrimeVue event.page dimulai dari 0, Laravel dari 1
    const newPage = event.page + 1;

    router.get(route('product.index'), {
        // Pertahankan filter lain saat pindah halaman
        ...props.filters,
        page: newPage
    }, {
        preserveState: true,
        preserveScroll: true, // Sangat penting agar layar tidak melompat ke atas
        only: ['products'],   // Hanya update data products (Partial Reload)
    });
};

watch(search, debounce(() => {
    filterProducts();
}, 300));

const selectCategory = (slug) => {
    selectedCategory.value = selectedCategory.value === slug ? null : slug;
    filterProducts();
};

const catContainer = ref(null);

const scrollCategories = (direction) => {
    const container = catContainer.value;
    // Jarak scroll disesuaikan dengan kebutuhan (misal 200px)
    const scrollAmount = 200;

    if (direction === 'left') {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
};
</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title">

        <!-- Sticky Search & Filter Header -->
        <div class="sticky top-[60px] z-40 bg-white pb-2 shadow-sm">
            <div class="px-4 py-3">
                <IconField>
                    <InputIcon class="pi pi-search text-gray-400" />

                    <InputText v-model="search" placeholder="Cari di Kantin..."
                        class="w-full pl-10 pr-10 py-2 bg-gray-100 border-none rounded-2xl text-sm focus:ring-2 focus:ring-green-500 transition-all placeholder:text-gray-400 font-medium" />

                    <InputIcon v-if="search" @click="search = ''"
                        class="pi pi-times-circle right-3 cursor-pointer text-gray-400 hover:text-red-500 transition-colors" />
                </IconField>
            </div>

            <!-- Horizontal Category Filter -->
            <div
                class="relative group px-4 py-4 bg-gradient-to-r from-blue-50 via-green-50 to-white rounded-2xl border border-white/50 shadow-sm">

                <Button icon="pi pi-chevron-left" rounded outlined severity="secondary"
                    @click="scrollCategories('left')"
                    class="!absolute left-2 top-1/2 -translate-y-1/2 !z-10 !w-8 !h-8 !bg-white/90 !border-gray-100 !shadow-md opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex" />

                <div ref="catContainer"
                    class="flex overflow-x-auto gap-2 no-scrollbar scroll-smooth cursor-grab active:cursor-grabbing px-2">

                    <Button label="Semua" rounded size="small" :variant="!selectedCategory ? 'primary' : 'outlined'"
                        :severity="!selectedCategory ? 'success' : 'secondary'" @click="selectCategory(null)"
                        class="whitespace-nowrap !text-[12px] !font-bold !px-5 !shrink-0"
                        :class="{ 'shadow-md shadow-green-200': !selectedCategory }" />

                    <Button v-for="cat in categories" :key="cat.id" :label="cat.name" rounded size="small"
                        :variant="selectedCategory === cat.slug ? 'primary' : 'outlined'"
                        :severity="selectedCategory === cat.slug ? 'success' : 'secondary'"
                        @click="selectCategory(cat.slug)"
                        class="whitespace-nowrap !text-[12px] !font-bold !px-5 !shrink-0"
                        :class="{ 'shadow-md shadow-green-200': selectedCategory === cat.slug }" />
                </div>

                <Button icon="pi pi-chevron-right" rounded outlined severity="secondary"
                    @click="scrollCategories('right')"
                    class="!absolute right-2 top-1/2 -translate-y-1/2 !z-10 !w-8 !h-8 !bg-white/90 !border-gray-100 !shadow-md opacity-0 group-hover:opacity-100 transition-opacity hidden md:flex" />
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-4 py-4 min-h-[60vh] bg-gradient-to-r from-green-50 via-gray-50 to-white  rounded-2xl">

            <!-- Result Count (Optional) -->
            <div class="mb-4 text-xs font-medium text-gray-500" v-if="search || selectedCategory">
                Menampilkan {{ products.total }} produk
            </div>

            <!-- Product Grid -->
            <div v-if="products.data.length > 0" class="grid grid-cols-2 gap-4">
                <div v-for="product in products.data" :key="product.id">
                    <Card
                        class="h-full overflow-hidden border border-surface-100 shadow-sm hover:shadow-md transition-shadow group relative flex flex-col">
                        <template #header>
                            <Link :href="route('product.show', product.slug)"
                                class="relative aspect-square block bg-surface-50">
                                <img v-if="product.images.length > 0" :src="`/storage/${product.images[0].image}`"
                                    :alt="product.name"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />

                                <img v-else src="/assets/images/placeholder.webp"
                                    class="w-full h-full object-cover grayscale opacity-50" />

                                <Badge :value="product.category?.name" severity="success"
                                    class="absolute top-2 right-2 !text-[10px] !px-2" />
                            </Link>
                        </template>

                        <template #title>
                            <Link :href="route('product.show', product.slug)">
                                <h3 class="text-xs font-bold text-surface-900  leading-relaxed h-8 mb-0">
                                    {{ product.name }}
                                </h3>
                            </Link>
                        </template>

                        <template #content>
                            <div class="flex flex-col gap-1">
                                <p class="text-sm font-black text-green-600">
                                    {{ formatCurrencyIndo(product.selling_price) }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] text-surface-400 line-through">
                                        {{ formatCurrencyIndo(product.selling_price * 1.1) }}
                                    </span>
                                </div>
                            </div>
                        </template>

                        <template #footer>
                            <Button @click.prevent="cartStore.addItem(product)" :disabled="product.stock === 0"
                                icon="pi pi-plus" label="Keranjang" outlined severity="success" fluid
                                class="!py-1.5 !text-[12px] !font-bold" />
                        </template>
                    </Card>
                </div>
            </div>



            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <i class="pi pi-search text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Produk tidak ditemukan</h3>
                <p class="text-xs text-gray-500 max-w-[200px] mb-4">Coba kata kunci lain atau cek kategori yang berbeda.
                </p>
                <button @click="search = ''; selectedCategory = null; filterProducts();"
                    class="px-4 py-2 bg-green-50 text-green-600 rounded-lg text-xs font-bold hover:bg-green-100 transition-colors">
                    Reset Filter
                </button>
            </div>

            <!-- Pagination -->
            <div class="card mt-8 overflow-hidden">
                <Paginator :rows="products.per_page" :totalRecords="products.total"
                    :first="(products.current_page - 1) * products.per_page" @page="onPageChange" :pageLinkSize="2"
                    template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink" :pt="{
                        root: { class: 'justify-center !bg-transparent !flex-nowrap !gap-1' },
                        pages: { class: '!flex-nowrap' },
                        pageButton: ({ context }) => ({
                            class: [
                                '!min-w-[2rem] !h-8 !p-0 !text-xs !rounded-lg transition-all',
                                context.active ? '!bg-green-700 !text-white shadow-md' : '!bg-white border border-gray-100'
                            ]
                        }),
                        action: ({ context }) => ({
                            class: '!min-w-[2rem] !h-8 !p-0 !rounded-lg border border-transparent hover:border-gray-200'
                        })
                    }" />
            </div>

        </div>
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
</style>
