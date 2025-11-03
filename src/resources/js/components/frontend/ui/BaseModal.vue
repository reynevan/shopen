<script setup>
import {useTemplateRef, watch} from "vue";
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
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-300 ease-out"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" @click="onCoverCLick" class="fixed inset-0 z-[100] flex items-center justify-center">
                <!-- Backdrop -->
                <div ref="cover"
                     class="absolute inset-0 bg-black/60 transition-opacity duration-300 ease-out
                    data-[enter-from]:opacity-0 data-[leave-to]:opacity-0">
                </div>

                <!-- Modal -->
                <div :class="props.class"
                     class="relative bg-white shadow-lg max-h-[100vh] flex flex-col
                    transition-all duration-300 ease-out
                    data-[enter-from]:opacity-0 data-[enter-from]:translate-y-5 data-[enter-from]:scale-95
                    data-[leave-to]:opacity-0 data-[leave-to]:translate-y-5 data-[leave-to]:scale-95">
                    <div v-if="$slots.header" class="py-4 px-8 mb-4 border-b text-lg bg-white shadow relative flex-shrink-0">
                        <slot name="header" />
                        <button v-if="closable"
                                class="absolute right-2 top-2 px-2 py-2 hover:shadow cursor-pointer"
                                @click="emits('onClose')">
                            <IconX md />
                        </button>
                    </div>

                    <div class="overflow-y-auto flex-1 min-h-0 max-w-2xl"
                         :class="size === 'sm' ? 'pb-2 px-4' : 'pb-8 px-8'">
                        <slot />
                    </div>

                    <div v-if="$slots.buttons" class="py-4 px-8 border-t text-lg flex-shrink-0">
                        <div class="flex items-center justify-center gap-6">
                            <slot name="buttons" />
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>