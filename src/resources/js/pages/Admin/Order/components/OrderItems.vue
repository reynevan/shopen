<script setup>
import ProductThumbnailImage from "@shopen/components/admin/product/ProductThumbnailImage.vue";

defineProps(['items']);
</script>

<template>
    <table class="w-full">
        <thead class="sticky top-0 bg-panel py-2">
        <tr>
            <th class="uppercase text-sm font-normal text-left pr-4 py-2 ">Produkt</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Cena</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Obniżka</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Cena po obniżce</th>
            <th class="uppercase text-sm font-normal w-[200px] px-4 py-2 text-right">Kod rabatowy</th>
            <th class="uppercase text-sm font-normal w-[100px] px-4 py-2 text-right">Ilość</th>
            <th class="uppercase text-sm font-normal w-[100px] px-4 py-2 text-right">Podatek</th>
            <th class="uppercase text-sm font-normal w-[150px] px-4 py-2 text-right">Kwota</th>
        </tr>
        <tr>
            <th class="h-[1px] bg-border-primary" colspan="8"></th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b hover:bg-accent/50 transition-colors" v-for="item in items">
            <td class="pr-4 py-2">
                <div class="flex items-start pl-4 gap-4">
                    <div>
                        <ProductThumbnailImage :product="item.product" size="sm"/>
                    </div>
                    <div>
                        <div>{{ item.name }}</div>
                        <div v-if="item.promo_code_coupons" class="flex items-center gap-2 text-sm">
                            <div>Kod rabatowy:</div>
                            <div v-for="coupon in item.promo_code_coupons" class="border px-2 bg-white">
                                {{ coupon.code }}
                            </div>
                        </div>
                        <div class="mt-1 text-neutral-500 text-sm">SKU: {{ item.sku }}</div>
                        <div class="text-neutral-500 text-sm" v-for="attribute in item.product.variant_attributes">
                            {{ attribute.name }}: {{ attribute.value }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-2 text-right">{{ item.price }}</td>
            <td class="px-4 py-2 text-right">{{ item.discount }}</td>
            <td class="px-4 py-2 text-right">{{ item.final_price }}</td>
            <td class="px-4 py-2 text-right">{{ item.promo_code_discount_amount }}</td>
            <td class="px-4 py-2 text-right">{{ item.quantity }}</td>
            <td class="px-4 py-2 text-right">{{ item.tax_amount }}</td>
            <td class="px-4 py-2 text-right">{{ item.total }}</td>
        </tr>
        </tbody>
    </table>
</template>