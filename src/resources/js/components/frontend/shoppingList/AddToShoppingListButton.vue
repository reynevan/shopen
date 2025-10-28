<script setup>

import {useForm, usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import {useShoppingListStore} from "../../../stores/shoppingList";
import IconHeart from "../../icons/IconHeart.vue";
import IconHeartFull from "../../icons/IconHeartFull.vue";
import IconLoader from "../../icons/IconLoader.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import {trackAddToWishlist} from "../../../utils/ga4";

const props = defineProps({
    product: {type: Object},
    size: {type: String, default: 'xl'},
    label: { type: Boolean, default: true }
})

const page = usePage();
const shoppingListStore = useShoppingListStore();

const shoppingLists = computed(() => page.props.shoppingLists || []);
const addToDefaultListForm = useForm({
    product_id: props.product.id,
});

const handleAddToShoppingList = () => {
    if (shoppingLists.value.length === 0) {
        trackAddToWishlist(props.product)
        addToDefaultListForm.post(route('user.shopping-lists.items.store'), {
            preserveScroll: true
        });
    } else {
        shoppingListStore.openModal(props.product)
    }
};
</script>

<template>
    <button class="add-to-shopping-list-btn cursor-pointer"
            :class="label ? '' : 'p-2'"
            :title="product.is_on_list ? 'Edytuj na listach' : 'Dodaj do listy'"
            :aria-label="product.is_on_list ? 'Edytuj na listach' : 'Dodaj do listy'"
            @click.prevent="handleAddToShoppingList"
            :disabled="addToDefaultListForm.processing">
        <span v-if="addToDefaultListForm.processing">
            <IconLoader :size="size"/>
        </span>
        <span v-else class="flex items-center gap-2">
            <IconHeartFull v-if="product.is_on_list" :size="size"/>
            <IconHeart v-else :size="size"/>
            <span class="mb-1" v-if="label && !product.is_on_list">Dodaj do listy</span>
            <span class="mb-1" v-if="label && product.is_on_list">Usuń z listy</span>
        </span>
    </button>

</template>