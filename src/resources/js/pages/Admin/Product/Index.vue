<script setup>
import {Link} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ProductsTable from "@shopen/components/admin/product/ProductsTable.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    products: {type: Object, required: true},
})
</script>
<template>
    <PageTitle>Produkty</PageTitle>
    <ActionsPanel>
        <Link :href="route('admin.products.create')">
            <Button>Nowy produkt</Button>
        </Link>
    </ActionsPanel>
    <ProductsTable :products="products"/>
</template>