<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import {Link} from "@inertiajs/vue3";
import AttributesTable from "./components/AttributesTable.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import Button from "@shopen/components/admin/ui/Button.vue";

defineOptions({layout: AdminLayout})

defineProps({
    attributes: Array
});
</script>
<template>
    <PageTitle>Atrybuty</PageTitle>
    <ActionsPanel>
        <Link :href="route('admin.attributes.create')">
            <Button>Nowy atrybut</Button>
        </Link>
    </ActionsPanel>
    <AttributesTable :attributes="attributes"/>
</template>