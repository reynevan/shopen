<script setup>
import IconCheck from "../../icons/IconCheck.vue";

const model = defineModel()
const props = defineProps({
    required: { type: Boolean, default: false },
    id: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    label: { type: String },
    value: { type: String },
    checked: { type: Boolean, default: false },
})
const emits = defineEmits(["change"])
</script>

<template>

    <div class="checkbox inline-flex items-center">
        <label class="flex items-center cursor-pointer relative bg-white" :for="id">
            <input type="checkbox"
                   class="checkbox-input peer cursor-pointer transition-all appearance-none"
                   :id="id"
                   :checked="checked"
                   @change="emits('change')"
                   v-model="model"
                   :required="required"
                   :disabled="disabled"
                   :value="value" />
            <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                <IconCheck/>
            </span>
        </label>
        <label v-if="label" class="checkbox-label cursor-pointer ml-2" :for="id">
            {{ label }}
        </label>
    </div>
</template>