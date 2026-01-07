<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import OrderItems from "@shopen/pages/Admin/Order/components/OrderItems.vue";
import StatusHistory from "@shopen/pages/Admin/Order/components/StatusHistory.vue";
import Shipping from "@shopen/pages/Admin/Order/components/Shipping.vue";
import OrderSummary from "@shopen/pages/Admin/Order/components/OrderSummary.vue";
import Panel from "../../../components/admin/ui/Panel.vue";
import {Link} from "@inertiajs/vue3";
import Payment from "./components/Payment.vue";
import PromoCodes from "./components/PromoCodes.vue";
import ActionsPanel from "../../../components/admin/ui/ActionsPanel.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import OrderDetails from "./components/OrderDetails.vue";
import CustomerDetails from "./components/CustomerDetails.vue";
import OrderInvoices from "./components/OrderInvoices.vue";
import Button from "@shopen/components/admin/ui/Button.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    order: {type: Object, required: true},
    orderStatusOptions: {type: Array},
    paymentStatusOptions: {type: Array},
})
</script>
<template>
    <ActionsPanel back-route="admin.orders.index">
        <template #title>
            <PageTitle>Zamówienie #{{ order.order_number }}</PageTitle>
        </template>
        <Link :href="route('admin.orders.returns.create', props.order.id)">
            <Button>Zwrot</Button>
        </Link>
        <Link :href="route('admin.orders.invoices.create', props.order.id)">
            <Button>Faktura</Button>
        </Link>
    </ActionsPanel>
    <div class="px-6 py-8">
        <div class="flex flex-col sm:flex-row gap-4">
            <Panel width="w-1/2">
                <template #header>
                    Szczegóły
                </template>
                <OrderDetails :order="order"/>
            </Panel>

            <Panel width="w-1/2">
                <template #header>
                    Dane zamawiającego
                </template>
                <CustomerDetails :order="order"/>
            </Panel>
        </div>

        <Panel>
            <template #header>
                Produkty
            </template>
            <OrderItems :items="order.items"/>
        </Panel>

        <div class="flex flex-col items-start sm:flex-row gap-4">
            <div class="w-1/2">
                <Panel>
                    <template #header>
                        Status
                    </template>
                    <StatusHistory :statusItems="order.status_history" :orderStatuses="orderStatusOptions" :order="order"/>
                </Panel>
                <Panel>
                    <template #header>
                        Faktury
                    </template>
                    <OrderInvoices :invoices="order.invoices"/>
                </Panel>
            </div>
            <div class="w-1/2">
                <div class="flex gap-4">
                    <div class="w-full">
                        <Panel>
                            <template #header>
                                Wysyłka
                            </template>
                            <Shipping :order="order"/>
                        </Panel>
                    </div>
                    <div class="w-full">
                        <Panel>
                            <template #header>
                                Bony podarunkowe
                            </template>
                            <PromoCodes :order="order"/>
                        </Panel>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-1/2">
                        <Panel>
                            <template #header>
                                Płatność
                            </template>
                            <Payment :order="order" :paymentStatuses="paymentStatusOptions"/>
                        </Panel>
                    </div>
                    <div class="w-1/2">
                        <Panel>
                            <template #header>
                                Podsumowanie
                            </template>
                            <OrderSummary :order="order"/>
                        </Panel>
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>