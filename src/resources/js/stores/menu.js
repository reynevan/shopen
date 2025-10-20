import {defineStore} from 'pinia'
import { ref } from 'vue'

export const useMenuStore = defineStore('menu', () => {

    const menuData = ref(null)

    const setMenu = (menu) => {
        if (menu) {
            menuData.value = menu
        }
    }

    return {
        setMenu,
        menu: menuData
    }
})