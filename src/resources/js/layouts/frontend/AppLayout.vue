<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "@shopen/components/frontend/layout/header/Header.vue";
import FlashMessage from "@shopen/components/frontend/ui/FlashMessage.vue";
import Breadcrumbs from "@shopen/components/frontend/ui/Breadcrumbs.vue";
import AddToShoppingListModal from "@shopen/components/frontend/shoppingList/AddToShoppingListModal.vue";
import Footer from "@shopen/components/frontend/layout/footer/Footer.vue";
import Minicart from "@shopen/components/frontend/cart/Minicart.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import CookiesModal from "../../components/frontend/cookies/CookiesModal.vue";
import BannersContainer from "../../components/frontend/banner/BannersContainer.vue";
import TopBar from "@shopen/components/frontend/ui/TopBar.vue";


const page = usePage();
const cart = computed(() => page.props.cart);

</script>

<template>
    <TopBar/>
    <div class="flex flex-col min-h-screen">
        <Header/>
        <div class="grow">
            <div class="main-container">
                <Breadcrumbs/>
                <BannersContainer :banners="page.props.banners?.page_top"/>
            </div>
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