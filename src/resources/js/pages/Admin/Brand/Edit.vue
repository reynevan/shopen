<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import BrandForm from "./components/BrandForm.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

defineProps({
    brand: {type: Object}
})
</script>
<template>
    <Head :title="brand.name + ' - Edycja marki '"/>
    <main>
        <BrandForm :brand="brand"/>
    </main>
</template>