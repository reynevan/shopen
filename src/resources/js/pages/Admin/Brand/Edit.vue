<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import BrandForm from "./components/BrandForm.vue";

defineOptions({layout: AdminLayout})

defineProps({
    brand: {type: Object}
})
</script>
<template>
    <main>
        <PageTitle>{{ brand.name }}</PageTitle>

        <BrandForm :brand="brand"/>
    </main>
</template>