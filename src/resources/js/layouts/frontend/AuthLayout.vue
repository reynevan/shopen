<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "@shopen/components/frontend/layout/header/Header.vue";
import FlashMessage from "@shopen/components/frontend/ui/FlashMessage.vue";
import Footer from "@shopen/components/frontend/layout/footer/Footer.vue";
import Minicart from "@shopen/components/frontend/cart/Minicart.vue";
import CookiesModal from "@shopen/components/frontend/cookies/CookiesModal.vue";
import TopBar from "@shopen/components/frontend/ui/TopBar.vue";

</script>

<template>
    <TopBar/>
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
        <Minicart/>
        <FlashMessage/>
        <CookiesModal/>
    </Teleport>
</template>