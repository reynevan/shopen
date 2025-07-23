import {defineStore} from "pinia";
import {ref} from "vue";

export const useCoverStore = defineStore('cover', () => {
    const onCloseEvents = [];

    const visible = ref(false);

    const open = () => {
        visible.value = true;
    }

    const close = () => {
        visible.value = false;
        for (let i = 0; i < onCloseEvents.length; i++) {
            onCloseEvents[i]();
        }
    }

    const onClose = (fn) => {
        onCloseEvents.push(fn);
    }

    return {
        visible,
        open,
        close,
        onClose
    }
})