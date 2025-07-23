<script setup>

import IconMinus from "@shopen/components/icons/IconMinus.vue";
import IconPlus from "@shopen/components/icons/IconPlus.vue";

const emit = defineEmits(['onChange'])

const props = defineProps({
    value: {
        required: true
    },
    min: {
        default: 0
    }
});


const inc = () => {
    props.value++;
    emit('onChange', props.value);
}
const dec = () => {
    if (props.value > 0) {
        props.value--;
        emit('onChange', props.value);
    }
}
const onChange = () => {
    if (props.value < 0 || !props.value) {
        props.value = 0;
    }
    emit('onChange', props.value);
}
</script>

<template>
    <div class="flex border border-primary-border/70">
        <button @click="dec"
                class="w-8 flex justify-center items-center  border-none cursor-pointer hover:bg-accent hover:text-primary-text transition-colors duration-300">
            <IconMinus></IconMinus>
        </button>
        <input type="number"
               :value="value"
               :min="min"
               class="w-12 text-center py-0 px-2 border-none focus:border-none hover:border-none shadow-none"
               @change="onChange">
        <button @click="inc"
                class="w-8 flex justify-center items-center border-none cursor-pointer hover:bg-accent hover:text-primary-text transition-colors duration-300">
            <IconPlus></IconPlus>
        </button>
    </div>
</template>

<style scoped>

</style>