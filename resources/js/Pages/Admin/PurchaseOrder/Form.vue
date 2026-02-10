<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useToast } from 'primevue/usetoast';
import { formatCurrencyIndo } from '@/Utils/formatter';

const props = defineProps({
    purchaseOrder: Object,
    suppliers: Array,
    products: Array,
    financialAccounts: Array,
    isEdit: Boolean,
    title: String,
});

const toast = useToast();

const form = useForm({
    supplier_id: props.purchaseOrder?.supplier_id || null,
    financial_account_id: props.purchaseOrder?.financial_account_id || null,
    shipping_cost: props.purchaseOrder?.shipping_cost || 0,
    notes: props.purchaseOrder?.notes || '',
    status: props.purchaseOrder?.status || 'draft',
    payment_status: props.purchaseOrder?.payment_status || 'unpaid',
    items: props.purchaseOrder?.purchase_order_items.map(item => ({
        product_id: item.product_id,
        name: item.product?.name,
        quantity: item.quantity,
        cost_price: item.cost_price,
    })) || []
});

const selectedProduct = ref(null);
const quantityToAdd = ref(1);

const addItem = () => {
    if (!selectedProduct.value) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Please select a product', life: 3000 });
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
        });
    }

    selectedProduct.value = null;
    quantityToAdd.value = 1;
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const subtotal = computed(() => {
    return form.items.reduce((acc, item) => acc + (item.cost_price * item.quantity), 0);
});

const grandTotal = computed(() => {
    return subtotal.value + Number(form.shipping_cost);
});

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.purchase-orders.update', props.purchaseOrder.id), {
            onSuccess: () => toast.add({ severity: 'success', summary: 'Success', detail: 'Purchase Order updated', life: 3000 }),
        });
    } else {
        form.post(route('admin.purchase-orders.store'), {
            onSuccess: () => toast.add({ severity: 'success', summary: 'Success', detail: 'Purchase Order created', life: 3000 }),
        });
    }
};

const statusOptions = [
    { label: 'Draft', value: 'draft' },
    { label: 'Ordered', value: 'ordered' },
    { label: 'Received', value: 'received' },
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

    <AdminLayout menuActive="purchase-orders" :title="title">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: PO Items & Details -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                <!-- Items Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="pi pi-box text-blue-500"></i>
                        PO Items
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
                        <Column field="cost_price" header="Cost Price">
                            <template #body="slotProps">
                                <InputNumber v-model="slotProps.data.cost_price" mode="currency" currency="IDR"
                                    locale="id-ID" :maxFractionDigits="0" class="w-full" size="small" />
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
                                <span class="font-bold">{{ formatCurrencyIndo(slotProps.data.cost_price *
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

                <!-- Notes Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="pi pi-pencil text-blue-500"></i>
                        Extra Notes
                    </h3>
                    <Textarea v-model="form.notes" rows="4" class="w-full"
                        placeholder="Internal notes or special instructions for this PO..." />
                </div>
            </div>

            <!-- Right Column: Summary & Supplier -->
            <div class="flex flex-col gap-6">
                <!-- Supplier & Payment Card -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white">Supplier & Payment</h3>
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Supplier</label>
                            <Select v-model="form.supplier_id" :options="suppliers" filter optionLabel="name"
                                optionValue="id" placeholder="Select Supplier" class="w-full mt-2" />
                            <Message v-if="form.errors.supplier_id" severity="error" variant="simple">{{
                                form.errors.supplier_id
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
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white">PO Status</h3>
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Process Status</label>
                            <Select v-model="form.status" :options="statusOptions" optionLabel="label"
                                optionValue="value" class="w-full mt-2" />
                            <Message v-if="form.errors.status" severity="error" variant="simple">{{ form.errors.status
                                }}</Message>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">Payment Status</label>
                            <Select v-model="form.payment_status" :options="paymentOptions" optionLabel="label"
                                optionValue="value" class="w-full mt-2" />
                            <Message v-if="form.errors.payment_status" severity="error" variant="simple">{{
                                form.errors.payment_status }}</Message>
                        </div>
                    </div>
                </div>

                <!-- Shipping Cost -->
                <div
                    class="card p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors duration-300">
                    <label for="shipping_cost" class="text-xs font-bold text-gray-400 uppercase">Shipping Cost</label>
                    <InputNumber id="shipping_cost" v-model="form.shipping_cost" mode="currency" currency="IDR"
                        locale="id-ID" :maxFractionDigits="0" class="w-full mt-2" />
                    <Message v-if="form.errors.shipping_cost" severity="error" variant="simple">{{
                        form.errors.shipping_cost }}
                    </Message>
                </div>

                <!-- Total Summary Card -->
                <div class="card p-6 bg-blue-600 text-white rounded-2xl border border-blue-500 shadow-xl">
                    <h3 class="text-lg font-bold mb-4 opacity-80 uppercase tracking-wider text-xs">PO Summary</h3>
                    <div class="flex justify-between mb-2">
                        <span>Subtotal</span>
                        <span class="font-medium">{{ formatCurrencyIndo(subtotal) }}</span>
                    </div>
                    <div class="flex justify-between mb-4 pb-4 border-b border-blue-400/30">
                        <span>Shipping</span>
                        <span class="font-medium">{{ formatCurrencyIndo(form.shipping_cost) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold uppercase text-xs opacity-90">Grand Total</span>
                        <span class="text-2xl font-black">{{ formatCurrencyIndo(grandTotal) }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3">
                    <Button :label="isEdit ? 'Update PO' : 'Create PO'" :icon="isEdit ? 'pi pi-save' : 'pi pi-check'"
                        severity="primary" size="large" @click="submit" :loading="form.processing"
                        class="shadow-lg shadow-blue-500/20" />
                    <Link :href="route('admin.purchase-orders.index')" class="w-full">
                        <Button label="Cancel" severity="secondary" variant="text" class="w-full" />
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
