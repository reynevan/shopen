<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const flash = computed(() => page.props.flash);
const errors = computed(() => page.props.errors);

const hasErrors = computed(() => Object.keys(errors.value).length > 0);

const message = computed(() => {
    if (flash.value?.success) {
        return flash.value.success;
    }
    if (flash.value?.error) {
        return flash.value.error;
    }
    if (hasErrors.value) {
        return 'Wystąpiły błędy w formularzu. Proszę sprawdzić wprowadzone dane.';
    }
    return null;
});

const type = computed(() => {
    if (flash.value?.success) {
        return 'success';
    }
    if (flash.value?.error || hasErrors.value) {
        return 'error';
    }
    return null;
});

const show = ref(false);
let timeoutId = null;

const close = () => {
    show.value = false;
    if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
};

const showMessage = () => {
    show.value = false;

    if (timeoutId) {
        clearTimeout(timeoutId);
    }

    setTimeout(() => {
        show.value = true;
        timeoutId = setTimeout(() => close(), 5000);
    }, 100);
};

watch(
    () => page.props.flash,
    (newFlash) => {
        if (newFlash?.success || newFlash?.error) {
            showMessage();
        }
    },
    { deep: true }
);

watch(
    () => page.props.errors,
    (newErrors) => {
        if (newErrors && Object.keys(newErrors).length > 0) {
            showMessage();
        }
    },
    { deep: true }
);

if (message.value) {
    showMessage();
}
</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform opacity-0 -translate-y-4"
        enter-to-class="transform opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-300"
        leave-from-class="transform opacity-100 blur-0"
        leave-to-class="transform opacity-0 blur-xl">
        <div
            v-if="show && message"
            class="fixed top-5 right-5 z-50 flex items-center justify-between max-w-sm w-full p-4 rounded-lg shadow-lg text-white"
            :class="{
                'bg-emerald-500': type === 'success',
                'bg-red-500': type === 'error',
            }">
            <span>{{ message }}</span>
            <button @click="close" class="ml-4 p-1 rounded-full hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </transition>
</template>