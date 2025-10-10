<script setup>
import {computed} from "vue";

const props = defineProps({
    errors: {}
})

const errorsArr = computed(() => {
    if (typeof props.errors === 'object') {
        return props.errors;
    }
    return [props.errors];
})
</script>

<template>
    <p class="text-red-400 text-sm" v-for="error in errorsArr">
        {{ error }}
    </p>
</template>