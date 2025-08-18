<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import BannersTable from "../../../components/admin/banner/BannersTable.vue";
import {Link} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

defineProps({
    banners: Array
});
</script>
<template>
        <Link :href="route('admin.banners.create')" class="mr-4">
            Nowy
        </Link>
    <BannersTable :banners="banners"/>
</template>