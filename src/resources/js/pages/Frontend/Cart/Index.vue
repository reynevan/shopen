<script setup>
import {computed} from 'vue'
import {Link, usePage} from '@inertiajs/vue3'
import AppLayout from "@shopen/layouts/frontend/AppLayout.vue";
import AmountInput from "@shopen/components/frontend/input/AmountInput.vue";
import IconNoImage from "@shopen/components/icons/IconNoImage.vue";
import IconX from "@shopen/components/icons/IconX.vue";
import {useCartStore} from "@shopen/stores/cart.js";
import {useAuthStore} from "@shopen/stores/auth.js";

defineOptions({ layout: AppLayout })

const page = usePage();
const cart = computed(() => page.props.cart);

const auth = useAuthStore()
const cartStore = useCartStore();

const onQtyChange = (item, qty) => {
    cartStore.updateItem(item.id, qty)
}

</script>

<template>
    <div class="flex items-start">
        <table class="w-full mr-8">
            <thead>
            <tr>
                <th class="w-[50px]"></th>
                <th class="w-[120px]"></th>
                <th class="text-left">Produkt</th>
                <th class="w-[100px] text-right">Cena</th>
                <th class="w-[200px] text-center">Ilość</th>
                <th class="w-[150px] text-right">Kwota</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in cart.items" :key="item.id" class="cart-item py-2 border-b asd">
                <td>
                    <div @click="removeItem(item)" class="cursor-pointer">
                        <IconX lg/>
                    </div>
                </td>
                <td class="py-2">
                    <div class="mr-2 w-[100px] h-[100px] flex items-center justify-center text-no-image-icon bg-no-image-bg">
                        <img v-if="item.product.image" :src="item.product.image" :alt="item.product.name" class="w-full h-full">
                        <IconNoImage md v-if="!item.product.image"/>
                    </div>
                </td>
                <td class="text-left">
                    <div class="text-lg font-semibold">
                        <a :href="item.product.url">
                            {{ item.product.name }}
                        </a>
                    </div>
                    <div v-if="item.product.attributes">
                        <div v-for="attribute in item.product.attributes">
                            <span>{{ attribute.name }}</span>: <span>{{ attribute.value }}</span>
                        </div>
                    </div>
                </td>
                <td class="">
                    <div class="text-right">{{ item.price }}</div>
                </td>
                <td class="">
                    <div class="flex justify-center">
                        <AmountInput :value="item.quantity" @onChange="(val) => onQtyChange(item, val)"></AmountInput>
                    </div>
                </td>
                <td class="">
                    <div class="text-right text-accent text-lg font-semibold">
                        {{ item.totalPrice }}
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="bg-primary-bg p-4 sticky top-8 min-w-sm">
            <div class="text-xl border-b pb-2 mb-2">
                Podsumowanie
            </div>
            <div class="flex justify-between items-center mb-2">
                <div>Kwota:</div>
                <div>{{ cart.subtotal }}</div>
            </div>
            <div class="flex justify-between items-center mb-2">
                <div>Wysyłka od:</div>
                <div>{{ cart.shipping }}</div>
            </div>
            <div class="flex justify-between items-end">
                <div>Do zapłaty:</div>
                <div class="text-xl text-accent font-semibold">
                    {{ cart.total }}
                </div>
            </div>
            <Link :href="route(auth.isLoggedIn ? 'checkout.index' : 'checkout.login')" class="button-primary block mt-4" >
                Do kasy
            </Link>
        </div>
    </div>
</template>