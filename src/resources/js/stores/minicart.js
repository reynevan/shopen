import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import {useCoverStore} from "./cover";

export const useMiniCartStore = defineStore('minicart', () => {

    const cover = useCoverStore();

    const isOpened = ref(false);

    const open = () => {
        cover.open();
        isOpened.value = true;
    }

    const close = () => {
        cover.close();
        isOpened.value = false;
    }

    return {
        isOpened,
        open,
        close
    }
})