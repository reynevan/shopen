<script setup>
import OrderThumbnailItems from "@shopen/pages/Frontend/User/Order/components/OrderThumbnailItems.vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps(['order']);

</script>

<template>
    <Link :href="route('user.orders.show', order.uuid)" prefetch>
        <div class="order-thumbnail cursor-pointer group flex flex-col sm:flex-row sm:items-stretch justify-between">
            <div class="flex-shrink-0 sm:pr-4 mb-4 sm:mb-0">
                <div class="order-status">{{ order.status_label }}</div>
                <h3 class="order-number">
                    #{{ order.order_number }}
                </h3>
                <p class="text-sm text-neutral-600 mt-2">
                    {{ order.placed_date }}
                </p>
            </div>

            <div class="flex-grow py-4 sm:py-0 sm:px-6 sm:border-l sm:border-r">
                <OrderThumbnailItems :items="order.items" :limit="2"/>
            </div>

            <div
                class="flex-shrink-0 flex sm:flex-col justify-between items-center sm:items-end sm:pl-4 pt-4 sm:pt-0 border-t sm:border-none">
                <div class="text-left sm:text-right">
                    <span class="text-sm">Suma</span>
                    <p class="text-lg">
                        {{ order.total_amount }}
                    </p>
                </div>
            </div>
        </div>
    </Link>
</template>