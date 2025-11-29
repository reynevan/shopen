<script setup>

    import {computed} from "vue";

    const props = defineProps({
        section: { type: String},
        title: { type: String, required: true },
        selected: { type: Boolean, default: false },
        disabled: { type: Function },
    })

    const emits = defineEmits(['onClick'])

    const onClick = () => {
        if (props.disabled && props.disabled()) {
            return;
        }
        emits('onClick')
    }

    const isDisabled = computed(() =>  props.disabled && props.disabled())
</script>

<template>
    <li class="px-4 py-2 mb-2 transition-all"
        :class="[selected ? 'bg-accent' : 'hover:bg-accent/20', isDisabled ? 'opacity-60' : 'cursor-pointer']"
        @click="onClick"
    >
        {{ title }}
    </li>
</template>