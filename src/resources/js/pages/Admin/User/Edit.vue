<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import {Link} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";
import Button from "@shopen/components/admin/ui/Button.vue";
import UsersTable from "./components/UsersTable.vue";
import UserForm from "./components/UserForm.vue";

defineOptions({layout: AdminLayout})

defineProps({
    user: {type: Object},
    orders: {type: Array}
});
</script>
<template>
    <main>
        <PageTitle>{{ user.first_name }} {{ user.last_name}}</PageTitle>

        <UserForm :user="user" :orders="orders" />
    </main>
</template>