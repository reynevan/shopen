<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import {router, usePage, Link} from "@inertiajs/vue3";
import ActionButton from "../../../../components/admin/ui/ActionButton.vue";
import ActionButtons from "../../../../components/admin/ui/ActionButtons.vue";

defineProps(['orders'])

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;
const status = page.props.status;

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.orders.index'), {
        sort: field,
        dir: dir
    }, {})
}

const onSearch = () => {
    router.get(route('admin.orders.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    }, {})
}

const filter = () => {

}
</script>

<template>
    <div>

        <DataTable
            table-class="w-full"
            head-class="bg-neutral-700 text-neutral-200 py-2"
            td-class="py-2"
            top="top-0"
            @onSort="onSort"
            :default-sort="[sort, dir]"
            :data="orders.data"
            paginated
            :meta="orders.meta"
        >
            <TableColumn field="id" label="ID" sortable v-slot="data" width="90px">
                {{ data.row.order_number }}
            </TableColumn>

            <TableColumn field="created_at" label="Data złożenia" sortable v-slot="data">
                {{ data.row.created_at }}
            </TableColumn>

            <TableColumn field="status" label="Status" v-slot="data" width="150px">
                {{ data.row.status_label }}
            </TableColumn>

            <TableColumn field="shipping_address" label="Dane do wysyłki" v-slot="data">
                {{ data.row.shipping_address?.first_name }} {{ data.row.shipping_address?.last_name }}
            </TableColumn>

            <TableColumn field="shipping_address" label="Dane do faktury" v-slot="data">
                {{ data.row.billing_address?.first_name }} {{ data.row.billing_address?.last_name }}
            </TableColumn>

            <TableColumn field="-" label="Akcje" v-slot="data" width="50px">
                <Link :href="route('admin.orders.show', data.row.id)">
                    <ActionButton type="view"/>
                </Link>
            </TableColumn>
        </DataTable>
    </div>
</template>

<style scoped>

</style>