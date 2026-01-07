<script setup>
import {Head, Link, router, useForm} from '@inertiajs/vue3';
import {ref} from 'vue';
import UserPanelLayout from "@shopen/layouts/frontend/UserPanelLayout.vue";
import ProductThumbnailImage from "@shopen/components/frontend/product/ProductThumbnailImage.vue";
import IconTrash from "@shopen/components/icons/IconTrash.vue";
import IconEdit from "@shopen/components/icons/IconEdit.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import IconChevron from "@shopen/components/icons/IconChevron.vue";
import AddToCartButton from "@shopen/components/frontend/product/thumbnail/AddToCartButton.vue";
import IconX from "@shopen/components/icons/IconX.vue";
import Input from "../../../../components/frontend/input/Input.vue";
import {useConfirm} from "../../../../composables/useConfirm";

defineOptions({layout: UserPanelLayout})

const props = defineProps({
    list: Object,
});

const form = useForm({
    name: '',
});

const {confirm} = useConfirm()

const editingList = ref(false);
const editForm = useForm({name: ''});


const startEditing = () => {
    editingList.value = true;
    editForm.name = props.list.name;
};

const updateList = () => {
    editForm.put(route('user.shopping-lists.update', props.list.id), {
        onSuccess: () => editingList.value = false,
    });
};

const removeList = async () => {
    const isConfirmed = await confirm({
        title: 'Potwierdź usunięcie',
        message: `Czy na pewno chcesz usunąc listę "${props.list.name}"?`,
        confirmButtonText: 'Tak, usuń',
        cancelButtonText: 'Nie, wróć'
    });

    if (!isConfirmed) {
        return;
    }

    router.delete(route('user.shopping-lists.destroy', props.list.id))
}

const removeItem = (productId) => {
    router.delete(route('user.shopping-lists.items.destroy', [props.list.id, productId]))
}
</script>

<template>
    <Head :title="list.name + ' - Moje listy zakupowe'"/>

    <div>
        <header class="flex items-center justify-between mb-6">
            <Link :href="route('user.shopping-lists.index')" rel="prev" prefetch>
                <Button type="ghost" class="pl-2">
                    <IconChevron left size="xl"/>
                    Powrót
                </Button>
            </Link>

            <button
                @click="removeList(list)"
                class="cursor-pointer flex items-center justify-start gap-2 px-4 py-2 text-sm hover:text-red-700 transition-all"
                title="Usuń całą listę zakupową"
                aria-label="Usuń całą listę zakupową"
            >
                <IconTrash/>
                <span class="hidden sm:inline">Usuń listę</span>
            </button>
        </header>

        <div class="flex justify-between items-center min-h-16 mb-4">
            <div v-if="!editingList" class="flex items-center gap-2">
                <h2 class="text-xl font-semibold">{{ list.name }}</h2>
                <button
                    @click="startEditing(list)"
                    aria-label="Edytuj nazwę listy"
                    title="Edytuj nazwę listy"
                    class="p-1 cursor-pointer hover:text-primary"
                >
                    <IconEdit size="2xl"/>
                </button>
            </div>

            <form v-if="editingList" @submit.prevent="updateList" class="flex items-center justify-between w-full">
                <div class="flex-grow">
                    <label for="list-name-input" class="sr-only">Nowa nazwa listy</label>
                    <Input
                        id="list-name-input"
                        v-model="editForm.name"
                        aria-label="Nowa nazwa listy"
                    />
                </div>
                <div class="ml-4 flex-shrink-0 gap-2">
                    <Button type="primary" role="submit">
                        Zapisz
                    </Button>
                    <Button type="ghost" @click="editingList = false">
                        Anuluj
                    </Button>
                </div>
            </form>
        </div>

        <div v-if="list.products.length > 0">
            <!-- Zastępujemy tabelę divami, aby uzyskać pełną kontrolę nad responsywnością -->
            <div class="border-t">
                <div v-for="product in list.products"
                     :key="product.id"
                     :class="[
                         'border-b last:border-b-0',
                         product.in_stock ? '' : 'opacity-60 hover:opacity-100 transition-all'
                     ]"
                     class="block p-4 lg:p-0 lg:flex lg:items-center lg:justify-between"
                >
                    <!-- Grupa: Zdjęcie i Nazwa -->
                    <div class="flex items-start justify-between w-full lg:w-auto lg:flex-1 lg:items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-[60px] flex-shrink-0 lg:py-4">
                                <Link :href="product.url">
                                    <ProductThumbnailImage size="sm" :product="product"/>
                                </Link>
                            </div>
                            <div class="pr-4">
                                <Link :href="product.url" class="">
                                    {{ product.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Przycisk usuwania (widoczny na końcu w układzie mobilnym) -->
                        <div class="flex-shrink-0 lg:hidden">
                            <button
                                @click="removeItem(product.id)"
                                :aria-label="`Usuń produkt z listy`"
                                :title="`Usuń produkt z listy`"
                                class="text-red-600 hover:text-red-900 transition-all cursor-pointer p-2 -mr-2"
                            >
                                <IconX size="xl"/>
                            </button>
                        </div>
                    </div>


                    <!-- Grupa: Cena, Dostępność, Akcje.
                         Używamy 'lg:contents', aby na desktopie ten div "zniknął",
                         a jego dzieci stały się częścią głównego flexboxa.
                    -->
                    <div class="flex items-center justify-between mt-4 lg:mt-0 lg:contents">
                        <!-- Cena -->
                        <div class="text-left font-semibold pr-2 lg:py-2 lg:px-4 lg:text-right lg:font-normal">
                            <div v-if="product.in_stock">
                                {{ product.price?.final_price }}
                            </div>
                        </div>

                        <!-- Dodaj do koszyka / Niedostępny -->
                        <div class="lg:w-auto lg:py-2 lg:px-2">
                            <AddToCartButton :product="product" v-if="product.in_stock"/>
                            <div v-else class="text-neutral-500 text-sm">
                                Niedostępny
                            </div>
                        </div>

                        <!-- Przycisk usuwania (widoczny tylko na desktopie w tym miejscu) -->
                        <div class="hidden lg:block lg:w-auto lg:py-2 lg:pr-2">
                            <button
                                @click="removeItem(product.id)"
                                :aria-label="`Usuń produkt z listy`"
                                :title="`Usuń produkt z listy`"
                                class="text-red-600 hover:text-red-900 transition-all cursor-pointer p-2"
                            >
                                <IconX size="xl"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p v-else class="font-light mt-8 text-center">Ta lista jest pusta.</p>

    </div>
</template>