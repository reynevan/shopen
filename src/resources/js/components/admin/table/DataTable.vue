<script setup>

import {onMounted, provide, ref} from "vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    data: {
        type: Array
    },
    tableClass: {
        type: String
    },
    headClass: {
        type: String
    },
    tdClass: {
        type: String
    },
    defaultSort: {
        type: Array
    },
    paginated: {
        type: Boolean,
        default: false
    },
    meta: {
        type: Object
    }
});

const emits = defineEmits(['onSort'])

const columns = ref([]);

const sort = ref({});

const addColumn = (column) => {
    columns.value.push(column);

}

const onThClick = (column) => {
    let field = column.props.field;
    if (!column.props.sortable) {
        return;
    }
    if (!sort.value || !sort.value.field || sort.value.field !== field) {
        sort.value = {
            field: field,
            dir: 'asc'
        }
    } else {
        sort.value = {
            field: field,
            dir: sort.value.dir === 'asc' ? 'desc' : 'asc'
        }
    }
    emits('onSort', field, sort.value.dir);
}
onMounted(() => {
    if (props.defaultSort) {
        sort.value = {
            field: props.defaultSort[0],
            dir: props.defaultSort[1]
        };
    }
})
provide('table', {
    addColumn
})

</script>

<template>

    <slot/>

    <table :class="tableClass">
        <thead :class="headClass">
        <tr>
            <th v-for="(column, index) in columns"
                :key="column.field + ':' + index"
                @click="onThClick(column)"
                class="text-left border-r p-2"
                :width="column.props.width"
                :class="{'cursor-pointer': column.props.sortable, 'border-l': index === 0}">
                <span>{{ column.props.label }}</span>
                <span v-if="column.props.sortable && sort.field === column.props.field">
                        <i class="bi bi-arrow-down-short" v-if="sort.dir === 'desc'"></i>
                        <i class="bi bi-arrow-up-short" v-if="sort.dir === 'asc'"></i>
                    </span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="row in data" class="hover:bg-accent/5 transition-colors">
            <td v-for="(column, colIndex) in columns" :key="colIndex" class="p-2 border-r border-b" :class="{'border-l': colIndex === 0}">
                <component
                    :is="column.slots.default"
                    :row="row"
                />
            </td>
        </tr>
        </tbody>
    </table>

    <nav v-if="meta.links.length > 3" class="flex justify-center mt-8">
        <div class="flex space-x-1">
            <template v-for="(link, index) in meta.links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    prefetch
                    :class="[
                        'px-4 py-2 text-sm rounded-md border transition-colors',
                        link.active ? 'bg-blue-600 text-white border-blue-600'
                        : link.url ? 'text-gray-700 border-gray-300 hover:bg-gray-50'
                        : 'text-gray-400 border-gray-200 cursor-not-allowed'
                    ]"
                    preserve-state
                    v-html="link.label"
                />
            </template>
        </div>
    </nav>

</template>

<style scoped>

</style>