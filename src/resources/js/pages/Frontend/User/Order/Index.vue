<script setup>

import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import OrderThumbnail from "@shopen/pages/Frontend/User/Order/components/OrderThumbnail.vue";
import Pagination from "@shopen/components/frontend/ui/Pagination.vue";
import Heading from "@shopen/pages/Frontend/User/components/Heading.vue";
import IconReceipt from "@shopen/components/icons/IconReceipt.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: UserPanelLayout})
defineProps(['orders'])
</script>

<template>
    <Head title="Moje zamówienia"/>
    <Heading title="Zamówienia"/>
    <div>
        <div v-if="!orders.data.length" class="flex items-center justify-between gap-6 mb-6">
            <div class="w-full sm:w-1/2">
                <h2 class="text-xl">Nie masz jeszcze żadnych zamówień</h2>

                <p>Gdy złożysz zamówienie, zobaczysz je tutaj.</p>
            </div>
            <div class="hidden text-gray-200 w-1/2 sm:flex items-center justify-center">
                <IconReceipt size="7xl"/>
            </div>

        </div>

        <div class="mb-4" v-for="order in orders.data" :key="order.id">
            <OrderThumbnail :order="order"/>
        </div>

        <Pagination :meta="orders.meta" :links="orders.meta.links" :only="['orders']"/>
    </div>
</template>
