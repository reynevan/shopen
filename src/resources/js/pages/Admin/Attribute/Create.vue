<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import AttributeForm from "./components/AttributeForm.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

</script>
<template>
    <Head title="Nowy atrybut"/>
    <main>
        <PageTitle>Nowy atrybut</PageTitle>

        <AttributeForm :attribute="{}" />
    </main>
</template>