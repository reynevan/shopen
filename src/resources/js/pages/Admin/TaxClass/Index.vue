<script setup>
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import TaxClassesTable from "@shopen/pages/Admin/TaxClass/components/TaxClassesTable.vue";
import {Link} from "@inertiajs/vue3";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import Button from "@shopen/components/admin/ui/Button.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    taxClasses: {type: Object, required: true},
})
</script>
<template>
    <ActionsPanel>
        <template #title>Stawki VAT</template>
        <Link :href="route('admin.tax-classes.create')">
            <Button>Nowa stawka VAT</Button>
        </Link>
    </ActionsPanel>
    <TaxClassesTable :taxClasses="taxClasses"/>
</template>