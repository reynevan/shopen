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
import {onMounted, onUnmounted, ref} from "vue";

const auth = useAuthStore();

const showHeader = ref(false)
const lastScrollPosition = ref(0)

const handleScroll = () => {
    const currentScrollPosition = window.scrollY || document.documentElement.scrollTop

    if (currentScrollPosition < 0) {
        return
    }

    if (currentScrollPosition > lastScrollPosition.value) {
        showHeader.value = false
    }
    else {
        showHeader.value = true
    }

    lastScrollPosition.value = currentScrollPosition
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <header class="header mx-auto bg-header shadow pt-4 pb-2 sm:pb-0 z-10 sticky transition-all duration-300"
            :class="showHeader ? 'top-0' : '-top-full'">
        <div class="mx-auto container px-4">
            <div class="flex flex-col sm:gap-4 sm:flex-row justify-between items-center">
                <div class="hidden sm:block">
                    <Link href="/">
                        <img src="/img/shopen-logo.png" alt="Shopen"/>
                    </Link>
                </div>
                <div class="flex items-center w-full order-2 sm:order-1 h-auto sm:h-[42px]">
                    <SearchBox/>
                </div>
                <div class="flex justify-between items-center w-full sm:w-auto sm:gap-2 order-1 sm:order-2">
                    <div class="sm:hidden">
                        <MobileMenu/>
                    </div>

                    <div class="sm:hidden">
                        <Link href="/">
                            <img src="/img/shopen-logo-mobile.png" alt="Shopen"/>
                        </Link>
                    </div>

                    <div class="flex items-center sm:gap-2">
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