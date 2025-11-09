<script setup>

import {Link} from "@inertiajs/vue3";
import IconProfile from "@shopen/components/icons/IconProfile.vue";
import SearchBox from "@shopen/components/frontend/layout/header/SearchBox/SearchBox.vue";
import MinicartButton from "../../cart/MinicartButton.vue";
import UserMenu from "./UserMenu.vue";
import {useAuthStore} from "@shopen/stores/auth.js";
import IconHeart from "../../../icons/IconHeart.vue";
import Navigation from "./Navigation.vue";
import MobileMenu from "./MobileMenu.vue";
import Logo from "@shopen/components/frontend/ui/Logo.vue";
import { useScrollDirection } from '@shopen/composables/useScrollDirection.js'

const props = defineProps({
    hideOnScroll: {
        type: Boolean,
        default: true
    },
    showCart: {
        type: Boolean,
        default: true
    }
})

const auth = useAuthStore();

const { isScrollingDown } = props.hideOnScroll ? useScrollDirection() : false;

</script>

<template>
    <header class="header w-full mx-auto bg-header shadow pt-4 pb-2 sm:pb-0 z-10 sticky transition-all duration-300"
            :class="isScrollingDown ? '-top-full' : 'top-0'">
        <div class="mx-auto container">
            <div class="flex flex-col sm:gap-4 sm:flex-row justify-between items-center">
                <div class="hidden sm:block">
                    <Link href="/" title="Shopen - Strona główna">
                        <Logo width="140px"/>
                    </Link>
                </div>
                <div class="hidden sm:flex items-center w-full  h-auto sm:h-[42px]">
                    <SearchBox/>
                </div>
                <div class="flex justify-between items-center w-full sm:w-auto sm:gap-2 order-1 sm:order-2">
                    <div class="sm:hidden">
                        <MobileMenu/>
                    </div>

                    <div class="sm:hidden">
                        <Link href="/" title="Shopen - Strona główna">
                            <Logo width="120px"/>
                        </Link>
                    </div>

                    <div class="flex items-center sm:gap-2 pr-2 sm:pr-0">
                        <div class="user-menu-btn group relative">
                            <div class="p-2 group-hover:shadow-lg rounded-t border border-transparent group-hover:border-light">
                                <Link :href="auth.isLoggedIn ? route('user.orders.index') : route('login')" :title="auth.isLoggedIn ? 'Moje konto' : 'Zaloguj się'" class="flex items-center gap-2">
                                    <span v-if="!auth.isLoggedIn" class="hidden sm:inline text-neutral-700">Zaloguj&nbsp;się</span>
                                    <IconProfile size="2xl"/>
                                </Link>
                            </div>
                            <UserMenu/>
                        </div>

                        <div v-if="showCart">
                            <MinicartButton/>
                        </div>

                        <Link :href="route('user.shopping-lists.index')"
                              class="button ghost inline-flex items-center justify-center duration-300 transition-all p-2"
                              title="Listy zakupowe">
                            <IconHeart size="2xl"/>
                        </Link>

                        <div class="block sm:hidden">
                            <SearchBox/>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="nav-panel z-auto relative hidden sm:block">
            <Navigation/>
        </div>
    </header>
</template>