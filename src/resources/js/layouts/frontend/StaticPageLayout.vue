<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "@shopen/components/frontend/layout/header/Header.vue";
import FlashMessage from "@shopen/components/frontend/ui/FlashMessage.vue";
import Breadcrumbs from "@shopen/components/frontend/Breadcrumbs.vue";
import Footer from "@shopen/components/frontend/layout/footer/Footer.vue";
import Minicart from "@shopen/components/frontend/cart/Minicart.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import CookiesModal from "../../components/frontend/cookies/CookiesModal.vue";
import TopBar from "@shopen/components/frontend/ui/TopBar.vue";


const page = usePage();
const cart = computed(() => page.props.cart);

</script>

<template>
    <TopBar/>
    <div class="flex flex-col min-h-screen">
        <Header/>
        <div class="container mx-auto max-w-7xl grow sm:px-4">
            <div class="mb-6 mt-6">
                <Breadcrumbs/>
            </div>
            <main class="bg-body px-4 sm:px-16 py-12 shadow">
                <slot/>
            </main>
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