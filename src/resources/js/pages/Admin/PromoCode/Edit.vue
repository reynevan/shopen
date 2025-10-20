<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PromoCodeForm from "@shopen/pages/Admin/PromoCode/components/PromoCodeForm.vue";
import {Head} from "@inertiajs/vue3";

defineOptions({layout: AdminLayout})

const props = defineProps({
    promoCode: {type: Object, required: true},
    categories: {type: Array, required: true},
    attributes: {type: Array, required: true},
})
</script>
<template>
    <Head :title="promoCode.name + ' - Edycja kodu promocyjnego'"/>
    <PromoCodeForm :promoCode="promoCode" :categories="categories" :attributes="attributes"/>
</template>