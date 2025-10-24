<script setup>
import {computed} from 'vue'
import {Head, Link, usePage} from '@inertiajs/vue3'
import CheckoutLayout from "@shopen/layouts/frontend/CheckoutLayout.vue";
import AmountInput from "@shopen/components/frontend/input/AmountInput.vue";
import IconNoImage from "@shopen/components/icons/IconNoImage.vue";
import IconX from "@shopen/components/icons/IconX.vue";
import {useCartStore} from "@shopen/stores/cart.js";
import {useAuthStore} from "@shopen/stores/auth.js";
import ProductsCarousel from "@shopen/components/frontend/product/ProductsCarousel.vue";
import IconLoader from "@shopen/components/icons/IconLoader.vue";
import Button from "@shopen/components/frontend/ui/Button.vue";
import ProductImage from "../../../components/frontend/product/ProductImage.vue";
import {trackRemoveFromCart, trackViewCart} from "../../../utils/ga4";

defineOptions({layout: CheckoutLayout})

const props = defineProps({
    crossSellProducts: {type: Array}
})

const page = usePage();
const cart = computed(() => page.props.cart);

const auth = useAuthStore()
const cartStore = useCartStore();

trackViewCart(page.props.cart?.items ?? [])

const onQtyChange = (item, qty) => {
    item.loading = true;
    cartStore.updateItem(item.id, qty)
}
const removeItem = (item) => {
    trackRemoveFromCart(item.product, item.quantity);
    cartStore.removeItem(item);
}
</script>

<template>
    <Head>
        <title>Koszyk</title>
    </Head>
    <div class="container mx-auto px-4 py-8 max-w-7xl ">

        <h1 class="text-3xl font-semibold mb-6 sr-only">Koszyk</h1>

        <div v-if="!cart.items || cart.items.length === 0" class="text-center py-16">
            <h2 class="text-2xl font-semibold mb-4">Twój koszyk jest pusty</h2>
            <p class="text-gray-600 mb-6">Dodaj produkty do koszyka, aby kontynuować zakupy.</p>
            <Link :href="route('home')" class="button-primary">
                Wróć do sklepu
            </Link>
        </div>

        <div v-else class="flex flex-col lg:flex-row lg:items-start gap-8">

            <section class="w-full lg:flex-1" aria-labelledby="cart-items-heading">
                <h2 id="cart-items-heading" class="text-2xl font-semibold mb-4">Koszyk</h2>
                <ul class="bg-body divide-y divide-light px-4 py-4 rounded shadow">
                    <li v-for="item in cart.items" :key="item.id" class="flex flex-col md:flex-row items-center gap-4 py-4">

                        <div class="flex-shrink-0 w-24 h-24 bg-no-image-bg flex items-center justify-center overflow-hidden">
                            <Link :href="item.product.url">
                                <div class="w-[100px]">
                                    <ProductImage :alt="item.product.name" :urls="item.product.image" sizes="100px"
                                                  :width="100"/>
                                </div>
                            </Link>
                        </div>

                        <div class="flex-1 w-full flex flex-col md:flex-row md:justify-between gap-4">
                            <div class="flex-1">
                                <Link :href="item.product.url" class="text-lg hover:underline">
                                    {{ item.product.name }}
                                </Link>
                                <div v-if="item.product.attributes" class="text-sm text-gray-600 mt-1">
                                    <div v-for="attribute in item.product.attributes" :key="attribute.name">
                                        <span>{{ attribute.name }}</span>: <span class="font-medium">{{
                                            attribute.value
                                        }}</span>
                                    </div>
                                </div>
                                <div class="md:hidden mt-2 text-sm">
                                    Cena: <span class="font-semibold">{{ item.price }}</span>
                                </div>
                            </div>

                            <div class="hidden md:flex items-center justify-end w-28">
                                <span class="text-right">{{ item.price }}</span>
                            </div>

                            <div class="flex items-center justify-between md:justify-center w-full md:w-32">
                                <span class="md:hidden font-medium">Ilość:</span>
                                <AmountInput :value="item.quantity" :disabled="item.loading"
                                             @onChange="(val) => onQtyChange(item, val)"/>
                            </div>

                            <div class="flex items-center justify-between md:justify-end w-full md:w-24">
                                <span class="md:hidden font-medium">Kwota:</span>
                                <div class="text-lg font-semibold text-right min-w-[80px]">
                                    <IconLoader v-if="item.loading" class="mx-auto"/>
                                    <span v-else>{{ item.total_final_price }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-full md:w-auto flex justify-end md:justify-center pt-2 md:pt-0">
                            <button @click="removeItem(item)" class="text-gray-500 hover:text-red-600 transition-colors"
                                    aria-label="Usuń produkt z koszyka">
                                <IconX lg/>
                            </button>
                        </div>
                    </li>
                </ul>
            </section>

            <aside class="w-full lg:w-80 mt-12 p-6 rounded shadow lg:sticky top-8 bg-body">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">Podsumowanie</h2>
                <dl class="space-y-2 mb-8">
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-700">Wartość produktów:</dt>
                        <dd class="font-medium">{{ cart.subtotal }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-gray-700">Wysyłka od:</dt>
                        <dd class="font-medium">{{ cart.shipping }}</dd>
                    </div>
                    <div class="border-t my-4"></div>
                    <div class="flex justify-between items-end">
                        <dt class="text-lg font-semibold">Do zapłaty:</dt>
                        <dd class="text-2xl font-bold text-primary">
                            {{ cart.total }}
                        </dd>
                    </div>
                </dl>
                <div>
                    <Link :href="route(auth.isLoggedIn ? 'checkout.index' : 'checkout.login')">
                        <Button type="secondary" full-width>
                            Przejdź do kasy
                        </Button>
                    </Link>
                </div>
            </aside>
        </div>

        <!-- Sekcja Cross-Sell -->
        <section v-if="crossSellProducts && crossSellProducts.length" class="mt-16"
                 aria-labelledby="cross-sell-heading">
            <h2 id="cross-sell-heading" class="section-title">Może jeszcze to Ci się przyda</h2>
            <ProductsCarousel :products="crossSellProducts"/>
        </section>

    </div>
</template>