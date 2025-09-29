<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import TaxClassForm from "@shopen/pages/Admin/TaxClass/components/TaxClassForm.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    taxClass: {type: Object, required: true}
})
</script>
<template>
    <TaxClassForm :taxClass="taxClass"/>
</template>