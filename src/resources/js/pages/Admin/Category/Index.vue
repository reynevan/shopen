<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import CategoryForm from "@shopen/pages/Admin/Category/components/CategoryForm.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

defineProps({
    categories: Object,
    attributes: Object,
    category: Object
});
</script>
<template>
    <Head>
        <title v-if="category">
            {{ category.id ? category.attributes.name : 'Nowa kategoria'}}
        </title>
        <title v-else>
            Kategorie
        </title>
    </Head>
    <CategoryForm :categories="categories" :category="category" :attributes="attributes"/>
</template>