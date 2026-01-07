<script setup>
    import IconDots from "../../../../../components/icons/IconDots.vue";
    import {Link, router} from "@inertiajs/vue3";
    import ShoppingListProductThumbnail from "./ShoppingListProductThumbnail.vue";
    import Dropdown from "../../../../../components/frontend/ui/Dropdown.vue";
    import IconTrash from "../../../../../components/icons/IconTrash.vue";
    import Button from "@shopen/components/frontend/ui/Button.vue";
    import {useConfirm} from "../../../../../composables/useConfirm";

    const props = defineProps({
        list: {type: Object}
    })

    const {confirm} = useConfirm()

    const removeList = async () => {
        const isConfirmed = await confirm({
            title: 'Potwierdź usunięcie',
            message: `Na pewno chcesz usunąć listę "${props.list.name}"?`,
            confirmButtonText: 'Tak, usuń',
            cancelButtonText: 'Nie, wróć'
        });
        if (!isConfirmed) {
            return;
        }
        router.delete(route('user.shopping-lists.destroy', props.list.id), {
            preserveScroll: true,
        });
    }
</script>

<template>
    <div class="border p-4 relative hover:shadow transition-all">
        <div class="absolute top-2 right-1">
            <Dropdown>
                <template #trigger>
                    <Button type="ghost" size="sm">
                        <IconDots size="2xl"/>
                    </Button>
                </template>
                <button class="cursor-pointer w-full flex items-center justify-start gap-2 px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-all"
                        @click="removeList"
                        role="menuitem">
                    <IconTrash/> Usuń
                </button>
            </Dropdown>
        </div>

        <Link :href="route('user.shopping-lists.show', list.id)" prefetch>

            <!-- Wyświetlanie nazwy listy -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold">{{ list.name }}</h3>
            </div>

            <ul v-if="list.products.length > 0" class="flex gap-4 flex-wrap">
                <div v-for="product in list.products" :key="product.id" class="relative group">
                    <ShoppingListProductThumbnail :product="product"/>
                </div>
            </ul>
            <p v-else class="font-light">Nie masz jeszcze żadnych produktów na tej liście</p>
        </Link>
    </div>
</template>