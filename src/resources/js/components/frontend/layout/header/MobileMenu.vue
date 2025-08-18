<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, nextTick } from 'vue';
import IconChevron from "../../../icons/IconChevron.vue";
import IconMenu from "../../../icons/IconMenu.vue";
import IconX from "../../../icons/IconX.vue";

// --- State Management ---

const page = usePage();
const categories = page.props.menu.categories;

const isOpen = ref(false);
// Stos nawigacji przechowuje "ścieżkę" kliknięć użytkownika.
// Każdy element to obiekt z tytułem i listą kategorii do wyświetlenia.
const navigationStack = ref([{ title: 'Menu', items: categories }]);
// Kierunek animacji (w przód / w tył)
const navigationDirection = ref('forward');

// Elementy DOM do zarządzania focusem
const menuPanel = ref(null);
const openMenuButton = ref(null);

// --- Computed Properties ---

// Zawsze wyświetlamy ostatni element ze stosu nawigacji.
const currentLevel = computed(() => navigationStack.value[navigationStack.value.length - 1]);
// Sprawdzamy, czy jesteśmy na najwyższym poziomie menu.
const isBaseLevel = computed(() => navigationStack.value.length === 1);

// --- Methods ---

const openMenu = () => {
    isOpen.value = true;
};

const closeMenu = () => {
    isOpen.value = false;
};

// Przejście do podkategorii
const selectCategory = (category) => {
    if (category.subcategories && category.subcategories.length > 0) {
        navigationDirection.value = 'forward';
        navigationStack.value.push({ title: category.name, items: category.subcategories });
    }
};

// Powrót do nadrzędnej kategorii
const goBack = () => {
    if (!isBaseLevel.value) {
        navigationDirection.value = 'backward';
        navigationStack.value.pop();
    }
};

// --- Accessibility & Side Effects (Watchers) ---

// Zarządzanie stanem strony (blokowanie scrolla, focus) po otwarciu/zamknięciu menu
watch(isOpen, (newVal) => {
    if (newVal) {
        // Blokujemy scrollowanie body
        document.body.classList.add('overflow-hidden');
        // Ustawiamy focus wewnątrz menu po jego otwarciu
        nextTick(() => {
            menuPanel.value?.focus();
        });
        // Resetujemy nawigację do stanu początkowego
        navigationStack.value = [{ title: 'Menu', items: categories }];
    } else {
        // Odblokowujemy scrollowanie
        document.body.classList.remove('overflow-hidden');
        // Przywracamy focus na przycisk, który otworzył menu
        nextTick(() => {
            openMenuButton.value?.focus();
        });
    }
});

// Obsługa zamykania menu klawiszem Escape
const handleKeydown = (e) => {
    if (e.key === 'Escape' && isOpen.value) {
        closeMenu();
    }
};
</script>

<template>
    <!-- Przycisk otwierający menu -->
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
            <header class="flex items-center justify-between border-b border-gray-200 p-4">
                <button v-if="!isBaseLevel" @click="goBack" class="flex items-center gap-2 p-2 text-lg font-semibold" aria-label="Wróć do poprzedniego poziomu">
                    <IconChevron left size="xl" />
                    <span>{{ currentLevel.title }}</span>
                </button>
                <h2 v-else class="text-xl font-bold">{{ currentLevel.title }}</h2>

                <button @click="closeMenu" aria-label="Zamknij menu" class="p-2">
                    <IconX size="2xl" />
                </button>
            </header>

            <!-- Kontener na zmieniające się listy kategorii (dla animacji) -->
            <div class="relative flex-1 overflow-y-auto overflow-x-hidden">
                <transition
                    enter-active-class="transition-transform duration-300 ease-in-out"
                    leave-active-class="transition-transform duration-300 ease-in-out"
                    :enter-from-class="navigationDirection === 'forward' ? 'translate-x-full' : '-translate-x-full'"
                    :leave-to-class="navigationDirection === 'forward' ? '-translate-x-full' : 'translate-x-full'"
                >
                    <!-- Używamy `key`, aby Vue wiedziało, że komponent się zmienił i należy go animować -->
                    <div :key="currentLevel.title" class="absolute inset-0">
                        <nav :aria-label="currentLevel.title">
                            <ul class="flex flex-col">
                                <li v-for="category in currentLevel.items" :key="category.id">
                                    <!-- Jeśli kategoria ma podkategorie, jest przyciskiem -->
                                    <button
                                        v-if="category.subcategories && category.subcategories.length > 0"
                                        @click="selectCategory(category)"
                                        class="flex w-full items-center justify-between px-4 py-3 text-left text-gray-700 hover:bg-gray-100"
                                    >
                                        <span class="text-lg">{{ category.name }}</span>
                                        <IconChevron right />
                                    </button>
                                    <!-- W przeciwnym razie jest linkiem nawigacyjnym -->
                                    <Link
                                        v-else
                                        :href="category.url"
                                        @click="closeMenu"
                                        class="block px-4 py-3 text-lg text-gray-700 hover:bg-gray-100"
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