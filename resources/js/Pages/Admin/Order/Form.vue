<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { formatCurrencyIndo } from '@/Utils/formatter';
import { useConfirm } from "primevue/useconfirm";

const confirm = useConfirm();

const props = defineProps({
    order: Object,
    users: Array,
    products: Array,
    financialAccounts: Array,
    isEdit: Boolean,
    title: String,
});


const form = useForm({
    user_id: props.order?.user_id || null,
    financial_account_id: props.order?.financial_account_id || null,
    shipping_address: props.order?.shipping_address || '',
    shipping_cost: props.order?.shipping_cost || 0,
    status: props.order?.status || 'pending',
    payment_status: props.order?.payment_status || 'unpaid',
    items: props.order?.order_items.map(item => ({
        product_id: item.product_id,
        name: item.product?.name,
        quantity: item.quantity,
        cost_price: item.cost_price,
        selling_price: item.selling_price
    })) || []
});

const selectedProduct = ref(null);
const quantityToAdd = ref(1);

const addItem = () => {
    if (!selectedProduct.value) {
        return;
    }

    const existingItem = form.items.find(item => item.product_id === selectedProduct.value.id);
    if (existingItem) {
        existingItem.quantity += quantityToAdd.value;
    } else {
        form.items.push({
            product_id: selectedProduct.value.id,
            name: selectedProduct.value.name,
            quantity: quantityToAdd.value,
            cost_price: selectedProduct.value.cost_price,
            selling_price: selectedProduct.value.selling_price
        });
    }

    selectedProduct.value = null;
    quantityToAdd.value = 1;
};

const removeItem = (index) => {
    confirm.require({
        message: 'Are you sure you want to delete this item?',
        header: 'Confirm Delete',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger',
        accept: () => {
            form.items.splice(index, 1);
        }
    });
};

const subtotal = computed(() => {
    return form.items.reduce((acc, item) => acc + (item.selling_price * item.quantity), 0);
});

const grandTotal = computed(() => {
    return subtotal.value + Number(form.shipping_cost);
});

const submit = () => {
    if (form.payment_status === 'paid' && form.status === 'completed') {
        confirm.require({
            message: 'Are you sure you want to Status completed and payment paid this order?',
            header: 'Confirm completed and paid',
            icon: 'pi pi-exclamation-triangle',
            rejectLabel: 'Cancel',
            acceptLabel: 'Complete',
            rejectClass: 'p-button-secondary ',
            acceptClass: 'p-button-success',
            accept: () => {
                if (props.isEdit) {
                    form.put(route('admin.orders.update', props.order.id), {
                        onSuccess: () => {
                            setTimeout(() => {
                                page.props.flash.success = null;
                            }, 500);
                        },
                    });
                } else {
                    form.post(route('admin.orders.store'), {
                        onSuccess: () => {
                            setTimeout(() => {
                                page.props.flash.success = null;
                            }, 500);
                        },
                    });
                }
            }
        });
    } else {
        if (props.isEdit) {
            form.put(route('admin.orders.update', props.order.id), {
                onSuccess: () => {
                    setTimeout(() => {
                        page.props.flash.success = null;
                    }, 500);
                },
            });
        } else {
            form.post(route('admin.orders.store'), {
                onSuccess: () => {
                    setTimeout(() => {
                        page.props.flash.success = null;
                    }, 500);
                },
            });
        }
    }


};

const statusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'Processing', value: 'processing' },
    { label: 'Completed', value: 'completed' },
    { label: 'Cancelled', value: 'cancelled' }
];

const paymentOptions = [
    { label: 'Unpaid', value: 'unpaid' },
    { label: 'Paid', value: 'paid' },
    { label: 'Failed', value: 'failed' }
];
</script>

