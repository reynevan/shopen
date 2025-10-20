<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import BannerForm from "./components/BannerForm.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

defineProps({
    placementTypes: Object,
    placements: Object,
    categories: Array,
});
</script>
<template>
    <Head title="Nowy banner"/>
    <BannerForm
        :placement-types="placementTypes"
        :placements="placements"
        :categories="categories"
    />
</template>