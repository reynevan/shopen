<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import PromoCodesTable from "@shopen/pages/Admin/PromoCode/components/PromoCodesTable.vue";
import {Link, Head} from "@inertiajs/vue3";
import ActionsPanel from "../../../components/admin/ui/ActionsPanel.vue";
import PageTitle from "../../../components/admin/ui/PageTitle.vue";
import Button from "@shopen/components/admin/ui/Button.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    promoCodes: {type: Object, required: true},
})
</script>
<template>
    <Head title="Kody promocyjne"/>
    <ActionsPanel>
        <template #title>
            <PageTitle>Kody rabatowe</PageTitle>
        </template>
        <Link :href="route('admin.promo-codes.create')">
            <Button>Nowy kod</Button>
        </Link>
    </ActionsPanel>

    <PromoCodesTable :promoCodes="promoCodes"/>
</template>