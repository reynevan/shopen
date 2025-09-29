<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ReviewsTable from "./components/ReviewsTable.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    reviews: {type: Object, required: true},
})
</script>
<template>
    <ActionsPanel>
        <template #title>
            <PageTitle>Opinie</PageTitle>
        </template>
    </ActionsPanel>
    <ReviewsTable :reviews="reviews"/>
</template>