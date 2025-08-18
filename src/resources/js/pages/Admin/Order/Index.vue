<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import OrdersTable from "@shopen/pages/Admin/Order/components/OrdersTable.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    orders: {type: Object, required: true},
})
</script>
<template>
    <OrdersTable :orders="orders"/>
</template>