<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import AttributeForm from "./components/AttributeForm.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

const props = defineProps({
    attribute: {type: Object, required: true},
})
</script>
<template>
    <Head :title="attribute.name + ' - Edycja atrybutu'" />
    <main>
        <PageTitle>{{ attribute.name }}</PageTitle>

        <AttributeForm :attribute="attribute" />
    </main>
</template>