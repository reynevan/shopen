<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

const isOpen = ref(false);

const dropdown = ref(null);

const close = () => {
    isOpen.value = false;
};

const toggle = () => {
    isOpen.value = !isOpen.value;
};

const handleClickOutside = (event) => {
    if (dropdown.value && !dropdown.value.contains(event.target)) {
        close();
    }
};

watch(isOpen, (value) => {
    if (value) {
        document.addEventListener('click', handleClickOutside);
    } else {
        document.removeEventListener('click', handleClickOutside);
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div ref="dropdown" class="relative inline-block text-left">
        <div @click="toggle">
            <slot name="trigger"></slot>
        </div>

        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 z-10 w-56 origin-top-right rounded-md bg-white shadow border border-light"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="menu-button"
            >
                <div class="py-1" role="none">
                    <slot></slot>
                </div>
            </div>
        </transition>
    </div>
</template>