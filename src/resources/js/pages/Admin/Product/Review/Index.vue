<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ReviewsTable from "./components/ReviewsTable.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    reviews: {type: Object, required: true},
})
</script>
<template>
    <ReviewsTable :reviews="reviews"/>
</template>