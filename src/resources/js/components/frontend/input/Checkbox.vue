<script setup>
import IconCheck from "../../icons/IconCheck.vue";

const model = defineModel()
const props = defineProps({
    required: { type: Boolean, default: false },
    id: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    label: { type: String },
    value: { type: String },
})
</script>

<template>

    <div class="inline-flex items-center">
        <label class="flex items-center cursor-pointer relative" :for="id">
            <input type="checkbox"
                   class="peer h-5 w-5 cursor-pointer transition-all appearance-none shadow hover:shadow-md border border-slate-300 checked:bg-slate-800 checked:border-slate-800"
                   :id="id"
                   v-model="model"
                   :required="required"
                   :disabled="disabled"
                   :value="value" />
            <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <IconCheck/>
            </span>
        </label>
        <label v-if="label" class="cursor-pointer ml-2 text-slate-600 text-sm" :for="id">
            {{ label }}
        </label>
    </div>
</template>