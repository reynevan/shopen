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
    'show': {
        type: Boolean,
        default: false
    },
    'closable': {
        type: Boolean,
        default: true
    },
    'class': {
        type: String,
        default: ''
    }
});


</script>

<template>
    <Teleport to="body">
        <div v-if="show"
             @click="close"
             class="fixed inset-0 flex items-center justify-center z-30">
            <div class="modal-backdrop absolute inset-0 bg-black/60"></div>
            <div ref="modal-content"
                 :class="props.class"
                 class="modal-content relative bg-white rounded-lg shadow-xl max-h-[100vh] overflow-y-auto">
                <div class="py-4 px-8 mb-4 border-b text-lg" v-if="$slots.header">
                    <slot name="header"/>
                </div>
                <div class="px-8 pb-8">
                    <slot/>
                </div>
                <div class="py-4 px-8 mt-4 border-t text-lg" v-if="$slots.buttons">
                    <div class="flex items-center justify-center gap-6">
                        <slot name="buttons"/>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>