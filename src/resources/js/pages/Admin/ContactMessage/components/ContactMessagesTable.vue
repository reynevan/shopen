<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import { router, usePage} from "@inertiajs/vue3";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";
import ActionButtons from "@shopen/components/admin/ui/ActionButtons.vue";

const props = defineProps({
    messages: Object
})

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const status = page.props.status;

const onSort = (field, dir) => {
    router.get(route('admin.contact-messages.index'), {
        sort: field,
        dir: dir
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

const filter = (_status = null) => {
    const params = {
        sort: sort,
        dir: dir
    }
    if (_status) {
        params.status = _status;
    }
    router.get(route('admin.contact-messages.index'), params, {})
}

</script>

<template>
    <div class="flex flex-col lg:flex-row gap-10 justify-between items-end mb-4">

        <div class="flex items-center my-2 divide-x divide-gray-border-light">
            <div class="text-sm px-4 py-2 cursor-pointer hover:bg-accent/50 transition-all duration-300"
                 :class="!status ? 'bg-accent': ''" @click="filter()">Wszystkie</div>

            <div class="text-sm px-4 py-2 cursor-pointer hover:bg-blue-200 hover:text-blue-800 transition-all duration-300"
                 :class="status === 'new' ? 'bg-blue-100 text-blue-800': ''" @click="filter('new')">
                <i class="bi  bi-envelope mr-2"></i> Nowe
            </div>

            <div class="text-sm px-4 py-2 cursor-pointer hover:bg-gray-200 hover:text-gray-800 transition-all duration-300"
                 :class="status === 'read' ? 'bg-gray-100 text-gray-800': ''" @click="filter('read')">
                <i class="bi  bi-envelope-open mr-2"></i> Odczytane
            </div>

            <div class="text-sm px-4 py-2 cursor-pointer hover:bg-green-200 hover:text-green-800 transition-all duration-300"
                 :class="status === 'replied' ? 'bg-green-100 text-green-800': ''" @click="filter('replied')">
                <i class="bi bi-reply mr-2"></i> Z odpowiedzią
            </div>
        </div>
    </div>
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