<script setup>
import {computed, ref, onMounted, onUnmounted} from "vue";
import IconChevron from "../../icons/IconChevron.vue";
import IconX from "../../icons/IconX.vue";

const props = defineProps({
    id: {type: String},
    options: {type: Array},
    placeholder: {type: String, default: ''},
    size: {type: String, default: 'md'},
    title: {type: String},
})
const model = defineModel()
const emits = defineEmits(['onChange'])
const expanded = ref(false)
const selectRef = ref(null)

const onClick = (option) => {
    model.value = option.key
    expanded.value = false
    emits('onChange', model.value)
}

const selectedOptionValue = computed(() => {
    const selected = props.options.find(opt => opt.key === model.value)
    return selected ? selected.label : null
})

const handleClickOutside = (event) => {
    if (selectRef.value && !selectRef.value.contains(event.target)) {
        expanded.value = false
    }
}

const sizeClasses = {
    sm: 'max-w-32',
    md: 'max-w-64',
    lg: 'max-w-sm',
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div ref="selectRef" class="w-full relative group" :class="sizeClasses[size]">
        <div @click="expanded = !expanded"
             :class="[
                 expanded ? 'sm:opacity-100' : 'sm:opacity-70',
                'flex gap-w items-center w-full justify-between',
                'bg-white transition-all duration-300 cursor-pointer',
                'pl-4 pr-2 py-2 group-hover:opacity-100']">
            <div class="tracking-wide text-sm">{{ selectedOptionValue ?? placeholder }}</div>
            <IconChevron down size="lg"/>
        </div>
        <div v-show="expanded" class="sm:border border-light sm:mt-2 fixed left-0 right-0 top-0 bottom-0 sm:left-0 sm:right-0 sm:top-auto sm:bottom-auto sm:absolute bg-white z-10 sm:w-full sm:shadow transition-all">
            <div v-if="title" class="sm:hidden px-4 py-2 border-b border-light flex items-center justify-between">
                <span class="text-2xl">{{ title }}</span>
                <IconX size="2xl" @click="expanded = false"/>
            </div>
            <div v-for="option in options"
                 :key="option.key"
                 class="px-4 py-2 text-lg sm:text-sm hover:bg-accent/50 cursor-pointer transition-all duration-300 tracking-wide font-light"
                 @click="onClick(option)">
                {{ option.label }}
            </div>
        </div>
    </div>
</template>