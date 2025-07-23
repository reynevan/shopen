<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ProductForm from "../../../components/admin/product/ProductForm.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    product: {type: Object, required: true},
    categories: {type: Array, required: true},
    attributes: {type: Array, required: true},
})
</script>
<template>
    <ProductForm :product="product" :categories="categories" :attributes="attributes"/>
</template>