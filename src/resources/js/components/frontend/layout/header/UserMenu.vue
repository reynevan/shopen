<script setup>
import {Link} from "@inertiajs/vue3";
import {useAuthStore} from "@shopen/stores/auth.js";
import Button from "@shopen/components/frontend/ui/Button.vue";
import IconLocation from "@shopen/components/icons/IconLocation.vue";
import IconHeart from "@shopen/components/icons/IconHeart.vue";
import IconSettings from "@shopen/components/icons/IconSettings.vue";
import IconLogout from "@shopen/components/icons/IconLogout.vue";
import IconReceipt from "@shopen/components/icons/IconReceipt.vue";

const auth = useAuthStore();
</script>

<template>

    <div class="absolute top-[calc(100%-3px)] z-50 hidden group-hover:block bg-white py-2 shadow-lg border border-light rounded-b-lg rounded-r-lg">
        <div v-if="!auth.isLoggedIn" class="flex flex-col px-4">
            <Link prefetch :href="route('login')" title="zaloguj się">
                <Button type="secondary" class="whitespace-nowrap" full-width :shadow="false">
                    Zaloguj się
                </Button>
            </Link>

            <div class="my-6 flex items-center gap-4 text-neutral-500 text-sm">
                <div class="w-[60px] h-[1px] border-b border-light"></div>
                <div class="whitespace-nowrap">
                    Nie masz konta?
                </div>
                <div class="w-[60px] h-[1px] border-b border-light"></div>
            </div>

            <Link prefetch :href="route('sign-up')" title="Zarejestruj się">
                <Button type="primary" class="whitespace-nowrap" full-width :shadow="false">
                    Załóż konto
                </Button>
            </Link>
        </div>
        <ul v-if="auth.isLoggedIn">
            <li>
                <Link prefetch class="flex items-center gap-2 whitespace-nowrap py-2 px-4 hover:bg-accent transition-all"
                      title="Zamówienia"
                      :href="route('user.orders.index')">
                    <IconReceipt/>
                    Zamówienia
                </Link>
            </li>
            <li>
                <Link prefetch class="flex items-center gap-2 whitespace-nowrap py-2 px-4 hover:bg-accent transition-all"
                      title="Dane do zamówień"
                      :href="route('user.addresses.index')">
                    <IconLocation/>
                    Dane do zamówień
                </Link>
            </li>
            <li>
                <Link prefetch class="flex items-center gap-2 whitespace-nowrap py-2 px-4 hover:bg-accent transition-all"
                      title="Listy zakupowe"
                      :href="route('user.shopping-lists.index')">
                    <IconHeart/>
                    Listy zakupowe
                </Link>
            </li>
            <li>
                <Link prefetch class="flex items-center gap-2 whitespace-nowrap py-2 px-4 hover:bg-accent transition-all"
                      title="Ustawienia konta"
                      :href="route('user.settings.index')">
                    <IconSettings/>
                    Ustawienia konta
                </Link>
            </li>
            <li class="mt-2 pt-2 border-t border-light">
                <a class="flex items-center gap-2 py-2 px-4 hover:bg-accent transition-all cursor-pointer"
                   title="Wyloguj się"
                   @click.prevent="auth.logout">
                    <IconLogout/>
                    Wyloguj się
                </a>
            </li>
        </ul>
    </div>
</template>