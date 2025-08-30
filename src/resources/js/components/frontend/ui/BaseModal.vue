<script setup>
import {useTemplateRef} from "vue";
import IconX from "@shopen/components/icons/IconX.vue";

const emits = defineEmits(['onClose']);

const cover = useTemplateRef('cover')
const onCoverCLick = (e) => {
    if (!props.closableCover) {
        return;
    }
    if (cover.value !== e.target) {
        return;
    }
    emits('onClose');
};
const close = (e) => {
    if (!props.closable) {
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
    closableCover: {
        type: Boolean,
        default: true
    },
    class: {
        type: String,
        default: ''
    },
    size : {
        type: String,
        default: 'md'
    }
});


</script>

<template>
    <Teleport to="body">
        <transition name="modal-transition">
            <div v-if="show"
                 @click="onCoverCLick"
                 class="fixed inset-0 flex items-center justify-center z-100">
                <div ref="cover" class="modal-backdrop absolute inset-0 bg-black/60"></div>
                <div :class="props.class"
                     class="modal-content relative bg-white rounded-lg max-h-[100vh] shadow-xl flex flex-col">
                    <div class="py-4 px-8 mb-4 border-b text-lg bg-white shadow relative flex-shrink-0" v-if="$slots.header">
                        <slot name="header"/>
                        <button v-if="closable" class="absolute right-2 top-2 px-2 py-2 hover:shadow cursor-pointer" @click="emits('onClose')">
                            <IconX md/>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1 min-h-0" :class="size === 'sm' ? 'pb-2 px-4' : 'pb-8 px-8'">
                        <slot/>
                    </div>
                    <div class="py-4 px-8 border-t text-lg flex-shrink-0" v-if="$slots.buttons">
                        <div class="flex items-center justify-center gap-6">
                            <slot name="buttons"/>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style>
.modal-transition-enter-active,
.modal-transition-leave-active {
    transition: all 0.3s ease-out;
}

.modal-transition-enter-active .modal-backdrop,
.modal-transition-leave-active .modal-backdrop {
    transition: opacity 0.3s ease-out;
}

.modal-transition-enter-active .modal-content,
.modal-transition-leave-active .modal-content {
    transition: all 0.3s ease-out;
}


.modal-transition-enter-from .modal-backdrop {
    opacity: 0;
}

.modal-transition-enter-from .modal-content {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}


.modal-transition-leave-to .modal-backdrop {
    opacity: 0;
}

.modal-transition-leave-to .modal-content {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}
</style>