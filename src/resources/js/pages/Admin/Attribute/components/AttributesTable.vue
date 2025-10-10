<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import {router, usePage, Link} from "@inertiajs/vue3";
import Input from "@shopen/components/admin/form/input/Input.vue";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";

defineProps(['attributes'])

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.attributes.index'), {
        sort: field,
        dir: dir,
        q: search.value
    }, {

    })
}

const onSearch = () => {
    router.get(route('admin.attributes.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    }, {

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
        :data="attributes.data"
        paginated
        :meta="attributes.meta"
    >
        <TableColumn field="name" label="Nazwa" sortable v-slot="data">
            {{ data.row.name }}
        </TableColumn>

        <TableColumn field="code" label="Kod" sortable v-slot="data">
            {{ data.row.code }}
        </TableColumn>

        <TableColumn field="entity_type" label="Typ" sortable v-slot="data">
            <span v-if="data.row.entity_type === 'product'">Produkt</span>
            <span v-if="data.row.entity_type === 'category'">Kategoria</span>
        </TableColumn>

        <TableColumn field="is_system" label="System" sortable v-slot="data" width="75px">
            <i v-if="data.row.is_system" class="bi bi-check-lg"></i>
            <i v-else class="bi bi-x-lg"></i>
        </TableColumn>

        <TableColumn field="is_filterable" label="Filtrowalny" sortable v-slot="data" width="75px">
            <i v-if="data.row.is_filterable" class="bi bi-check-lg"></i>
            <i v-else class="bi bi-x-lg"></i>
        </TableColumn>

        <TableColumn field="is_searchable" label="Wyszukiwalny" sortable v-slot="data" width="75px">
            <i v-if="data.row.is_searchable" class="bi bi-check-lg"></i>
            <i v-else class="bi bi-x-lg"></i>

        </TableColumn>

        <TableColumn field="is_visible_in_details" label="Pokaż w szczegółach" sortable v-slot="data" width="75px">
            <i v-if="data.row.is_visible_in_details" class="bi bi-check-lg"></i>
            <i v-else class="bi bi-x-lg"></i>

        </TableColumn>

        <TableColumn field="-" label="Akcje" v-slot="data" width="75px">
            <Link :href="route('admin.attributes.edit', data.row.id)">
                <ActionButton type="edit"/>
            </Link>
        </TableColumn>


    </DataTable>
</template>