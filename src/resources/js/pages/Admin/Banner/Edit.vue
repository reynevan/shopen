<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ProductForm from "../../../components/admin/product/ProductForm.vue";
import BannerForm from "../../../components/admin/banner/BannerForm.vue";

defineOptions({layout: AdminLayout})

defineProps({
    banner: Object,
    placementTypes: Object,
    placements: Object,
    categories: Array,
});
</script>
<template>
    <BannerForm
        :banner="banner"
        :placement-types="placementTypes"
        :placements="placements"
        :categories="categories"
    />
</template>