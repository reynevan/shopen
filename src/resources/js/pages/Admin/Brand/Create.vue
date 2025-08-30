<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import BrandForm from "./components/BrandForm.vue";

defineOptions({layout: AdminLayout})

</script>
<template>
    <main>
        <PageTitle>Nowa marka</PageTitle>

        <BrandForm :brand="{}"/>
    </main>
</template>