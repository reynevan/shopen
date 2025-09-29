<script setup>

import {Link} from "@inertiajs/vue3";
import IconProfile from "@shopen/components/icons/IconProfile.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import SearchBox from "@shopen/components/frontend/layout/header/SearchBox/SearchBox.vue";
import MinicartButton from "../../cart/MinicartButton.vue";
import UserMenu from "./UserMenu.vue";
import {useAuthStore} from "@shopen/stores/auth.js";
import IconHeart from "../../../icons/IconHeart.vue";
import Navigation from "./Navigation.vue";
import MobileMenu from "./MobileMenu.vue";

const auth = useAuthStore();


</script>

<template>
    <header class="header mx-auto bg-header shadow pt-4 pb-2 sm:pb-0 sticky top-0 sm:relative z-10">
        <div class="mx-auto container px-4">
            <div class="flex flex-col sm:gap-4 sm:flex-row justify-between items-center">
                <div class="hidden sm:block">
                    <Link href="/">
                        <img src="/img/shopen-logo.png" alt="Shopen"/>
                    </Link>
                </div>
                <div class="flex items-center w-full order-2 sm:order-1 h-auto sm:h-[42px]">
                    <div class="visible sm:hidden mr-4">
                        <MobileMenu/>
                    </div>
                    <SearchBox/>
                </div>
                <div class="flex justify-between items-center sm:gap-2 mx-6 order-1 sm:order-2">
                    <div class="sm:hidden">
                        <Link href="/">
                            <img src="/img/shopen-logo-mobile.png" alt="Shopen"/>
                        </Link>
                    </div>

                    <div class="user-menu-btn group relative">
                        <div class="p-2 group-hover:shadow-lg rounded-t border border-transparent group-hover:border-border-light">
                            <Link :href="route('user.orders.index')" class="flex items-center gap-2">
                                <span v-if="!auth.isLoggedIn" class="hidden sm:inline text-neutral-700">Zaloguj&nbsp;się</span>
                                <IconProfile size="2xl"/>
                            </Link>
                        </div>
                        <UserMenu/>
                    </div>

                    <Button type="ghost" :shadow="false">
                        <MinicartButton/>
                    </Button>

                    <Button type="ghost" :shadow="false">
                        <Link :href="route('shopping-lists.index')" title="Listy zakupowe">
                            <IconHeart size="2xl"/>
                        </Link>
                    </Button>

                </div>
            </div>
        </div>
        <div class="nav-panel z-auto relative hidden sm:block">
            <Navigation/>
        </div>
    </header>
</template>
<style >
.user-menu-btn:hover::after {
    height: 6px;
    content: '';
    position: absolute;
    bottom: -3px;
    left: 1px;
    right: 1px;
    background-color: var(--color-header);
    z-index: 51;
    opacity: 100%;
}
</style>