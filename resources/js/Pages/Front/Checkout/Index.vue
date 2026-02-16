<script setup>
import FrontLayout from '@/Layouts/FrontLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { formatCurrencyIndo } from '@/Utils/formatter';
import { useConfirm } from "primevue/useconfirm";

const props = defineProps({
    menu: String,
    title: String,
    financialAccounts: Array,
});

const cartStore = useCartStore();
const confirm = useConfirm();

const form = useForm({
    username: '', // WhatsApp number
    name: '',
    financial_account_id: '',
    shipping_address: '',
    items: cartStore.items.map(item => ({
        id: item.id,
        quantity: item.quantity
    })),
});

const submitCheckout = () => {
    confirm.require({
        message: 'Pastikan data pesanan sudah benar. Lanjutkan pembayaran?',
        header: 'Konfirmasi Pesanan',
        icon: 'pi pi-check-circle',
        rejectProps: {
            label: 'Batal',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Ya, Buat Pesanan',
            severity: 'success',
        },
        accept: () => {
            form.post(route('checkout.store'), {
                onSuccess: () => {
                    cartStore.clearCart();
                },
                onError: (errors) => {
                    console.error(errors);
                }
            });
        }
    });
};
</script>

<template>

    <Head :title="props.title" />

    <FrontLayout v-model:menuActive="props.menu" v-model:title="props.title" :hideBottomNav="true">
        <div class="px-4 py-6 pb-32 min-h-[80vh]">

            <h1 class="text-xl font-black text-gray-900 dark:text-white mb-6 tracking-tight">Checkout Pesanan</h1>

            <div v-if="cartStore.items.length > 0">

                <!-- 1. Customer Info -->
                <section class="mb-6">
                    <h2 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="pi pi-user text-green-600"></i> Informasi Pemesan
                    </h2>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Nama Lengkap</label>
                            <InputText v-model="form.name" placeholder="Contoh: Ahmad Fulan"
                                class="w-full !text-sm !rounded-lg" :class="{ 'p-invalid': form.errors.name }" />
                            <small v-if="form.errors.name" class="text-red-500 text-[10px]">{{ form.errors.name
                            }}</small>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Nomor WhatsApp</label>
                            <InputText v-model="form.username" placeholder="Contoh: 08123456789"
                                class="w-full !text-sm !rounded-lg" :class="{ 'p-invalid': form.errors.username }" />
                            <small v-if="form.errors.username" class="text-red-500 text-[10px]">{{ form.errors.username
                            }}</small>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Alamat / Lokasi Pengiriman</label>
                            <Textarea v-model="form.shipping_address" rows="2"
                                placeholder="Contoh: Asrama Putra Lt. 2, Kamar B10" class="w-full !text-sm !rounded-lg"
                                :class="{ 'p-invalid': form.errors.shipping_address }" />
                            <small v-if="form.errors.shipping_address" class="text-red-500 text-[10px]">{{
                                form.errors.shipping_address }}</small>
                        </div>
                    </div>
                </section>

                <!-- 2. Items -->
                <section class="mb-6">
                    <h2 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="pi pi-box text-green-600"></i> Rincian Pesanan
                    </h2>
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
                        <div v-for="item in cartStore.items" :key="item.id" class="flex items-center gap-3 p-3">
                            <div class="w-12 h-12 rounded-lg bg-gray-50 flex-shrink-0 overflow-hidden">
                                <img v-if="item.image" :src="`/storage/${item.image}`"
                                    class="w-full h-full object-cover">
                                <img v-else src="/assets/images/placeholder.webp"
                                    class="w-full h-full object-cover opacity-50">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-gray-900 truncate">{{ item.name }}</h4>
                                <p class="text-[10px] text-gray-500">{{ item.category }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-900">{{ formatCurrencyIndo(item.price *
                                    item.quantity) }}</p>
                                <p class="text-[10px] text-gray-500">{{ item.quantity }} x {{
                                    formatCurrencyIndo(item.price) }}</p>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-600">Subtotal</span>
                            <span class="text-sm font-black text-gray-900">{{ formatCurrencyIndo(cartStore.totalPrice)
                            }}</span>
                        </div>
                    </div>
                </section>

                <!-- 3. Payment Method -->
                <section class="mb-6">
                    <h2 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="pi pi-wallet text-green-600"></i> Metode Pembayaran
                    </h2>
                    <div class="grid grid-cols-1 gap-3">
                        <div v-for="account in financialAccounts" :key="account.id"
                            @click="form.financial_account_id = account.id"
                            class="relative p-3 rounded-xl border-2 transition-all cursor-pointer flex items-center gap-3"
                            :class="form.financial_account_id === account.id ? 'border-green-500 bg-green-50' : 'border-gray-100 bg-white hover:border-gray-200'">

                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                :class="form.financial_account_id === account.id ? 'border-green-600' : 'border-gray-300'">
                                <div v-if="form.financial_account_id === account.id"
                                    class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                            </div>

                            <div>
                                <h4 class="text-sm font-bold text-gray-900">{{ account.name }}</h4>
                                <p class="text-xs text-gray-500">{{ account.account_number }}</p>
                            </div>
                        </div>
                    </div>
                    <small v-if="form.errors.financial_account_id" class="text-red-500 text-[10px] mt-1 block">{{
                        form.errors.financial_account_id }}</small>
                </section>

                <!-- Sticky Bottom Action -->
                <div
                    class="fixed bottom-[80px] left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-gray-100 p-4 z-40 pb-safe shadow-[0_-4px_20px_rgba(0,0,0,0.05)] rounded-2xl">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-gray-500">Total Tagihan</span>
                        <span class="text-lg font-black text-green-700">{{ formatCurrencyIndo(cartStore.totalPrice)
                        }}</span>
                    </div>

                    <Button @click="submitCheckout" :loading="form.processing" label="Buat Pesanan Sekarang"
                        icon="pi pi-check" iconPos="right"
                        class="!w-full !rounded-xl !bg-green-600 !border-none !font-bold !py-3.5 shadow-lg shadow-green-200 !mb-2" />
                </div>

            </div>

            <!-- Empty Cart State -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <i class="pi pi-shopping-cart text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Keranjang Kosong</h3>
                <p class="text-xs text-gray-500 max-w-[200px] mb-6">Anda belum memilih item apapun untuk di checkout.
                </p>
                <Link :href="route('product.index')" class="text-green-600 font-bold text-sm">Belanja Sekarang</Link>
            </div>

        </div>
    </FrontLayout>
</template>

<style scoped>
.pb-safe {
    padding-bottom: env(safe-area-inset-bottom, 20px);
}
</style>
