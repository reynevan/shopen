<script setup>
import { computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useFlash } from '@shopen/composables/useFlash.js';

const page = usePage();
const flash = useFlash();

const errors = computed(() => page.props.errors);
const hasErrors = computed(() => Object.keys(errors.value).length > 0);

// Obsługa flash messages z serwera (Inertia)
watch(
    () => page.props.flash,
    (newFlash) => {
        if (newFlash?.success) {
            flash.success(newFlash.success);
        }
        if (newFlash?.error) {
            flash.error(newFlash.error);
        }
    },
    { deep: true, immediate: true }
);

// Obsługa błędów walidacji
watch(
    () => page.props.errors,
    (newErrors) => {
        if (newErrors && Object.keys(newErrors).length > 0) {
            flash.error('Wystąpiły błędy w formularzu. Proszę sprawdzić wprowadzone dane.');
        }
    },
    { deep: true }
);

const getBackgroundClass = (type) => {
    const classes = {
        success: 'bg-emerald-500',
        error: 'bg-red-500',
        warning: 'bg-amber-500',
        info: 'bg-blue-500'
    };
    return classes[type] || 'bg-gray-500';
};
</script>

<template>
    <div class="fixed top-[100px] right-5 z-[200] flex flex-col gap-2 max-w-sm w-full">
        <transition-group
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 -translate-y-4"
            enter-to-class="transform opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-300"
            leave-from-class="transform opacity-100 blur-0"
            leave-to-class="transform opacity-0 blur-xl">
            <div
                v-for="msg in flash.messages.value"
                :key="msg.id"
                class="flex items-center justify-between w-full p-4 rounded-lg shadow-lg text-white"
                :class="getBackgroundClass(msg.type)">
                <span>{{ msg.message }}</span>
                <button
                    @click="flash.remove(msg.id)"
                    class="ml-4 p-1 rounded-full hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </transition-group>
    </div>
</template>