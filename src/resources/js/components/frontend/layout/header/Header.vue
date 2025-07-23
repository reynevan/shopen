<script setup>

import MenuButton from "@shopen/components/frontend/layout/header/MenuButton.vue";
import Minicart from "@shopen/components/frontend/cart/Minicart.vue";
import {usePage, Link} from "@inertiajs/vue3";
import {computed} from "vue";
import {useAuthStore} from "@shopen/stores/auth.js";
import NavCategories from "@shopen/components/frontend/layout/header/NavCategories.vue";

const page = usePage();
const cart = computed(() => page.props.cart);

const auth = useAuthStore();
</script>

<template>
    <header class="header py-6 sticky top-0 sm:relative z-10">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="visible sm:hidden mr-4">
                    <MenuButton/>
                </div>
                <div class="font-2xl flex justify-center">
                    LOGO
                </div>
            </div>
            <div class="flex justify-between items-center mx-6">

                <div class="group" v-if="auth.isLoggedIn">
                    <div class="">
                        <Link :href="route('user.orders.index')">Moje konto</Link>
                    </div>
                    <div class="absolute invisible group-hover:visible">
                        <div>
                            <span @click="auth.logout">Wyloguj</span>
                        </div>
                    </div>
                </div>
                <div v-if="!auth.isLoggedIn">
                    <a :href="route('login')">Logowanie</a> | <a href="{{ route('register') }}">Rejestracja</a>
                </div>
                <div class="ml-4">
                    <Minicart :items="cart.items" :itemsCount="cart.itemsCount" :subtotal="cart.subtotal"/>
                </div>
            </div>
        </div>
    </header>
    <div class="nav-panel z-30 sm:z-auto transition-[left] duration-500 top-0 bottom-0 fixed sm:relative" id="nav-panel">
        <NavCategories/>
    </div>
</template>

<style scoped>

</style>