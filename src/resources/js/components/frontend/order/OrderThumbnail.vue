<script setup>
import OrderStatus from "./OrderStatus.vue";
import OrderThumbnailItems from "./OrderThumbnailItems.vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps(['order']);

</script>

<template>
    <Link :href="route('user.orders.show', order.uuid)">
        <div
            class="order-thumbnail cursor-pointer group flex flex-col sm:flex-row sm:items-stretch justify-between">
            <div class="flex-shrink-0 sm:pr-4 mb-4 sm:mb-0">
                <OrderStatus :status="order.status" :label="order.status_label"/>
                <p class="text-sm text-neutral-600 mt-2 mb-1">
                    {{ order.placed_date }}
                </p>
                <h3 class="text-sm text-neutral-400">
                    Numer #{{ order.order_number }}
                </h3>
            </div>

            <div class="flex-grow py-4 sm:py-0 sm:px-6 sm:border-l sm:border-r">
                <OrderThumbnailItems :items="order.items" :limit="2"/>
            </div>

            <div
                class="flex-shrink-0 flex sm:flex-col justify-between items-center sm:items-end sm:pl-4 pt-4 sm:pt-0 border-t sm:border-none">
                <div class="text-left sm:text-right">
                    <span class="text-sm text-gray-500">Suma</span>
                    <p class="text-lg font-bold text-gray-900">
                        {{ order.total_amount }}
                    </p>
                </div>
            </div>
        </div>
    </Link>
</template>