<script setup>

import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import OrderThumbnail from "../../../../components/frontend/order/OrderThumbnail.vue";
import Pagination from "../../../../components/frontend/ui/Pagination.vue";
import Heading from "../components/Heading.vue";

defineOptions({layout: UserPanelLayout})
defineProps(['orders'])
</script>

<template>
    <Heading title="Zamówienia"/>
    <div class="mb-4" v-for="order in orders.data" :key="order.id">
        <OrderThumbnail :order="order"/>
    </div>
    <Pagination :links="orders.meta.links" :only="['orders']"/>
</template>
