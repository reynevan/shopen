import {defineStore} from "pinia";
import axios from "axios";

export const useAttributeStore = defineStore('attribute', () => {

    const getAll = async () => {
        return axios.get('/admin/api/attributes').then(response => {
            return response.data.data;
        })
    }

    const getProductAttributes = async () => {
        return axios.get('/admin/api/attributes/products').then(response => {
            return response.data.data;
        })
    }

    const getCategoryAttributes = async () => {
        return axios.get('/admin/api/attributes/categories').then(response => {
            return response.data.data;
        })
    }

    return {
        getAll,
        getCategoryAttributes,
        getProductAttributes
    }
})