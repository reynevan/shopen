<script setup>

import DataTable from "../table/DataTable.vue";
import TableColumn from "../table/TableColumn.vue";
import {ref} from "vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import Input from "../form/input/Input.vue";

const props = defineProps({
    products: Object
})

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;

const search = ref(q);

const onSort = (field, dir) => {
    router.get(route('admin.products.index'), {
        sort: field,
        dir: dir
    }, {})
}

const onSearch = () => {
    router.get(route('admin.products.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    }, {})
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
        td-class="py-2"
        @onSort="onSort"
        :default-sort="[sort, dir]"
        :data="products.data"
        paginated
        :meta="products.meta"
    >
        <TableColumn field="id" label="ID" sortable v-slot="data" width="75px">
            {{ data.row.id }}
        </TableColumn>

        <TableColumn label="Zdjęcie" v-slot="data" width="70px">
            <div class="min-h-[50px]">
                <img :src="data.row.image"
                     width="50px"
                     class="border"
                     v-if="data.row.image">
            </div>
        </TableColumn>

        <TableColumn field="name" label="Nazwa" sortable v-slot="data">
            {{ data.row.attributes.name }}
        </TableColumn>

        <TableColumn field="sku" label="SKU" v-slot="data">
            {{ data.row.sku }}
        </TableColumn>

        <TableColumn field="is_active" label="Status" sortable v-slot="data">
            <span v-if="data.row.attributes.is_active">Aktywny</span>
            <span v-else>Nieaktywny</span>
        </TableColumn>

        <TableColumn field="stock_qty" label="Ilość" sortable v-slot="data">
            {{ data.row.stock_qty }}
        </TableColumn>

        <TableColumn field="price" label="Bazowa Cena" sortable v-slot="data" width="135px">
            {{ data.row.price ? data.row.price.price_formatted : '' }}
        </TableColumn>

        <TableColumn field="final_price" label="Cena" sortable v-slot="data" width="135px">
            {{ data.row.price ? data.row.price.final_price_formatted : '' }}
        </TableColumn>

        <TableColumn label="-" v-slot="data" width="100px">
            <Link :href="route('admin.products.edit', data.row.id)" class="text-link cursor-pointer">Edytuj</Link>
        </TableColumn>

    </DataTable>

</template>