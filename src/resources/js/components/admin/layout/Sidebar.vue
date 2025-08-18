<script setup>
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <aside class="sidebar fixed left-0 top-0 bottom-0 w-[100px] bg-secondary py-6 z-50">
        <ul class="">
            <li>
                <Link :href="route('admin.dashboard')">Daszbord</Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.products.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-box-seam text-2xl"></i>
                    <div class="uppercase">Produkty</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.products.reviews.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-box-seam text-2xl"></i>
                    <div class="uppercase">Opinie</div>
                </Link>
            </li>

            <li class="mb-4 py-2  text-accent">
                <Link :href="route('admin.categories.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-folder text-2xl"></i>
                    <div class="uppercase">Kategorie</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent ">
                <Link :href="route('admin.orders.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-bag-check text-2xl"></i>
                    <div class="uppercase">Zamówienia</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.promo-codes.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-percent text-2xl"></i>
                    <div class="uppercase text-center">Kody promocyjne</div>
                </Link>
            </li>

            <li class="mb-4 py-2 text-accent">
                <Link :href="route('admin.banners.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-percent text-2xl"></i>
                    <div class="uppercase text-center">Bannery</div>
                </Link>
            </li>

        </ul>

        <p class="menu-label">Konto</p>
        <ul class="menu-list">
            <li>
                <a class="text-accent"> Wyloguj </a>
            </li>
        </ul>
    </aside>
</template>