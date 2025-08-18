<script setup>
import {computed, ref} from 'vue';
import {usePage} from "@inertiajs/vue3";

const props = defineProps(['sortOptions'])
const page = usePage();
const selectedSort = ref(page.props.activeSort);

const emits = defineEmits(['onChange']);


const onChange = () => {
    emits('onChange', selectedSort.value)
}
</script>

<template>
    <div class="flex items-center" data-ai="sort">
        <label for="sort-by" class="whitespace-nowrap mr-2 text-sm font-medium text-gray-700">
            Sortuj:
        </label>
        <select
            id="sort-by"
            @change="onChange"
            v-model="selectedSort"
            class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 sm:text-sm"
        >
            <option
                v-for="option in sortOptions"
                :key="option.key"
                :value="option.key"
            >
                {{ option.label }}
            </option>
        </select>
    </div>
</template>