<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import BrandForm from "./components/BrandForm.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

</script>
<template>
    <Head title="Nowa marka"/>
    <main>
        <PageTitle>Nowa marka</PageTitle>

        <BrandForm :brand="{}"/>
    </main>
</template>