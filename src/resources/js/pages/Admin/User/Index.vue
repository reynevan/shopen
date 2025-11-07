<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import {Link, Head} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import UsersTable from "./components/UsersTable.vue";

defineOptions({layout: AdminLayout})

defineProps({
    users: Array
});
</script>
<template>
    <ActionsPanel>
        <template #title>
            <PageTitle>Klienci</PageTitle>
        </template>
        <Link :href="route('admin.attributes.create')">
            <Button>Nowy klient</Button>
        </Link>
    </ActionsPanel>
    <UsersTable :users="users"/>
</template>