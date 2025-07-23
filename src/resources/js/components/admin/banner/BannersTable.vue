<script setup>

import DataTable from "../table/DataTable.vue";
import TableColumn from "../table/TableColumn.vue";
import {ref} from "vue";
import {Link, router, usePage} from "@inertiajs/vue3";

const props = defineProps({
    banners: Object
})

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.banners.index'), {
        sort: field,
        dir: dir
    }, {

    })
}

const onSearch = () => {
    router.get(route('admin.banners.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    }, {

    })
}
</script>

<template>
    <DataTable
        table-class="w-full"
        head-class="bg-neutral-700 text-neutral-200 py-2"
        td-class="py-2"
        @onSort="onSort"
        :default-sort="[sort, dir]"
        :data="banners.data"
        paginated
        :meta="banners.meta"
    >
        <TableColumn field="id" label="ID" sortable v-slot="data" width="75px">
            {{ data.row.id }}
        </TableColumn>

        <TableColumn field="is_active" label="Status" v-slot="data" width="30px" sortable>
            <span v-if="data.row.is_active">Aktywny</span>
            <span v-if="!data.row.is_active">Nieaktywny</span>
        </TableColumn>

        <TableColumn field="title" label="Tytuł" v-slot="data" sortable>
            {{ data.row.title }}
        </TableColumn>

        <TableColumn field="placement_key" label="Umiejscowienie" v-slot="data" sortable>
            {{ data.row.placement_key }}
        </TableColumn>

        <TableColumn field="placement_key" label="Kliknięcia" v-slot="data" sortable>
            {{ data.row.click_count }}
        </TableColumn>

        <TableColumn label="-" v-slot="data" width="100px">
            <Link :href="route('admin.banners.edit', data.row.id)" class="text-accent cursor-pointer">Edytuj</Link>
        </TableColumn>

    </DataTable>
</template>

<style scoped>

</style>