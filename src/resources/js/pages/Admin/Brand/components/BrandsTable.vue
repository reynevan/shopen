<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import {router, usePage, Link} from "@inertiajs/vue3";
import Input from "@shopen/components/admin/form/input/Input.vue";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";

defineProps(['brands'])

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.brands.index'), {
        sort: field,
        dir: dir,
        q: search.value
    })
}

const onSearch = () => {
    router.get(route('admin.brands.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    })
}
const removeBrand = (brand) => {
    if (!confirm(`Na pewno chcesz usunąć markę ${brand.name}?`)) {
        return;
    }
    router.delete(route('admin.brands.delete', brand.id), {
        preserveScroll: true,
    })

}
</script>

<template>

    <form @submit.prevent="onSearch" class="my-4 pr-4">
        <div class="flex justify-end">
            <Input v-model="search" id="search" class="mr-2 max-w-md"/>
            <button type="submit">Szukaj</button>
        </div>
    </form>

    <DataTable
        table-class="w-full"
        head-class="bg-neutral-700 text-neutral-200 py-2"
        td-class="py-2"
        @onSort="onSort"
        :default-sort="[sort, dir]"
        :data="brands.data"
        paginated
        :meta="brands.meta"
    >
        <TableColumn field="name" label="Nazwa" sortable v-slot="data">
            {{ data.row.name }}
        </TableColumn>

        <TableColumn field="is_active" label="Status" sortable v-slot="data">
            <div v-if="data.row.is_active" class="px-2 py-1 text-xs bg-green-100 text-green-700 inline-flex items-center gap-2 rounded">
                <i class="bi bi-check-lg"></i> Aktywna
            </div>
            <div v-else class="px-2 py-1 text-xs bg-neutral-100 text-neutral-700 inline-flex items-center gap-2 rounded">
                <i class="bi bi-x-lg"></i> Nieaktywna
            </div>
        </TableColumn>

        <TableColumn field="is_active" label="Na stronie głównej" sortable v-slot="data">
            <div v-if="data.row.show_on_homepage" class="px-2 py-1 text-xs bg-green-100 text-green-700 inline-flex items-center gap-2 rounded">
                <i class="bi bi-check-lg"></i> TAK
            </div>
            <div v-else class="px-2 py-1 text-xs bg-neutral-100 text-neutral-700 inline-flex items-center gap-2 rounded">
                <i class="bi bi-x-lg"></i> NIE
            </div>
        </TableColumn>

        <TableColumn field="logo" label="Logo" v-slot="data">
            <img v-if="data.row.logo_url" :src="data.row.logo_url">
        </TableColumn>

        <TableColumn field="-" label="Akcje" v-slot="data" width="75px">
            <div class="flex gap-1">
                <Link :href="route('admin.brands.edit', data.row.id)" class="text-accent cursor-pointer">
                    <ActionButton type="edit"/>
                </Link>
                <ActionButton @click="removeBrand(data.row)" type="remove"/>
            </div>
        </TableColumn>

    </DataTable>
</template>