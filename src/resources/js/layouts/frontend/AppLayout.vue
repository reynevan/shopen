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
import CookiesModal from "../../components/frontend/cookies/CookiesModal.vue";
import BannersContainer from "../../components/frontend/banner/BannersContainer.vue";

const page = usePage();
const cart = computed(() => page.props.cart);

</script>

<template>
    <div class="flex flex-col min-h-screen">
        <Header/>
        <div class="container mx-auto max-w-7xl sm:px-4 py-6 grow">
            <div class="mb-6">
                <Breadcrumbs/>
            </div>
            <BannersContainer :banners="page.props.banners?.page_top"/>
            <main>
                <slot/>
            </main>
        </div>
        <Footer/>
    </div>
    <Teleport to="body">
        <Cover/>
        <Minicart :items="cart.items" :subtotal="cart.subtotal"/>
        <AddToShoppingListModal/>
        <FlashMessage/>
        <CookiesModal/>
    </Teleport>
</template>