<script setup>
import { ChromePicker } from 'vue-color'
import 'vue-color/style.css';
import {onUnmounted, ref, watch} from "vue";

const model = defineModel()
const picker = ref(null)

const showPicker = ref(false)
const showBelow = ref(false)

const handleClickOutside = (event) => {
    if (picker.value && !picker.value.contains(event.target)) {
        showPicker.value = false;
    }
};

const checkPosition = () => {
    if (picker.value) {
        const rect = picker.value.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        showBelow.value = rect.top < windowHeight / 2;
    }
};

watch(showPicker, (value) => {
    if (value) {
        checkPosition();
        document.addEventListener('click', handleClickOutside);
    } else {
        document.removeEventListener('click', handleClickOutside);
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="w-8 h-8 cursor-pointer rounded relative border" ref="picker" :style="{'background-color': model ?? '#000'}" @click="showPicker = true">
        <div v-if="showPicker" class="absolute z-2" :class="showBelow ? 'top-full mt-4' : 'bottom-full mb-4'">
            <ChromePicker v-model="model"/>
        </div>
    </div>
</template>