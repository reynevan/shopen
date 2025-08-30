<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "@shopen/components/frontend/layout/header/Header.vue";
import FlashMessage from "@shopen/components/frontend/ui/FlashMessage.vue";
import Breadcrumbs from "@shopen/components/frontend/Breadcrumbs.vue";
import AddToShoppingListModal from "@shopen/components/frontend/shoppingList/AddToShoppingListModal.vue";
import Footer from "@shopen/components/frontend/layout/footer/Footer.vue";
import Minicart from "@shopen/components/frontend/cart/Minicart.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const page = usePage();
const cart = computed(() => page.props.cart);

</script>

<template>
    <Header/>
    <div class="bg-checkout py-12">
        <div class="container mx-auto max-w-5xl px-4">
            <slot/>
        </div>
    </div>
    <Footer/>
    <Teleport to="body">
        <Cover/>
        <FlashMessage/>
    </Teleport>
</template>