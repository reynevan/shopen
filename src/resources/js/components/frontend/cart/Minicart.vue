<script setup>
import {useCartStore} from "@shopen/stores/cart.js";
import IconCartEmpty from "@shopen/components/icons/IconCartEmpty.vue";
import AmountInput from "@shopen/components/frontend/input/AmountInput.vue";
import {debounce} from "vue-debounce";
import {useMiniCartStore} from "@shopen/stores/minicart.js";
import IconX from "@shopen/components/icons/IconX.vue";
import ProductThumbnailImage from "../product/ProductThumbnailImage.vue";
import {Link} from "@inertiajs/vue3";
import Button from "@shopen/components/frontend/ui/Button.vue";
import IconLoader from "../../icons/IconLoader.vue";
import ProductImage from "../product/ProductImage.vue";
import {trackRemoveFromCart} from "../../../utils/ga4";

defineOptions({
    name: 'Minicart'
})

const minicart = useMiniCartStore();
const cart = useCartStore();

defineProps(['items', 'subtotal']);


const closeMinicart = () => {
    minicart.close();
}

const removeItem = (item) => {
    trackRemoveFromCart(item, item.quantity);
    cart.removeItem(item.id);
}

const updateItem = debounce((item, val) => {
    if (parseInt(val) === 0) {
        removeItem(item);
        return;
    }
    item.loading = true;
    item.quantity = val;
    cart.updateItem(item.id, val);
}, 250)

</script>

<template>
    <div
        class="minicart-panel flex flex-col fixed transition-[right] ease-in-out duration-500 w-screen sm:w-[400px] top-0 bottom-0 overflow-y-auto z-30"
        :class="{'right-0': minicart.isOpened, 'right-[calc(-100vw)] sm:right-[-401px]': !minicart.isOpened}"
        ref="minicart-element">
        <div class="flex items-center justify-between pl-4 mb-2 py-4 shadow">
            <div class="text-xl">Podgląd koszyka</div>
            <div @click="closeMinicart"
                 title="Zamknij podgląd koszyka"
                 class="mr-4 cursor-pointer hover:text-black transition-colors">
                <IconX size="2xl"/>
            </div>
        </div>
        <div v-if="!items || items.length === 0" class="flex flex-col items-center px-6 ">
            <div class="mt-10">
                <IconCartEmpty size="8xl"/>
            </div>
            <div class="mb-10 mt-4 empty-cart-label">
                Koszyk jest pusty
            </div>
        </div>
        <div class="overflow-y-auto grow divide-y divide-light">
            <div v-for="item in items"
                 :key="item.id"
                 class="minicart-item flex items-start mx-4 py-6 relative px-6">
                <div class="absolute top-2 right-2 cursor-pointer minicart-item-remove-btn"
                     title="Usuń produkt z koszyka"
                     @click="removeItem(item)">
                    <icon-x md/>
                </div>
                <div class="w-[100px] product-image">
                    <ProductImage :alt="item.product.name" :urls="item.product.image" sizes="100px"
                                  :width="100"/>
                </div>
                <div class="grow ml-2">
                    <div class="item-title">
                        <a :href="item.product.url" class="hover:text-black transition-colors">
                            {{ item.product.name }}
                        </a>
                    </div>

                    <div v-if="item.product.attributes" class="text-neutral-500">
                        <div v-for="attribute in item.product.attributes">
                            <span>{{ attribute.name }}</span>: <span>{{ attribute.value }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="mr-1">
                            <AmountInput :value="item.quantity"
                                         :disabled="item.loading"
                                         @onChange="(val) => { updateItem(item, val) }"></AmountInput>
                        </div>
                        <div v-if="item.loading">
                            <IconLoader/>
                        </div>
                        <div v-if="!item.loading">
                            <div v-if="item.total_final_price !== item.total_price"
                                 class="old-price">
                                {{ item.total_price }}
                            </div>
                            <div class="final-price">
                                {{ item.total_final_price }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t pt-2 pb-6 px-6" v-if="items.length">
            <div class="flex justify-between items-center my-4 text-lg">
                <div class="mr-2">Razem:</div>
                <div>{{ subtotal }}</div>
            </div>
            <Link :href="route('cart.index')" @click="closeMinicart">
                <Button type="primary" size="lg" full-width>
                    Koszyk
                </Button>
            </Link>
            <div class="text-center mt-4">
                lub <a class="continue-shopping-link" @click.prevent="closeMinicart">
                Kontynuuj zakupy →
            </a>
            </div>
        </div>
        <div class="border-t pt-2 pb-6 px-6" v-if="!items || items.length === 0">
            <div class="text-center mt-4">
                <span class="continue-shopping-link" @click.prevent="closeMinicart">
                    Kontynuuj zakupy →
                </span>
            </div>
        </div>
    </div>
</template>