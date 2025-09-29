import {defineStore} from "pinia";
import axios from "axios";
import {ref} from "vue";

export const useCategoryStore = defineStore('category', () => {

    const expandCallbacks = ref([]);

    const reset = () => {
        expandCallbacks.value = [];
    }

    const onExpandAll = (fn) => {
        expandCallbacks.value.push(fn);
    }

    const expandAll = () => {
        expandCallbacks.value.forEach((fn) => {
            fn(true)
        })
    }

    const collapseAll = () => {
        expandCallbacks.value.forEach((fn) => {
            fn(false)
        })
    }

    return {
        reset,
        expandAll,
        collapseAll,
        onExpandAll,
    }
})