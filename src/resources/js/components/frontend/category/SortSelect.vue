<script setup>
import {computed, ref} from 'vue';
import {usePage} from "@inertiajs/vue3";
import Select from "../input/Select.vue";

const props = defineProps(['sortOptions'])
const page = usePage();
const selectedSort = ref(page.props.activeSort);

const emits = defineEmits(['onChange']);


const onChange = () => {
    emits('onChange', selectedSort.value)
}
</script>

<template>
    <div class="flex items-center w-full justify-end" data-ai="sort">
        <label for="sort-by" class="whitespace-nowrap mr-2 text-sm text-gray-700">
            Sortuj:
        </label>

        <Select id="sort-by"
                title="Sortuj"
                @onChange="onChange"
                v-model="selectedSort"
                :options="sortOptions"/>
    </div>
</template>