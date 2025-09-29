<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import TaxClassForm from "@shopen/pages/Admin/TaxClass/components/TaxClassForm.vue";

defineOptions({layout: AdminLayout})

</script>
<template>
    <TaxClassForm :taxClass="{}"/>
</template>