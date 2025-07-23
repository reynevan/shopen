<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import CategoryForm from "@shopen/pages/Admin/Category/components/CategoryForm.vue";

defineOptions({layout: AdminLayout})

defineProps({
    categories: Object,
    attributes: Object,
    category: Object
});
</script>
<template>
    <CategoryForm :categories="categories" :category="category" :attributes="attributes"/>
</template>