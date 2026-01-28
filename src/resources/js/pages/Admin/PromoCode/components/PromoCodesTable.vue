<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import ActionButtons from "../../../../components/admin/ui/ActionButtons.vue";

const props = defineProps({
    promoCodes: Object
})

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.promo-codes.index'), {
        sort: field,
        dir: dir
    }, {})
}

const onSearch = () => {
    router.get(route('admin.promo-codes.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    }, {})
}

const removeCode = (code) => {
    if (!confirm(`Na pewno chcesz usunąć kod ${code.name}?`)) {
        return;
    }
    router.delete(route('admin.promo-codes.delete', code.id), {
        preserveScroll: true
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
        :data="promoCodes.data"
        paginated
        :meta="promoCodes.meta"
    >
        <TableColumn field="id" label="ID" sortable v-slot="data" width="75px">
            {{ data.row.id }}
        </TableColumn>

        <TableColumn field="is_active" label="Status" v-slot="data" width="30px" sortable>
            <span v-if="data.row.is_active">Aktywny</span>
            <span v-if="!data.row.is_active">Nieaktywny</span>
        </TableColumn>

        <TableColumn field="name" label="Nazwa" v-slot="data" sortable>
            <Link :href="route('admin.promo-codes.edit', data.row.id)" class="cursor-pointer">
                {{ data.row.name }}
            </Link>
        </TableColumn>

        <TableColumn field="valid_from" label="Ważny od" v-slot="data" sortable>
            {{ data.row.valid_from_formatted ? data.row.valid_from_formatted : '-' }}
        </TableColumn>

        <TableColumn field="valid_to" label="Ważny do" v-slot="data" sortable>
            {{ data.row.valid_to_formatted ? data.row.valid_to_formatted : '-' }}
        </TableColumn>


        <TableColumn label="Akcje" v-slot="data" width="100px">
            <ActionButtons>
                <ActionButton type="edit" :href="route('admin.promo-codes.edit', data.row.id)"/>
                <ActionButton type="remove" @click="removeCode(data.row)"/>
            </ActionButtons>
        </TableColumn>

    </DataTable>
</template>