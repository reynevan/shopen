<script setup>
import { ref, watch, computed } from 'vue';
import ActionButton from "../../admin/ui/ActionButton.vue";

const props = defineProps({
    meta: {
        type: Object,
        required: true,
        validator: (value) => {
            const requiredKeys = ['current_page', 'last_page', 'total'];
            return requiredKeys.every(key => Object.prototype.hasOwnProperty.call(value, key));
        }
    },
    loading: { type: Boolean, default: false },
});

const emit = defineEmits(['onPaginate']);

const page = ref(props.meta.current_page);

watch(() => props.meta.current_page, (newPage) => {
    page.value = newPage;
});

const isFirstPage = computed(() => props.meta.current_page === 1);
const isLastPage = computed(() => props.meta.current_page === props.meta.last_page);

const previousPage = () => {
    if (!isFirstPage.value) {
        emit('onPaginate', props.meta.current_page - 1);
    }
};

const nextPage = () => {
    if (!isLastPage.value) {
        emit('onPaginate', props.meta.current_page + 1);
    }
};

const goToPage = () => {
    let targetPage = parseInt(page.value, 10);
    if (isNaN(targetPage) || targetPage < 1) {
        page.value = props.meta.current_page;
        return;
    }
    if (targetPage > props.meta.last_page) {
        targetPage = props.meta.last_page;
    }
    page.value = targetPage;
    if (targetPage !== props.meta.current_page) {
        emit('onPaginate', targetPage);
    }
};
</script>
<template>
    <div v-if="meta && meta.last_page > 1" class="flex items-center justify-end gap-4 text-gray-700">

        <ActionButton type="prev"
                      size="md"
                      :loading="loading"
                      @click="previousPage"
                      :disabled="isFirstPage"/>

        <div class="flex items-center gap-2">
            <input
                type="number"
                v-model.number="page"
                @keyup.enter="goToPage"
                min="1"
                :max="meta.last_page"
                class="w-16 p-2 text-center border border-gray-300 rounded-md shadow-sm sm:text-sm"
            />
            <div class="text-sm whitespace-nowrap text-gray-500 w-full">/ {{ meta.last_page }}</div>
        </div>

        <ActionButton type="next"
                      size="md"
                      @click="nextPage"
                      :loading="loading"
                      :disabled="isLastPage"/>
    </div>
</template>

<style scoped>
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type='number'] {
    -moz-appearance: textfield;
}
</style>