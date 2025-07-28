<script setup>
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <aside class="sidebar fixed left-0 top-0 bottom-0 w-[100px] bg-primary-bg py-6 z-50">
        <ul class="">

            <li class="menu-item @if (Route::is('admin.products.*')) bg-neutral-300 @endif">
                <Link :href="route('admin.products.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-box-seam text-4xl"></i>
                    <div class="uppercase">Produkty</div>
                </Link>
            </li>

            <li class="menu-item @if (Route::is('admin.products.reviews.*')) bg-neutral-300 @endif">
                <Link :href="route('admin.products.reviews.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-box-seam text-4xl"></i>
                    <div class="uppercase">Opinie</div>
                </Link>
            </li>

            <li class="menu-item @if (Route::is('admin.categories.*')) bg-neutral-300 @endif">
                <Link :href="route('admin.categories.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-folder text-4xl"></i>
                    <div class="uppercase">Kategorie</div>
                </Link>
            </li>

            <li class="menu-item @if (Route::is('admin.orders.*')) bg-neutral-300 @endif">
                <Link :href="route('admin.orders.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-bag-check text-4xl"></i>
                    <div class="uppercase">Zamówienia</div>
                </Link>
            </li>

            <li class="menu-item @if (Route::is('admin.promo-codes.*')) bg-neutral-300 @endif">
                <Link :href="route('admin.promo-codes.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-percent text-4xl"></i>
                    <div class="uppercase text-center">Kody promocyjne</div>
                </Link>
            </li>

            <li class="menu-item @if (Route::is('admin.promo-codes.*')) bg-neutral-300 @endif">
                <Link :href="route('admin.banners.index')"
                   class="text-xs flex flex-col items-center"
                >
                    <i class="bi bi-percent text-4xl"></i>
                    <div class="uppercase text-center">Bannery</div>
                </Link>
            </li>

        </ul>

        <p class="menu-label">Konto</p>
        <ul class="menu-list">
            <li>
                <a> Wyloguj </a>
            </li>
        </ul>
    </aside>
</template>