<script setup>

import AppLayout from "./AppLayout.vue";
import {Link} from "@inertiajs/vue3";
</script>

<template>
<AppLayout>
    <div class="flex flex-col sm:flex-row">
        <nav class="flex-shrink-0 w-60">
            <ul>
                <li>
                    <Link :href="route('user.orders.index')">Zamówienia</Link>
                </li>
                <li>
                    <Link :href="route('user.addresses.index')">Dane do zamówień</Link>
                </li>
                <li>
                    <Link :href="''">Ustawienia konta</Link>
                </li>
            </ul>
        </nav>
        <div class="flex-1 min-w-0">
            <slot/>
        </div>
    </div>
</AppLayout>
</template>

<style scoped>

</style>