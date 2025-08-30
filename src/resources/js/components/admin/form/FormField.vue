<script setup>

const model = defineModel()
const props = defineProps({
    required: {
        type: Boolean,
        default: false
    },
    label: {
        type: String
    },
    labelFor: {
        type: String
    },
    error: {
        type: String
    }
})
</script>

<template>
    <div class="flex mb-4 items-stretch">
        <div class="w-[20rem] mr-4 pt-2">
            <div>
                <label class="text-right w-full block" :for="labelFor">
                    {{ props.label }} <span v-if="props.required" class="text-red-400">*</span>
                </label>
            </div>
            <div class="text-xs text-neutral-400 text-right">
                <slot name="description"/>
            </div>
        </div>
        <div class="w-full">
            <slot></slot>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>
    </div>
</template>

<style scoped>

</style>