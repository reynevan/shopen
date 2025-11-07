<script setup>

import Cover from "@shopen/components/frontend/Cover.vue";
import Header from "@shopen/components/frontend/layout/header/Header.vue";
import FlashMessage from "@shopen/components/frontend/ui/FlashMessage.vue";
import Footer from "@shopen/components/frontend/layout/footer/Footer.vue";
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
        <Header :hide-on-scroll="false" :show-cart="false" />
        <div class="bg-checkout py-12 grow">
            <div class="container mx-auto px-4">
                <slot/>
            </div>
        </div>
    <Footer/>
    </div>
    <Teleport to="body">
        <Cover/>
        <FlashMessage/>
        <CookiesModal/>
    </Teleport>
</template>