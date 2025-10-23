<script setup>
    import IconX from "../../../icons/IconX.vue";

    defineProps({
        label: {type: String},
        attributeName: {type: String},
    })

    const emits = defineEmits(['onClick'])
</script>

<template>
    <button @click="emits('onClick')"
            :data-facet="attributeName"
            :data-value="label"
            class="inline-flex group cursor-pointer items-center pl-2 pr-1 py-1 gap-1 sm:text-sm border border-light sm:border-0 sm:bg-accent-hover transition-all duration-300 hover:shadow font-light whitespace-nowrap">
        <span>{{ label }}</span>
        <span class="sm:pt-1"><IconX md/></span>
    </button>
</template>