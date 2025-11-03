<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import { router, usePage} from "@inertiajs/vue3";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import ActionButtons from "@shopen/components/admin/ui/ActionButtons.vue";

const props = defineProps({
    messages: Object
})

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;


const search = ref(q);


const onSort = (field, dir) => {
    router.get(route('admin.contact-messages.index'), {
        sort: field,
        dir: dir
    })
}

const onSearch = () => {
    router.get(route('admin.contact-messages.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    })
}

const removeMessage = (message) => {
    if (!confirm('Na pewno chcesz tą wiadomość?')) {
        return;
    }
    router.delete(route('admin.contact-messages.delete', message.id), {
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
        :data="messages.data"
        paginated
        :meta="messages.meta"
    >
        <TableColumn field="id" label="ID" sortable v-slot="data" width="75px">
            {{ data.row.id }}
        </TableColumn>

        <TableColumn field="status" label="Status" v-slot="data" sortable>
            {{ data.row.status_label }}
        </TableColumn>

        <TableColumn field="created_at" label="Data" v-slot="data" sortable>
            <span class="whitespace-nowrap">{{ data.row.created_at_diff }}</span>
        </TableColumn>

        <TableColumn field="name" label="Imię" v-slot="data" sortable>
            {{ data.row.name }}
        </TableColumn>

        <TableColumn field="email" label="Email" v-slot="data" sortable>
            <div>{{ data.row.email }}</div>
        </TableColumn>

        <TableColumn field="subject" label="Temat" v-slot="data">
            {{ data.row.subject }}
        </TableColumn>

        <TableColumn field="message" label="Wiadomość" v-slot="data">
            {{ data.row.message }}
        </TableColumn>


        <TableColumn label="Akcje" v-slot="data" width="100px">
            <ActionButtons>
                <ActionButton :href="route('admin.contact-messages.show', data.row.id)" type="view"/>
                <ActionButton @click="removeMessage(data.row)" type="remove"/>
            </ActionButtons>
        </TableColumn>

    </DataTable>
</template>