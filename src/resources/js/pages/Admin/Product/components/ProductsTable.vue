<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import {Link, router, usePage} from "@inertiajs/vue3";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";

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
        dir: dir,
        q: search.value
    })
}

const onSearch = (query) => {
    if (query === search.value || (!query && !search.value)) {
        return;
    }
    search.value = query;

    router.get(route('admin.products.index'), {
        sort: sort,
        dir: dir,
        q: search.value
    },)
}

const onPaginate = (page) => {
    router.get(route('admin.products.index'), {
        page: page,
        sort: sort,
        dir: dir,
        q: search.value
    },)
}

const removeProduct = (product) => {
    if (!confirm(`Na pewno chcesz usunąć produkt ${product.sku}?`)) {
        return;
    }
    router.delete(route('admin.products.delete', product.id), {
        preserveScroll: true,
    })
}

</script>

<template>

    <DataTable
        table-class="w-full"
        td-class="py-2"
        @onSort="onSort"
        :default-sort="[sort, dir]"
        :data="products.data"
        paginated
        basic-paginated
        @onPaginate="onPaginate"
        :meta="products.meta"
        searchable
        @onSearch="onSearch"
        :query="search"
    >
        <TableColumn field="id" label="ID" sortable v-slot="data" width="75px">
            {{ data.row.id }}
        </TableColumn>

        <TableColumn label="Zdjęcie" v-slot="data" width="70px">
            <div class="min-h-[50px]">
                <Link :href="route('admin.products.edit', data.row.id)" prefetch>
                <img :src="data.row.image"
                     width="50px"
                     class="border"
                     v-if="data.row.image">
                </Link>
            </div>
        </TableColumn>

        <TableColumn field="name" label="Nazwa" sortable v-slot="data">
            <Link :href="route('admin.products.edit', data.row.id)" class="hover:text-black cursor-pointer" prefetch>
                {{ data.row.attributes.name }}
            </Link>
        </TableColumn>

        <TableColumn field="sku" label="SKU" v-slot="data">
            {{ data.row.sku }}
        </TableColumn>

        <TableColumn field="is_active" label="Status" sortable v-slot="data">
            <span v-if="data.row.attributes.is_active">Aktywny</span>
            <span v-else>Nieaktywny</span>
        </TableColumn>

        <TableColumn field="type" label="Typ" sortable v-slot="data">
            <span v-if="data.row.is_configurable">Konfigurowalny</span>
            <span v-else>Prosty</span>
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

        <TableColumn label="Akcje" v-slot="data" width="100px">
            <div class="flex divide-x divide-light">
                <Link :href="route('admin.products.edit', data.row.id)" class="text-accent cursor-pointer" prefetch>
                    <ActionButton type="edit"/>
                </Link>
                <ActionButton @click="removeProduct(data.row)" type="remove"/>
            </div>
        </TableColumn>

    </DataTable>

</template>