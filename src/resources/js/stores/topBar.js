import {defineStore} from 'pinia'
import { ref } from 'vue'

export const useTopBarStore = defineStore('topBar', () => {

    const data = ref(null)

    const setSlides = (slides) => {
        if (slides) {
            data.value = slides
        }
    }

    return {
        setSlides,
        slides: data
    }
})