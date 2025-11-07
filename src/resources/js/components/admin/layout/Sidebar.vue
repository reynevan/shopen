<script setup>
import {Link} from "@inertiajs/vue3";

import {useAuthStore} from "@shopen/stores/auth.js";
const auth = useAuthStore();
</script>

<template>
    <aside class="sidebar fixed left-0 top-0 bottom-0 w-[100px] bg-secondary py-6 z-50">
        <ul class="">
            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.dashboard')"
                      class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase">Daszbord</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.products.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase">Produkty</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.products.reviews.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase">Opinie</div>
                </Link>
            </li>

            <li class="mb-4 py-2  text-accent">
                <Link :href="route('admin.categories.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase">Kategorie</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent ">
                <Link :href="route('admin.orders.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase">Zamówienia</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.promo-codes.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase text-center">Kody rabatowe</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.banners.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase text-center">Bannery</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.attributes.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase text-center">Atrybuty</div>

                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.brands.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase text-center">Marki</div>

                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.tax-classes.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase text-center">Stawki VAT</div>

                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.users.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase text-center">Klienci</div>

                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.contact-messages.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase text-center">Wiadomości</div>

                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.settings.top-bar.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <div class="uppercase text-center">Ustawienia</div>

                </Link>
            </li>

        </ul>
        <ul class="menu-list border-t border-gray-600 pt-4 mt-4">
            <li class="mb-4 py-2 text-accent">
                <a @click.prevent="auth.logout" class="text-xs flex flex-col items-center cursor-pointer">
                    <div class="uppercase text-center">Wyloguj</div>
                </a>
            </li>
        </ul>
    </aside>
</template>