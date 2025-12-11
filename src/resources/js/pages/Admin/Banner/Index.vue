<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import {Link, Head} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";
import ActionsPanel from "../../../components/admin/ui/ActionsPanel.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import BannersTable from "./components/BannersTable.vue";

defineOptions({layout: AdminLayout})

defineProps({
    banners: Array,
    placements: Array
});
</script>
<template>
    <Head title="Bannery"/>
    <ActionsPanel>
        <template #title>
            <PageTitle>Bannery</PageTitle>
        </template>
        <Link :href="route('admin.banners.create')" class="mr-4">
            <Button>Nowy banner</Button>
        </Link>
    </ActionsPanel>
    <BannersTable :banners="banners" :placements="placements"/>
</template>