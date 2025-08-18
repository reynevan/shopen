<script setup>
import { ref, onMounted } from 'vue';
import BaseModal from '@shopen/components/frontend/ui/BaseModal.vue';
import Button from "@shopen/components/frontend/ui/Button.vue";

const props = defineProps({
    // Nadal przyjmujemy propsy konfiguracyjne z composabla
    title: {
        type: String,
        default: 'Potwierdzenie',
    },
    message: {
        type: String,
        default: 'Czy na pewno chcesz wykonać tę akcję?',
    },
    confirmButtonText: {
        type: String,
        default: 'Potwierdź',
    },
    cancelButtonText: {
        type: String,
        default: 'Anuluj',
    },
});

const emit = defineEmits(['confirm', 'cancel']);

// --- Logika animacji ---
// 1. Wewnętrzny, reaktywny stan do kontrolowania widoczności BaseModal.
//    Zaczyna jako `false`.
const isVisible = ref(false);

// 2. Po zamontowaniu komponentu (gdy jest już w DOM, ale jeszcze niewidoczny),
//    zmieniamy stan na `true`. To uruchomi animację wejścia w <transition>.
onMounted(() => {
    // Użycie nextTick lub setTimeout(0) jest dobrą praktyką,
    // aby upewnić się, że DOM się "uspokoił" przed zmianą.
    requestAnimationFrame(() => {
        isVisible.value = true;
    });
});

// 3. Funkcje obsługujące akcje muszą teraz najpierw uruchomić animację wyjścia.
function handleAction(action) {
    // Ustawiamy stan na `false`, aby uruchomić animację wyjścia.
    isVisible.value = false;

    // Czekamy na zakończenie animacji (domyślnie w Tailwind to 150-300ms),
    // a następnie emitujemy zdarzenie. To pozwoli na płynne zniknięcie modala
    // zanim zostanie usunięty z DOM przez composable.
    setTimeout(() => {
        emit(action);
    }, 300); // Czas musi być równy lub dłuższy niż czas trwania animacji w BaseModal
}

const handleConfirm = () => handleAction('confirm');
const handleCancel = () => handleAction('cancel');
</script>

<template>
    <BaseModal
        size="sm"
        :show="isVisible"
        @onClose="handleCancel"
        class="max-w-md">
        <div class="text-center p-4">
            <h2 class="text-xl font-bold mb-2 text-gray-800">
                {{ props.title }}
            </h2>
            <p class="text-gray-600">
                {{ props.message }}
            </p>
        </div>

        <template #buttons>
            <Button
                @click="handleCancel"
                type="primary">
                {{ props.cancelButtonText }}
            </Button>
            <Button
                @click="handleConfirm"
                type="secondary">
                {{ props.confirmButtonText }}
            </Button>
        </template>
    </BaseModal>
</template>