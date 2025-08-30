<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import AttributeForm from "./components/AttributeForm.vue";

defineOptions({layout: AdminLayout})

</script>
<template>
    <main>
        <PageTitle>Nowy atrybut</PageTitle>

        <AttributeForm :attribute="{}" />
    </main>
</template>