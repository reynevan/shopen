<script setup>
import {Link, Head} from "@inertiajs/vue3";
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import IconMoney from "../../../components/icons/IconMoney.vue";
import IconReceipt from "../../../components/icons/IconReceipt.vue";
import ActionButton from "../../../components/admin/ui/ActionButton.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    latestOrders: {type: Array},
    pending_reviews_count: {type: Number},
    total_sale_amount: {type: String},
    orders_amount: {type: Number},
    new_messages: {type: Number}
})

</script>

<template>
    <Head title="Dashboard"/>
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
        <div class="flex gap-2">

            <section class="w-1/2">
                <h2 class="text-2xl mb-4">Ostatnie zamówienia</h2>
                <table v-if="latestOrders?.length" class="w-full table-primary">
                    <thead>
                    <tr>
                        <th class="">Klient</th>
                        <th class="w-30">Ilość produktów</th>
                        <th class="w-28 text-right">Kwota</th>
                        <th class="w-20">Akcje</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="order in latestOrders" :key="order.id">
                        <td class="text-left">
                            {{ order.shipping_address.first_name }} {{ order.shipping_address.last_name }}
                        </td>
                        <td class="text-right">
                            {{ order.items_count }}
                        </td>
                        <td class="text-right">
                            {{ order.total_amount }}
                        </td>
                        <td class="text-right">
                            <Link :href="route('admin.orders.show', order.id)">
                                <ActionButton type="view"/>
                            </Link>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-else class="text-gray-500 text-lg">
                    Brak zamówień
                </div>
            </section>

            <section class="w-1/2">
                <h2 class="text-2xl mb-4">Oczekujące działania</h2>
                <div class="space-y-2">
                    <div v-if="pending_reviews_count > 0">
                        <Link :href="route('admin.products.reviews.index', {status: 'pending'})"
                              class="flex items-center justify-between border rounded pl-2 hover:bg-accent/50 transition-all duration-300">
                            <div class="w-full flex items-center gap-2">
                                <div class="text-xs px-2 py-1 bg-secondary text-white rounded">
                                    {{ pending_reviews_count }}
                                </div>
                                <div class="py-2">Opinie czekające na zatwierdzenie</div>
                            </div>

                            <div class="flex items-center justify-center h-full text-xl border-l px-4">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        </Link>
                    </div>
                    <div v-if="new_messages > 0">
                        <Link :href="route('admin.contact-messages.index')"
                              class="flex items-center justify-between border rounded pl-2 hover:bg-accent/50 transition-all duration-300">
                            <div class="w-full flex items-center gap-2">
                                <div class="text-xs px-2 py-1 bg-secondary text-white rounded">
                                    {{ new_messages }}
                                </div>
                                <div class="py-2">Nowe wiadomości</div>
                            </div>

                            <div class="flex items-center justify-center h-full text-xl border-l px-4">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>