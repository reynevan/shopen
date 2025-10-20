<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ProductForm from "@shopen/pages/Admin/Product/components/ProductForm/ProductForm.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

const props = defineProps({
    product: {type: Object},
    parent: {type: Object},
    categories: {type: Array, required: true},
    ceneo_categories: {type: Array},
    attributes: {type: Array, required: true},
    brands: {type: Array, required: true},
    tax_classes: {type: Array}
})
</script>
<template>
    <Head title="Nowy produkt"/>
    <main>
        <ProductForm :product="product"
                     :parent="parent"
                     :categories="categories"
                     :ceneo-categories="ceneo_categories"
                     :attributes="attributes"
                     :taxClasses="tax_classes"
                     :brands="brands" />
    </main>
</template>