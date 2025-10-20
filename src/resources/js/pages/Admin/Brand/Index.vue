<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import {Link, Head} from "@inertiajs/vue3";
import BrandsTable from "./components/BrandsTable.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import Button from "@shopen/components/admin/ui/Button.vue";

defineOptions({layout: AdminLayout})

defineProps({
    brands: Array
});
</script>
<template>
    <Head title="Marki"/>
    <ActionsPanel>
        <template #title>
            <PageTitle>Marki</PageTitle>
        </template>
        <Link :href="route('admin.brands.create')">
            <Button>Nowa marka</Button>
        </Link>
    </ActionsPanel>
    <BrandsTable :brands="brands" />
</template>