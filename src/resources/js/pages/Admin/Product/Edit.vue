<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ProductForm from "@shopen/pages/Admin/Product/components/ProductForm/ProductForm.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

const props = defineProps({
    product: {type: Object, required: true},
    categories: {type: Array, required: true},
    ceneo_categories: {type: Array},
    attributes: {type: Array, required: true},
    variants: {type: Array, required: true},
    brands: {type: Array, required: true},
    tax_classes: {type: Array}
})
</script>
<template>
    <Head :title="product.attributes.name + ' - Edycja produktu'"/>
    <main>
        <ProductForm :product="product"
                     :categories="categories"
                     :ceneo-categories="ceneo_categories"
                     :attributes="attributes"
                     :brands="brands"
                     :taxClasses="tax_classes"
                     :variants="variants" />
    </main>
</template>