<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "@shopen/components/frontend/layout/header/Header.vue";
import FlashMessage from "@shopen/components/frontend/ui/FlashMessage.vue";
import Footer from "@shopen/components/frontend/layout/footer/Footer.vue";
import Minicart from "@shopen/components/frontend/cart/Minicart.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import CookiesModal from "../../components/frontend/cookies/CookiesModal.vue";

const page = usePage();
const cart = computed(() => page.props.cart);

</script>

<template>
    <div class="flex flex-col h-screen">
        <Header/>
        <div class="bg-checkout py-12 grow">
            <div class="container mx-auto max-w-7xl sm:px-4">
                <slot/>
            </div>
        </div>
        <Footer/>
    </div>
    <Teleport to="body">
        <Cover/>
        <Minicart :items="cart.items" :subtotal="cart.subtotal"/>
        <FlashMessage/>
        <CookiesModal/>
    </Teleport>
</template>