<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ProductForm from "@shopen/pages/Admin/Product/components/ProductForm.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    product: {type: Object},
    categories: {type: Array, required: true},
    attributes: {type: Array, required: true},
})
</script>
<template>
    <main>
        <h1 class="text-4xl py-4">Nowy produkt</h1>
        <ProductForm :product="product" :categories="categories" :attributes="attributes"/>
    </main>
</template>