<script setup>
import { computed, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import BaseModal from "@shopen/components/frontend/ui/BaseModal.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {useShoppingListStore} from "../../../stores/shoppingList";
import Checkbox from "../input/Checkbox.vue";
import IconPlus from "../../icons/IconPlus.vue";
import Input from "../input/Input.vue";
import IconCheck from "../../icons/IconCheck.vue";
import IconX from "../../icons/IconX.vue";


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

const cancelNewListForm = () => {
    showNewListInput.value = false;
    newListForm.reset()
}

const isSubmitDisabled = computed(() => {
    return form.processing;
});
</script>

<template>
    <BaseModal :show="shoppingListStore.isModalOpen" @onClose="closeModal">
        <template #header>
            Dodaj do listy zakupowej
        </template>
        <div class="shopping-list-modal">
            <form @submit.prevent="submit">
                <div v-if="userLists.length > 0" class="space-y-3 mb-4">
                    <p class="text-sm mb-4">Wybierz listy, do których chcesz dodać produkt.</p>
                    <div v-for="list in userLists" :key="list.id" class="flex items-center">
                        <Checkbox :id="`list-${list.id}`"
                                  :value="list.id"
                                  v-model="form.list_ids"/>
                        <label :for="`list-${list.id}`" class="block list-name">
                            {{ list.name }} ({{ list.products_count }} prod.)
                        </label>
                    </div>
                </div>

                <div class="mt-4">
                    <div v-if="showNewListInput">
                        <div class="flex items-center space-x-2">
                            <Input
                                id="new-list-name"
                                v-model="newListForm.name"
                                placeholder="Nazwa nowej listy"
                                @keyup.enter.prevent="saveNewList"
                            />
                            <Button @click.prevent="saveNewList"
                                    size="sm"
                                    icon-size="xl"
                                    :loading="newListForm.processing">
                                <IconCheck size="xl"/>
                            </Button>
                            <Button @click.prevent="cancelNewListForm"
                                    size="sm"
                                    icon-size="xl"
                                    :loading="newListForm.processing"
                                    type="ghost">
                                <IconX size="xl"/>
                            </Button>
                        </div>
                        <p v-if="newListForm.errors.name" class="mt-2 text-sm text-red-600">
                            {{ newListForm.errors.name }}
                        </p>
                    </div>
                    <Button v-else @click.prevent="showNewListInput = true" type="ghost" no-padding-x>
                        <IconPlus size="xl"/>
                        Dodaj nową listę
                    </Button>
                </div>
            </form>
        </div>

        <template #buttons>
            <Button @click="closeModal" type="ghost" class="mr-3">Anuluj</Button>
            <Button
                @click="submit"
                role="submit"
                :class="{ 'opacity-25': isSubmitDisabled }"
                :disabled="isSubmitDisabled"
            >
                Zapisz
            </Button>
        </template>
    </BaseModal>
</template>