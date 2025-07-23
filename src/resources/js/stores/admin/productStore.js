import {defineStore} from "pinia";
import axios from "axios";

export const useProductStore = defineStore('product', () => {

    const getById = async (id) => {
        return await axios.get(`/admin/api/products/${id}`).then(response => {
            return response.data.data;
        });
    }

    const save = async (product) => {
        const data = {
            product: product,
            images: product.images.map(image => {
                const imageData = {order: image.order};
                if (image.id) {
                    imageData.id = image.id;
                } else {
                    imageData.path = image.path;
                }
                return imageData;
            })
        };

        let url = '/admin/api/products';
        if (product.id) {
            url = `/admin/api/products/${product.id}`
        }
        return axios.post(url, data).then(response => {

        });
    }

    return {
        save,
        getById
    }
});