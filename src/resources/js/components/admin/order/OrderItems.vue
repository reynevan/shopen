<script setup>

defineProps(['items']);
</script>

<template>
    <div class="">
        <table class="w-full">
            <thead class="sticky top-0 bg-panel py-2">
            <tr>
                <th class="font-semibold text-left pr-4 py-2 ">Produkt</th>
                <th class="font-semibold w-[200px] px-4 py-2 text-right">Cena</th>
                <th class="font-semibold w-[200px] px-4 py-2 text-right">Obniżka</th>
                <th class="font-semibold w-[200px] px-4 py-2 text-right">Cena po obniżce</th>
                <th class="font-semibold w-[100px] px-4 py-2 text-right">Ilość</th>
                <th class="font-semibold w-[150px] px-4 py-2 text-right">Kwota</th>
            </tr>
            <tr>
                <th class="h-[1px] bg-border-primary" colspan="6"></th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-b" v-for="item in items">
                <td class="pr-4 py-2 ">
                    <div>{{ item.name }}</div>
                    <div class="mt-1 text-neutral-400 text-sm">SKU: {{ item.sku }}</div>
                    <div class="text-neutral-600 text-sm" v-for="attribute in item.product.variant_attributes">
                        {{ attribute.name }}: {{ attribute.value }}</div>
                </td>
                <td class="px-4 py-2 text-right">{{ item.price }}</td>
                <td class="px-4 py-2 text-right">{{ item.discount }}</td>
                <td class="px-4 py-2 text-right">{{ item.final_price }}</td>
                <td class="px-4 py-2 text-right">{{ item.quantity }}</td>
                <td class="px-4 py-2 text-right">{{ item.total }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>