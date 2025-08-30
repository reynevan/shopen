import {defineStore} from "pinia";
import {ref} from "vue";

export const useGeoWidgetStore = defineStore('geoWidget', () => {

    const opened = ref(false);

    const open = () => {
        opened.value = true
    }

    const close = () => {
        opened.value = false
    }
    const onPointSelectCallback = ref(null)

    const onPointSelect = (callback) => {
        onPointSelectCallback.value = callback
    }

    const selectPoint = (point) => {
        if (onPointSelectCallback.value) {
            onPointSelectCallback.value(point)
        }
    }

    return {
        opened,
        open,
        close,
        selectPoint,
        onPointSelect
    }
})