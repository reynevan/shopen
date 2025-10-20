<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, nextTick } from 'vue';
import IconChevron from "../../../icons/IconChevron.vue";
import IconMenu from "../../../icons/IconMenu.vue";
import IconX from "../../../icons/IconX.vue";
import {useMenuStore} from "../../../../stores/menu";
import {useBodyScrollLock} from "../../../../composables/useBodyScrollLock";

// --- State Management ---

const page = usePage();
const menuStore = useMenuStore()
menuStore.setMenu(page.props.menu)

const isOpen = ref(false);
const navigationStack = ref([{ title: 'Menu', items: menuStore.menu?.categories }]);
const navigationDirection = ref('forward');

const menuPanel = ref(null);
const openMenuButton = ref(null);

const currentLevel = computed(() => navigationStack.value[navigationStack.value.length - 1]);
const isBaseLevel = computed(() => navigationStack.value.length === 1);

const bodyScrollLock = useBodyScrollLock()

const openMenu = () => {
    isOpen.value = true;
};

const closeMenu = () => {
    isOpen.value = false;
};

const selectCategory = (category) => {
    if (category.subcategories && category.subcategories.length > 0) {
        navigationDirection.value = 'forward';
        navigationStack.value.push({ title: category.name, items: category.subcategories, url: category.url });
    }
};

const goBack = () => {
    if (!isBaseLevel.value) {
        navigationDirection.value = 'backward';
        navigationStack.value.pop();
    }
};

watch(isOpen, (newVal) => {
    if (newVal) {
        bodyScrollLock.lock()
        nextTick(() => {
            menuPanel.value?.focus();
        });
        navigationStack.value = [{ title: 'Menu', items: menuStore.menu?.categories }];
    } else {
        bodyScrollLock.unlock()
        nextTick(() => {
            openMenuButton.value?.focus();
        });
    }
});

const handleKeydown = (e) => {
    if (e.key === 'Escape' && isOpen.value) {
        closeMenu();
    }
};
</script>

<template>
    <button
        ref="openMenuButton"
        @click="openMenu"
        aria-label="Otwórz menu"
        aria-haspopup="true"
        :aria-expanded="isOpen"
        class="p-2"
    >
        <IconMenu />
    </button>

    <Teleport to="body">
        <!-- Overlay -->
        <transition
            enter-active-class="transition-opacity duration-300 ease-in-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300 ease-in-out"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="isOpen"
                @click="closeMenu"
                class="fixed inset-0 z-[150] bg-black/50"
                aria-hidden="true"
            ></div>
        </transition>

        <!-- Panel menu -->
        <div
            ref="menuPanel"
            @keydown="handleKeydown"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            aria-label="Główne menu nawigacyjne"
            class="fixed top-0 bottom-0 left-0 z-[200] flex w-[85%] max-w-sm flex-col bg-white shadow-lg transition-transform duration-300 ease-in-out"
            :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Nagłówek menu -->
            <header>
                <div class="flex items-center justify-between border-b border-light p-4 font-light">
                    <h2 class="text-xl">{{ currentLevel.title }}</h2>
                    <button @click="closeMenu" aria-label="Zamknij menu" class="p-2">
                        <IconX size="2xl" />
                    </button>
                </div>
                <div v-if="!isBaseLevel" class="flex items-center justify-between bg-accent text-neutral-600 ">
                    <button @click="goBack" class="flex items-center gap-1 p-2" aria-label="Wróć do poprzedniego poziomu">
                        <IconChevron left size="xl" />
                        <span class="tracking-wider uppercase text-sm">Wstecz</span>
                    </button>
                    <Link
                        :href="currentLevel.url"
                        @click="closeMenu"
                        class="block px-4 py-3 tracking-wider uppercase text-sm">
                        Zobacz wszystkie
                    </Link>
                </div>
            </header>

            <div class="relative flex-1 overflow-y-auto overflow-x-hidden">
                <transition
                    enter-active-class="transition-transform duration-300 ease-in-out"
                    leave-active-class="transition-transform duration-300 ease-in-out"
                    :enter-from-class="navigationDirection === 'forward' ? 'translate-x-full' : '-translate-x-full'"
                    :leave-to-class="navigationDirection === 'forward' ? '-translate-x-full' : 'translate-x-full'"
                >
                    <div :key="currentLevel.title" class="absolute inset-0">
                        <nav :aria-label="currentLevel.title">
                            <ul class="flex flex-col">
                                <li v-for="category in currentLevel.items" :key="category.id">
                                    <button
                                        v-if="category.subcategories && category.subcategories.length > 0"
                                        @click="selectCategory(category)"
                                        class="flex w-full items-center justify-between px-4 py-3 text-left text-gray-700 hover:bg-gray-100"
                                    >
                                        <span class="text-lg font-light">{{ category.name }}</span>
                                        <IconChevron right />
                                    </button>
                                    <Link
                                        v-else
                                        :href="category.url"
                                        @click="closeMenu"
                                        class="block px-4 py-3 text-lg text-gray-700 hover:bg-gray-100 font-light"
                                    >
                                        {{ category.name }}
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </transition>
            </div>
        </div>
    </Teleport>
</template>