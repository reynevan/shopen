<script setup>

import DataTable from "@shopen/components/admin/table/DataTable.vue";
import TableColumn from "@shopen/components/admin/table/TableColumn.vue";
import {ref} from "vue";
import { router, usePage} from "@inertiajs/vue3";
import ActionButtons from "@shopen/components/admin/ui/ActionButtons.vue";
import ActionButton from "@shopen/components/admin/ui/ActionButton.vue";

const props = defineProps({
    banners: Object,
    placements: Array
})

const page = usePage();

const sort = page.props.sort;
const dir = page.props.dir;
const q = page.props.q;

const search = ref(q);

const onSort = (field, dir) => {
    let data = {
        sort: sort,
        dir: dir,
    };
    if (page.props.placement) {
        data.placement = page.props.placement;
    }
    if (search.value) {
        data.q = search.value;
    }
    router.get(route('admin.banners.index'), data, {})
}

const onSearch = () => {
    let data = {
        sort: sort,
        dir: dir,
        q: search.value
    };
    if (page.props.placement) {
        data.placement = page.props.placement;
    }
    router.get(route('admin.banners.index'), data, {})
}

const removeBanner = (banner) => {
    if (!confirm(`Na pewno chcesz usunąc banner "${banner.title}"?`)) {
        return;
    }
    router.delete(route('admin.banners.delete', banner.id), {
        preserveScroll: true
    })
}
const filterPlacement = (placement) => {
    let data = {
        sort: sort,
        dir: dir,
        placement: placement,
    };
    if (search.value) {
        data.q = search.value;
    }
    router.get(route('admin.banners.index'), data, {})
}
</script>

<template>
    <div class="flex divide-x divide-gray-border-light">
        <div class="px-2 py-2 text-sm hover:bg-accent/50 transition-all duration-300"
             :class="!page.props.placement ? 'bg-accent' : 'cursor-pointer'"
             @click="filterPlacement(null)">
            Wszystkie
        </div>
        <div v-for="placement in placements"
             class="px-2 py-2 text-sm hover:bg-accent/50 transition-all duration-300"
             :class="page.props.placement === placement.value ? 'bg-accent' : 'cursor-pointer'"
             @click="filterPlacement(placement.value)">
            {{ placement.label }}
        </div>
    </div>
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
            {{ data.row.placement_key_label }}
        </TableColumn>

        <TableColumn field="click_count" label="Kliknięcia" v-slot="data" sortable>
            {{ data.row.click_count }}
        </TableColumn>

        <TableColumn label="Akcje" v-slot="data" width="100px">
            <ActionButtons>
                <ActionButton type="edit" :href="route('admin.banners.edit', data.row.id)"/>
                <ActionButton type="remove" @click="removeBanner(data.row)"/>
            </ActionButtons>
        </TableColumn>

    </DataTable>
</template>

<style scoped>

</style>