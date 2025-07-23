<script setup>
import IconNoImage from "@shopen/components/icons/IconNoImage.vue";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const page = usePage();
const cart = computed(() => page.props.cart);

</script>

<template>
    <div>
        <div v-for="item in cart.items" :key="item.id" class="flex relative mb-2 pb-2 pr-2">
            <div class="mr-2 w-[50px] h-[50px] flex items-center justify-center text-no-image-icon bg-no-image-bg">
                <img v-if="item.product.image" :src="item.product.image" :alt="item.product.name" class="w-full h-full">
                <IconNoImage md v-if="!item.product.image"/>
            </div>
            <div class="w-full">
                <div class="font-semibold mb-2">
                    <a :href="item.product.url">
                        {{ item.product.name }}
                    </a>
                </div>

                <div v-if="item.product.attributes">
                    <div v-for="attribute in item.product.attributes" class="text-neutral-500">
                        <span>{{ attribute.name }}</span>: <span>{{ attribute.value }}</span>
                    </div>
                </div>
                <div class="flex justify-between">
                    <div class="flex items-center text-neutral-400">
                        {{ item.quantity }} szt.
                    </div>
                    <div class="flex items-end">
                        <div class="text-gray-400 text-sm line-through mr-2" v-if="item.total_price !== item.total_final_price">
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
</template>

<style scoped>

</style>