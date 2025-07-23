import {defineStore} from "pinia";
import axios from "axios";
import {ref} from "vue";

export const useCategoryStore = defineStore('category', () => {

    const selectedCategory = ref({
        attributes: []
    });

    const getAll = async () => {
        return axios.get('/admin/api/categories').then(response => {
            return response.data.data;
        })
    }

    const selectCategory = (category) => {
        selectedCategory.value = category;
    }

    const move = (categories) => {
        return axios.post(`/admin/api/categories/move`, {
            categories
        })
    }

    const save = (category) => {
        if (category.id) {
            return axios.put(`/admin/api/categories/${category.id}`, {
                category
            });
        }
        return axios.post(`/admin/api/categories/`, {
            category
        });

    }

    return {
        selectedCategory,

        getAll,
        selectCategory,
        move,
        save
    }
})