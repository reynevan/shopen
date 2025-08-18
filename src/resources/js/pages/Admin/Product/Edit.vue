<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ProductForm from "@shopen/pages/Admin/Product/components/ProductForm.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    product: {type: Object, required: true},
    categories: {type: Array, required: true},
    attributes: {type: Array, required: true},
})
</script>
<template>
    <main>
        <PageTitle>{{ product.attributes.name }}</PageTitle>
        <ProductForm :product="product" :categories="categories" :attributes="attributes"/>
    </main>
</template>