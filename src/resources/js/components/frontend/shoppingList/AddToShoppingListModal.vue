<script setup>
import { computed, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import BaseModal from "@shopen/components/frontend/ui/BaseModal.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {useShoppingListStore} from "../../../stores/shoppingList";


const emit = defineEmits(['close']);

const shoppingListStore = useShoppingListStore();

const userLists = computed(() => usePage().props.shoppingLists || []);
const showNewListInput = ref(false);

const form = useForm({
    product_id: null,
    list_ids: [],
});

const newListForm = useForm({
    name: '',
});

watch(() => shoppingListStore.product, (newVal) => {
    if (newVal) {
        form.reset();
        newListForm.reset();
        showNewListInput.value = false;
        form.product_id = shoppingListStore.product.id;

        if (shoppingListStore.product.shopping_list_ids && shoppingListStore.product.shopping_list_ids.length) {
            form.list_ids = shoppingListStore.product.shopping_list_ids;
        }
        else if (userLists.value.length > 0) {
            form.list_ids = [userLists.value[0].id];
        }
    }
});

const closeModal = () => {
    shoppingListStore.closeModal();
};

const submit = () => {
    form.put(route('shopping-lists.items.update'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            closeModal()
        }
    });
};

const saveNewList = () => {
    newListForm.product_id = shoppingListStore.product.id;
    newListForm.post(route('shopping-lists.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showNewListInput.value = false;
            newListForm.reset();
        },
    });
};

const isSubmitDisabled = computed(() => {
    return form.processing;
});
</script>

<template>
    <BaseModal :show="shoppingListStore.isModalOpen" @onClose="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Dodaj do listy zakupowej
            </h2>

            <form @submit.prevent="submit" class="mt-6">
                <div v-if="userLists.length > 0" class="space-y-3 mb-4">
                    <p class="text-sm text-gray-600 mb-4">Wybierz listy, do których chcesz dodać produkt.</p>
                    <div v-for="list in userLists" :key="list.id" class="flex items-center">
                        <input
                            type="checkbox"
                            :id="`list-${list.id}`"
                            :value="list.id"
                            v-model="form.list_ids"
                            class="h-4 w-4 rounded text-indigo-600 focus:ring-indigo-500 border-gray-300"
                        >
                        <label :for="`list-${list.id}`" class="ml-3 block text-sm font-medium text-gray-700">
                            {{ list.name }} ({{ list.products_count }} prod.)
                        </label>
                    </div>
                </div>

                <div class="mt-4">
                    <div v-if="showNewListInput">
                        <div class="flex items-center space-x-2">
                            <input
                                id="new_list_name"
                                v-model="newListForm.name"
                                type="text"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Nazwa nowej listy"
                                @keyup.enter.prevent="saveNewList"
                            />
                            <Button @click="saveNewList" :disabled="newListForm.processing" :class="{ 'opacity-25': newListForm.processing }">Zapisz</Button>
                            <Button @click="showNewListInput = false; newListForm.reset()" type="ghost">Anuluj</Button>
                        </div>
                        <p v-if="newListForm.errors.name" class="mt-2 text-sm text-red-600">
                            {{ newListForm.errors.name }}
                        </p>
                    </div>
                    <Button v-else @click="showNewListInput = true" type="outline">
                        Stwórz nową listę
                    </Button>
                </div>

                <div class="mt-8 flex justify-end border-t pt-4">
                    <Button @click="closeModal" type="ghost" class="mr-3">Anuluj</Button>
                    <Button
                        role="submit"
                        :class="{ 'opacity-25': isSubmitDisabled }"
                        :disabled="isSubmitDisabled"
                    >
                        Zapisz
                    </Button>
                </div>
            </form>
        </div>
    </BaseModal>
</template>