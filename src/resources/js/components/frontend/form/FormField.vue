<script setup>

const model = defineModel()
const props = defineProps({
    label: {
        type: String
    },
    field: {
        type: String
    },
    error: {
        type: String
    },
    required: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <div class="form-field w-full mb-4 group" :class="{'has-error': error}">
        <div class="w-full">
            <label :for="field">
                {{ props.label }} <span class="text-red-500 text-lg" v-if="required">*</span>
            </label>
            <slot name="default"/>
            <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
        </div>
    </div>
</template>

<style scoped>

</style>