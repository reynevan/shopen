<script setup>
import { useTemplateRef } from "vue";

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
    }
});


</script>

<template>
    <Teleport to="body">
        <transition name="modal-transition">
            <div v-if="show"
                @click="close"
                class="fixed inset-0 flex items-center justify-center z-30" >
                <div class="modal-backdrop absolute inset-0 bg-black/60"></div>
                <div ref="modal-content"
                    class="modal-content relative bg-white p-8 rounded-lg shadow-xl max-h-[100vh] overflow-y-auto" >
                    <slot/>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
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