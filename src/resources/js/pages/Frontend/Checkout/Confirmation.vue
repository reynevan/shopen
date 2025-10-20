<script setup>
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";

import {Head, Link} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";
import IconCheckCircle from "@shopen/components/icons/IconCheckCircle.vue";
import {trackPurchase} from "../../../utils/ga4";

defineOptions({layout: AppLayout})

const props = defineProps({
    order: {type: Object}
})

trackPurchase(props.order)
</script>

<template>
    <Head>
        <title>Potwierdzenie złożenia zamówienia</title>
    </Head>
    <main class="py-6 max-w-xl mx-auto text-center">
        <div class="mb-4">
            <div class="flex items-center justify-center pb-4 text-accent-hover">
                <IconCheckCircle size="8xl"/>
            </div>
            <h1 class="text-4xl mb-4">Dziękujemy za złożenie zamówienia!</h1>
            <p class="text-xl text-neutral-600 mb-2">Numer Twojego zamówienia to <span class="text-black">{{ order.order_number }}</span>.</p>
            <p class="text-xl text-neutral-600 mb-4">
                Wysłaliśmy potwierdzenie na adres: {{ order.email }}
            </p>
            <Link href="/">
                <Button type="secondary">Kontynuuj zakupy</Button>
            </Link>
        </div>
        <div v-if="!order.user_id" class="my-6 py-6 border-t border-light">
            <h2 class="text-xl mb-2">Chcesz śledzić swoje zamówienie? Załóż konto!</h2>

            <p class="mb-2">Twoje dane zostaną automatycznie powiązane z tym zamówieniem.</p>

            <Link :href="route('sign-up')">
                <Button type="secondary">Załóż konto</Button>
            </Link>

            <div class="my-6 flex items-center gap-4 text-neutral-500 text-sm">
                <div class="w-full h-[1px] border-b border-light"></div>
                <p class="whitespace-nowrap">
                    Masz już konto?
                </p>
                <div class="w-full h-[1px] border-b border-light"></div>
            </div>

            <Link :href="route('login')">
                <Button>Zaloguj się</Button>
            </Link>
        </div>
    </main>
</template>