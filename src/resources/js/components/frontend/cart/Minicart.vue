<script setup>
import {useCartStore} from "@shopen/stores/cart.js";
import IconCart from "@shopen/components/icons/IconCart.vue";
import IconCartEmpty from "@shopen/components/icons/IconCartEmpty.vue";
import {useCoverStore} from "@shopen/stores/cover.js";
import AmountInput from "@shopen/components/frontend/input/AmountInput.vue";
import {debounce} from "vue-debounce";
import {useMiniCartStore} from "@shopen/stores/minicart.js";
import IconNoImage from "@shopen/components/icons/IconNoImage.vue";
import IconX from "@shopen/components/icons/IconX.vue";
import ProductThumbnailImage from "../product/ProductThumbnailImage.vue";

defineOptions({
    name: 'Minicart'
})

const cover = useCoverStore();
const minicart = useMiniCartStore();
const cart = useCartStore();

defineProps(['items', 'itemsCount', 'subtotal']);

cover.onClose(() => {
    minicart.isOpened = false;
})

const openMinicart = () => {
    minicart.open();
}

const closeMinicart = () => {
    minicart.close();
}

const removeItem = (item) => {
    cart.removeItem(item.id);
}

const updateItem = debounce((item, val) => {
    if (parseInt(val) === 0) {
        removeItem(item);
        return;
    }
    cart.updateItem(item.id, val);
}, 500)

</script>

<template>
    <div @click="openMinicart" class="cursor-pointer flex items-center">
        <div>
            <IconCart/>
        </div>
        <div class="bg-primary text-primary-text h-6 p-2 flex items-center justify-center" v-if="itemsCount">
            {{ itemsCount }}
        </div>
    </div>
    <Teleport to="body">
        <div
            class="minicart-panel flex flex-col fixed transition-[right] ease-in-out duration-500 w-screen sm:w-[400px] top-0 bottom-0 overflow-y-auto z-30"
            :class="{'right-0': minicart.isOpened, 'right-[calc(-100vw)] sm:right-[-401px]': !minicart.isOpened}"
            ref="minicart-element">
            <div class="flex items-center justify-between pl-4 mb-4">
                <div class="text-xl">Podgląd koszyka</div>
                <div @click="closeMinicart" class="mr-4 cursor-pointer hover:text-black transition-colors">
                    <IconX lg/>
                </div>
            </div>
            <div v-if="!items || items.length === 0" class="flex flex-col items-center">
                <div class="mt-10 text-neutral-200">
                    <IconCartEmpty xl/>
                </div>
                <div class="mb-10 mt-4 text-neutral-400 text-xl">
                    Koszyk jest pusty
                </div>
                <button class="" @click="closeMinicart">
                    Wróć do sklepu
                </button>
            </div>
            <div class="overflow-y-auto grow shadow">
                <div v-for="item in items" :key="item.id" class="flex minicart-item">
                    <div class="absolute top-2 right-2 cursor-pointer" @click="removeItem(item)">
                        <icon-x md/>
                    </div>
                    <ProductThumbnailImage :product="item.product"/>
                    <div class="grow ml-2">
                        <div class="item-title">
                            <a :href="item.product.url" class="hover:text-accent transition-colors">
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
                                              @onChange="(val) => { updateItem(item, val) }"></AmountInput>
                            </div>
                            <div>
                                <div v-if="item.total_final_price !== item.total_price"
                                     class="text-gray-400 line-through text-sm">
                                    {{ item.total_price }}
                                </div>
                                <div class="font-semibold">
                                    {{ item.total_final_price }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t py-2 px-4">
                <div class="flex justify-between items-center my-4 text-lg">
                    <div class="mr-2 uppercase">Razem:</div>
                    <div>{{ subtotal }}</div>
                </div>
                <a href="/koszyk" class="button-primary block mb-2">
                    Koszyk
                </a>
                <div class="text-center">
                    lub <a class="text-accent cursor-pointer" @click="closeMinicart">Kontynuuj zakupy →</a>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>

</style>