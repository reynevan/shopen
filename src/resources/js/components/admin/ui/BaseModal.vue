<script setup>
import {useTemplateRef} from "vue";

const emits = defineEmits(['onClose']);

const modalContent = useTemplateRef('modal-content');

const close = (e) => {
    if (!props.closable) {
        return;
    }
    if (modalContent.value && (modalContent.value.contains(e.target) || modalContent.value === e.target)) {
        return;
    }
    emits('onClose');
};

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    closable: {
        type: Boolean,
        default: true
    },
    fullWidth: {
        type: Boolean,
        default: false
    },
    class: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <Teleport to="body">
        <div v-if="show"
             @click="close"
             class="fixed inset-0 flex items-center justify-center z-100">
            <div class="modal-backdrop absolute inset-0 bg-black/60"></div>
            <div ref="modal-content"
                 :class="[
                     props.class,
                     fullWidth ? 'w-full mx-10' : ''
                 ]"
                 class="modal-content relative bg-white rounded-lg max-h-[100vh] shadow-xl flex flex-col">
                <div class="py-4 px-8 mb-4 border-b text-lg bg-white shadow relative flex-shrink-0" v-if="$slots.header">
                    <slot name="header"/>
                    <button v-if="closable" class="absolute right-2 top-2 px-2 py-2 hover:shadow cursor-pointer" @click="emits('onClose')">
                        <i class="bi bi-x-lg font-xl"></i>
                    </button>
                </div>
                <div class="px-8 pb-8 overflow-y-auto flex-1 min-h-0">
                    <slot/>
                </div>
                <div class="py-4 px-8 mt-4 border-t text-lg flex-shrink-0" v-if="$slots.buttons">
                    <div class="flex items-center justify-center gap-6">
                        <slot name="buttons"/>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>