<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";

const props = defineProps({
    taxClasses: Object
})

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.tax-classes.index'), {
        sort: field,
        dir: dir
    })
}

const onSearch = () => {
    router.get(route('admin.tax-classes.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    })
}

const removeTaxClass = (taxClass) => {
    if (!confirm('Na pewno chcesz usunąć klasę podatku ' + taxClass.name + '?')) {
        return;
    }
    router.delete(route('admin.tax-classes.delete', taxClass.id), {
        preserveScroll: true,
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
        :data="taxClasses.data"
        paginated
        :meta="taxClasses.meta"
    >
        <TableColumn field="id" label="ID" sortable v-slot="data" width="75px">
            {{ data.row.id }}
        </TableColumn>

        <TableColumn field="code" label="Kod" v-slot="data" sortable>
            {{ data.row.code }}
        </TableColumn>

        <TableColumn field="name" label="Nazwa" v-slot="data" sortable>
            <div>{{ data.row.name }}</div>
            <div v-if="data.row.description" class="text-neutral-400 text-sm">
                {{ data.row.description }}
            </div>
        </TableColumn>

        <TableColumn field="rate" label="Stawka VAT" v-slot="data" sortable>
            {{ data.row.rate }}%
        </TableColumn>


        <TableColumn label="-" v-slot="data" width="100px">
            <div class="flex gap-1">
                <Link :href="route('admin.tax-classes.edit', data.row.id)" class="text-accent cursor-pointer">
                    <ActionButton type="edit"/>
                </Link>
                <ActionButton @click="removeTaxClass(data.row)" type="remove"/>
            </div>
        </TableColumn>

    </DataTable>
</template>