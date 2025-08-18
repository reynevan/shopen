<script setup>
import { Link } from "@inertiajs/vue3";
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import IconMoney from "../../../components/icons/IconMoney.vue";
import IconReceipt from "../../../components/icons/IconReceipt.vue";
import ActionButton from "../../../components/admin/ui/ActionButton.vue";

defineOptions({layout: AdminLayout})

defineProps({
    total_sale_amount: {type: String},
    latest_orders: {type: Array},
    orders_amount: {type: Number},
})

</script>

<template>
    <div class="py-12">
        <section class="flex flex-col sm:flex-row gap-8 mb-10">

            <div class="rounded shadow pl-2 pr-6 py-4 flex items-center gap-4">
                <div class="bg-accent/50 rounded p-2 text-accent-strong">
                    <IconMoney size="3xl"/>
                </div>
                <div>
                    <div class="text-3xl mb-2"> {{ total_sale_amount }}</div>
                    <h2 class="text-neutral-500 uppercase text-xs tracking-wider">Łączna sprzedaż</h2>
                </div>
            </div>

            <div class="rounded shadow pl-2 pr-6 py-4 flex items-center gap-4">
                <div class="bg-accent/50 rounded p-2 text-accent-strong">
                    <IconReceipt size="3xl"/>
                </div>
                <div>
                    <div class="text-3xl mb-2"> {{ orders_amount }}</div>
                    <h2 class="text-neutral-500 uppercase text-xs tracking-wider">Zamówienia</h2>
                </div>
            </div>
        </section>
        <section class="w-1/2">
            <h2 class="text-2xl mb-4">Ostatnie zamówienia</h2>
            <table class="w-full">
                <thead class="bg-secondary text-white ">
                <tr>
                    <th class="text-sm font-semibold text-left py-2 px-2">Klient</th>
                    <th class="text-sm font-semibold text-right py-2 px-2 w-30">Ilość produktów</th>
                    <th class="text-sm font-semibold text-right py-2 px-2 w-28">Kwota</th>
                    <th class="text-sm font-semibold text-right py-2 px-2 w-20">Szczegóły</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="order in latest_orders" :key="order.id" class="odd:bg-accent/50">
                    <td class="text-left px-2 py-2 border-r border-light">
                        {{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}
                    </td>
                    <td class="text-right px-2 py-2 border-r border-light">
                        {{ order.items_count }}
                    </td>
                    <td class="text-right px-2 py-2 border-r border-light">
                        {{ order.total_amount }}
                    </td>
                    <td class="text-right px-2 py-2">
                        <Link :href="route('admin.orders.show', order.id)">
                            <ActionButton type="view"/>
                        </Link>
                    </td>
                </tr>
                </tbody>
            </table>

        </section>
    </div>
</template>