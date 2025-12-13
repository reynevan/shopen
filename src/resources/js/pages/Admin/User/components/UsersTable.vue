<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import {router, usePage, Link} from "@inertiajs/vue3";
import Input from "@shopen/components/admin/form/input/Input.vue";
import ActionButtons from "../../../../components/admin/ui/ActionButtons.vue";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";

defineProps(['users'])

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.users.index'), {
        sort: field,
        dir: dir,
        q: search.value
    }, {

    })
}

const onSearch = () => {
    router.get(route('admin.users.index'), {
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
        :data="users.data"
        paginated
        :meta="users.meta"
    >
        <TableColumn field="id" label="ID" sortable v-slot="data" width="75px">
            {{ data.row.id }}
        </TableColumn>

        <TableColumn field="email" label="Email" sortable v-slot="data">
            {{ data.row.email }}
        </TableColumn>

        <TableColumn field="first_name" label="Imię" sortable v-slot="data">
            {{ data.row.first_name }}
        </TableColumn>

        <TableColumn field="last_name" label="Nazwisko" sortable v-slot="data">
            {{ data.row.last_name }}
        </TableColumn>

        <TableColumn field="-" label="Akcje" v-slot="data" width="75px">
            <ActionButtons>
                <ActionButton type="view" :href="route('admin.users.edit', data.row.id)"/>
            </ActionButtons>
        </TableColumn>
    </DataTable>
</template>