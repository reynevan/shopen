<script setup>
import {Head, router} from "@inertiajs/vue3";
import Button from "@shopen/components/admin/ui/Button.vue";
import AdminLayout from "@shopen/layouts/admin/AdminLayout.vue";
import ActionsPanel from "@shopen/components/admin/ui/ActionsPanel.vue";
import PageTitle from "@shopen/components/admin/ui/PageTitle.vue";

defineOptions({layout: AdminLayout})

const props = defineProps({
    files: {type: Array, default: () => []},
})
const exportProducts = () => {
    router.post(route('admin.products.export.submit'))
}
</script>
<template>
    <Head title="Eksport produktów"/>
    <ActionsPanel back-route="admin.products.index">
        <template #title>
            <PageTitle>Produkty - Eksport</PageTitle>
        </template>
        <Button @click="exportProducts">Eksportuj</Button>
    </ActionsPanel>
    <section class="px-4">
        <table class="">
            <thead>
            <tr>
                <th class="text-left px-6">Nazwa pliku</th>
                <th class="text-right px-6">Data</th>
                <th class="text-right px-6">Rozmiar</th>
                <th class="text-center px-6">Pobierz</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="file in files" class="border">
                <td class="px-6 py-2">{{ file.name }}</td>
                <td class="px-6 py-2 text-right">{{ file.created_at }}</td>
                <td class="px-6 py-2 text-right">{{ file.size }}</td>
                <td class="px-6 py-2 text-center">
                    <a :href="file.download_url" target="_blank">
                        <i class="bi bi-download"></i>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </section>
</template>