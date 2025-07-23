<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ProductsTable from "../../../components/admin/product/ProductsTable.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    products: {type: Object, required: true},
})
</script>
<template>
    <ProductsTable :products="products"/>
</template>