<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "@shopen/components/frontend/layout/header/Header.vue";
import FlashMessage from "@shopen/components/frontend/ui/FlashMessage.vue";
import Breadcrumbs from "@shopen/components/frontend/ui/Breadcrumbs.vue";
import Footer from "@shopen/components/frontend/layout/footer/Footer.vue";
import Minicart from "@shopen/components/frontend/cart/Minicart.vue";
import CookiesModal from "@shopen/components/frontend/cookies/CookiesModal.vue";
import TopBar from "@shopen/components/frontend/ui/TopBar.vue";

</script>

<template>
    <TopBar/>
    <div class="flex flex-col min-h-screen">
        <Header/>
        <div class="main-container grow">
            <Breadcrumbs/>
            <main class="bg-body px-4 sm:px-16 py-12 shadow">
                <slot/>
            </main>
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