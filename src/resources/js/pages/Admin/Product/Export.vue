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
    <section>
        <table class="w-full">
            <thead>
            <tr>
                <th class="text-left px-2">Nazwa pliku</th>
                <th class="text-right">Data</th>
                <th class="text-right">Rozmiar</th>
                <th class="text-center">Pobierz</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="file in files">
                <td class="px-2 py-2">{{ file.name }}</td>
                <td class="px-2 py-2 text-right">{{ file.created_at }}</td>
                <td class="px-2 py-2 text-right">{{ file.size }}</td>
                <td class="px-2 py-2 text-center">
                    <a :href="file.download_url" target="_blank">
                        <i class="bi bi-download"></i>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </section>
</template>