<template>

    <Head :title="title" />

    <AdminLayout menuActive="orders" :title="title">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Details & Status -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                <!-- Order Items Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="pi pi-shopping-bag text-emerald-500"></i>
                        Order Items
                    </h3>

                    <!-- Add Product Form -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Select Product</label>
                            <Select v-model="selectedProduct" :options="products" filter optionLabel="name"
                                placeholder="Search product..." class="w-full" />
                        </div>
                        <div class="w-full sm:w-auto">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Qty</label>
                            <InputNumber v-model="quantityToAdd" showButtons buttonLayout="horizontal" :min="1"
                                class="!w-32 !rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700"
                                incrementButtonClass="!bg-white dark:!bg-gray-800 !text-gray-500 !border-none"
                                decrementButtonClass="!bg-white dark:!bg-gray-800 !text-gray-500 !border-none"
                                inputClass="!w-12 !text-center !font-bold !border-none !bg-transparent"
                                incrementButtonIcon="pi pi-plus" decrementButtonIcon="pi pi-minus" />
                        </div>
                        <div class="flex items-end">
                            <Button icon="pi pi-plus" label="Add" @click="addItem" severity="secondary"
                                class="w-full sm:w-auto" />
                        </div>
                    </div>

                    <DataTable :value="form.items" class="p-datatable-sm mb-6" stripedRows>
                        <Column field="name" header="Product"></Column>
                        <Column field="selling_price" header="Price">
                            <template #body="slotProps">
                                {{ formatCurrencyIndo(slotProps.data.selling_price) }}
                            </template>
                        </Column>
                        <Column field="quantity" header="Qty">
                            <template #body="slotProps">
                                <InputNumber v-model="slotProps.data.quantity" showButtons buttonLayout="horizontal"
                                    :min="1"
                                    class="!w-32 !rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700"
                                    incrementButtonClass="!bg-green-100 dark:!bg-gray-800 !text-gray-500 !border-none"
                                    decrementButtonClass="!bg-red-100 dark:!bg-gray-800 !text-gray-500 !border-none"
                                    inputClass="!w-12 !text-center !font-bold !border-none !py-1 !px-0"
                                    incrementButtonIcon="pi pi-plus" decrementButtonIcon="pi pi-minus" />
                            </template>
                        </Column>
                        <Column header="Total">
                            <template #body="slotProps">
                                <span class="font-bold">{{ formatCurrencyIndo(slotProps.data.selling_price *
                                    slotProps.data.quantity) }}</span>
                            </template>
                        </Column>
                        <Column header="Actions" class="text-right">
                            <template #body="slotProps">
                                <Button icon="pi pi-trash" severity="danger" text rounded
                                    @click="removeItem(slotProps.index)" />
                            </template>
                        </Column>
                    </DataTable>

                    <Message v-if="form.errors.items" severity="error" variant="simple">{{ form.errors.items }}
                    </Message>
                </div>

                <!-- Shipping Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="pi pi-map-marker text-emerald-500"></i>
                        Shipping Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="address" class="text-sm font-bold text-gray-700 dark:text-gray-300">Shipping
                                Address</label>
                            <Textarea id="address" v-model="form.shipping_address" rows="3" class="w-full mt-2"
                                placeholder="Full address details..." />
                            <Message v-if="form.errors.shipping_address" severity="error" variant="simple">{{
                                form.errors.shipping_address }}</Message>
                        </div>
                        <div>
                            <label for="shipping_cost"
                                class="text-sm font-bold text-gray-700 dark:text-gray-300">Shipping Cost</label>
                            <InputNumber id="shipping_cost" v-model="form.shipping_cost" mode="currency" currency="IDR"
                                locale="id-ID" :maxFractionDigits="0" class="w-full mt-2" />
                            <Message v-if="form.errors.shipping_cost" severity="error" variant="simple">{{
                                form.errors.shipping_cost }}
                            </Message>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary & Actions -->
            <div class="flex flex-col gap-6">
                <!-- Customer & Payment Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white">Customer & Payment</h3>
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Customer</label>
                            <Select v-model="form.user_id" :options="users" filter optionLabel="name" optionValue="id"
                                placeholder="Select Customer" class="w-full mt-2" />
                            <Message v-if="form.errors.user_id" severity="error" variant="simple">{{ form.errors.user_id
                            }}
                            </Message>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Payment Method</label>
                            <Select v-model="form.financial_account_id" :options="financialAccounts" optionValue="id"
                                placeholder="Select Account" class="w-full mt-2">
                                <template #value="slotProps">
                                    <div v-if="slotProps.value" class="flex items-center">
                                        {{financialAccounts.find(a => a.id === slotProps.value)?.name}} -
                                        {{financialAccounts.find(a => a.id === slotProps.value)?.account_number}}
                                    </div>
                                    <span v-else>
                                        {{ slotProps.placeholder }}
                                    </span>
                                </template>
                                <template #option="slotProps">
                                    <div class="flex items-center">
                                        {{ slotProps.option.name }} - {{ slotProps.option.account_number }}
                                    </div>
                                </template>
                            </Select>
                            <Message v-if="form.errors.financial_account_id" severity="error" variant="simple">{{
                                form.errors.financial_account_id }}</Message>
                        </div>
                    </div>
                </div>

                <!-- Status Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white">Order Status</h3>
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Process Status</label>
                            <Select v-model="form.status" :options="statusOptions" optionLabel="label"
                                optionValue="value" class="w-full mt-2" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Payment Status</label>
                            <Select v-model="form.payment_status" :options="paymentOptions" optionLabel="label"
                                optionValue="value" class="w-full mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Total Summary Card -->
                <div class="card p-6 bg-emerald-600 text-white rounded-2xl border border-emerald-500 shadow-xl">
                    <h3 class="text-lg font-bold mb-4 opacity-80 uppercase tracking-wider text-xs">Summary</h3>
                    <div class="flex justify-between mb-2">
                        <span>Subtotal</span>
                        <span class="text-xl font-bold">{{ formatCurrencyIndo(subtotal) }}</span>
                    </div>
                    <div class="flex justify-between mb-4 pb-4 border-b border-emerald-400/30">
                        <span>Shipping</span>
                        <span class="text-xl font-bold">{{ formatCurrencyIndo(form.shipping_cost) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold uppercase text-xs opacity-90">Grand Total</span>
                        <span class="text-2xl font-black">{{ formatCurrencyIndo(grandTotal) }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <Button :label="isEdit ? 'Update Order' : 'Place Order'"
                        :icon="isEdit ? 'pi pi-save' : 'pi pi-check'" severity="primary" size="large" @click="submit"
                        :loading="form.processing" class="shadow-lg shadow-emerald-500/20" />
                    <Link :href="route('admin.orders.index')" class="w-full">
                        <Button label="Cancel" severity="secondary" variant="text" class="w-full" />
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
