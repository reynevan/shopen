<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PromoCodesTable from "@shopen/pages/Admin/PromoCode/components/PromoCodesTable.vue";
import FormHeader from "../../../components/admin/form/FormHeader.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    promoCodes: {type: Object, required: true},
})
</script>
<template>

    <FormHeader>
        <a :href="route('admin.promo-codes.create')" >Dodaj</a>
    </FormHeader>
    <PromoCodesTable :promoCodes="promoCodes"/>
</template>