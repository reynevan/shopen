<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import OrderItems from "@shopen/pages/Admin/Order/components/OrderItems.vue";
import StatusHistory from "@shopen/pages/Admin/Order/components/StatusHistory.vue";
import Shipping from "@shopen/pages/Admin/Order/components/Shipping.vue";
import OrderSummary from "@shopen/pages/Admin/Order/components/OrderSummary.vue";
import Panel from "../../../components/admin/ui/Panel.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    order: {type: Object, required: true},
    orderStatusOptions: {type: Array}
})
</script>
<template>
    <div class="px-6 py-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <Panel width="w-1/2">
                <template #header>
                    Szczegóły
                </template>

                <div class="flex justify-between items-center mb-1 py-1 border-b">
                    <div>Data złożenia</div>
                    <div>{{ order.created_at }}</div>
                </div>
                <div class="flex justify-between mb-1 py-1 border-b">
                    <div>Status</div>
                    <div>{{ order.status_label }}</div>
                </div>
                <div class="flex justify-between mb-1 py-1 border-b">
                    <div>Metoda wysyłki</div>
                    <div>{{ order.shipping_method_label }}</div>
                </div>
                <div class="flex justify-between mb-1 py-1 border-b">
                    <div>Metoda płatności</div>
                    <div>{{ order.payment_method_label }}</div>
                </div>

            </Panel>

            <Panel width="w-1/2">
                <template #header>
                    Dane zamawiającego
                </template>

                <div class="mb-4 px-4 flex flex-wrap items-start">
                    <div class="w-full sm:w-1/2">
                        <div class="font-semibold text-lg mb-2">Adres wysyłki</div>
                        <div>
                            <div class="">{{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}</div>
                            <div class="">{{ order.shipping_address.postal_code }} {{ order.shipping_address.city }}</div>
                            <div class="">{{ order.shipping_address.address_line }}</div>
                            <div class="">tel.: {{ order.shipping_address.phone }}</div>
                            <div v-if="order.shipping_address.email" class="">{{ order.shipping_address.email }}</div>
                        </div>
                    </div>
                    <div class="w-full sm:w-1/2">
                        <div class="font-semibold text-lg mb-2">Dane do płatności</div>
                        <div>
                            <div class="">{{ order.billing_address.first_name }} {{ order.billing_address.last_name }}</div>
                            <div class="">{{ order.billing_address.postal_code }} {{ order.billing_address.city }}</div>
                            <div class="">{{ order.billing_address.address_line }}</div>
                            <div class="">tel.: {{ order.billing_address.phone }}</div>
                            <div v-if="order.billing_address.email" class="">{{ order.billing_address.email }}</div>
                        </div>
                    </div>
                </div>
            </Panel>
        </div>

        <Panel>
            <template #header>
                Produkty
            </template>
            <OrderItems :items="order.items"/>
        </Panel>

        <div class="flex flex-col items-start sm:flex-row gap-4">

            <Panel width="w-1/2">
                <template #header>
                    Status
                </template>
                <StatusHistory :statusItems="order.status_history" :orderStatuses="orderStatusOptions" :order="order"/>
            </Panel>

            <div class="w-1/2">
                <Panel>
                    <template #header>
                        Wysyłka
                    </template>
                    <Shipping :order="order"/>
                </Panel>
                <Panel>
                    <template #header>
                        Podsumowanie
                    </template>
                    <OrderSummary :order="order"/>
                </Panel>
            </div>

        </div>
    </div>
</template>