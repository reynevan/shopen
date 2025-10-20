<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import OrdersTable from "@shopen/pages/Admin/Order/components/OrdersTable.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import {Head} from "@inertiajs/vue3";
import ActionsPanel from "../../../components/admin/ui/ActionsPanel.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    orders: {type: Object, required: true},
})
</script>
<template>
    <Head title="Zamówienia"/>
    <ActionsPanel>
        <template #title>
            <PageTitle>Zamówienia</PageTitle>
        </template>
    </ActionsPanel>
    <OrdersTable :orders="orders"/>
</template>